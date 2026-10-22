<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $search = trim((string)($_GET['q'] ?? ''));
    if ($search !== '') {
        $term = '%' . $search . '%';
        $stmt = $pdo->prepare(
            'SELECT student_id, full_name, email, mobile, course, study_year, role, created_at
             FROM students
             WHERE full_name LIKE :name OR email LIKE :email OR course LIKE :course
             ORDER BY student_id DESC LIMIT 200'
        );
        $stmt->execute(['name'=>$term, 'email'=>$term, 'course'=>$term]);
    } else {
        $stmt = $pdo->query(
            'SELECT student_id, full_name, email, mobile, course, study_year, role, created_at
             FROM students ORDER BY student_id DESC LIMIT 200'
        );
    }
    api_json(200, ['success'=>true, 'data'=>$stmt->fetchAll()]);
}

if ($method === 'POST') {
    api_require_csrf();
    $data = api_read_json();
    $name = trim((string)($data['full_name'] ?? ''));
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $mobile = trim((string)($data['mobile'] ?? ''));
    $course = trim((string)($data['course'] ?? ''));
    $year = $data['study_year'] ?? null;
    $password = (string)($data['password'] ?? '');

    $courses = ['B.Tech Computer Science', 'B.Tech Information Technology', 'B.Tech AI & Machine Learning',
                'B.Tech Computer Engineering', 'Diploma Information Technology', 'Other'];
    if (mb_strlen($name) < 3 || mb_strlen($name) > 100) api_json(422, ['success'=>false,'message'=>'Name must be 3–100 characters.']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) api_json(422, ['success'=>false,'message'=>'Enter a valid email address.']);
    if ($mobile !== '' && !preg_match('/^[0-9]{10,15}$/', $mobile)) api_json(422, ['success'=>false,'message'=>'Mobile must contain 10–15 digits.']);
    if (!in_array($course, $courses, true)) api_json(422, ['success'=>false,'message'=>'Select a valid course.']);
    if (!is_numeric($year) || (int)$year < 1 || (int)$year > 6) api_json(422, ['success'=>false,'message'=>'Study year must be between 1 and 6.']);
    if (strlen($password) < 8 || !preg_match('/[A-Z]/',$password) || !preg_match('/[a-z]/',$password) || !preg_match('/[0-9]/',$password) || !preg_match('/[^A-Za-z0-9]/',$password)) {
        api_json(422, ['success'=>false,'message'=>'Password must be 8+ characters with uppercase, lowercase, number and special character.']);
    }
    $check = $pdo->prepare('SELECT student_id FROM students WHERE email=:email LIMIT 1');
    $check->execute(['email'=>$email]);
    if ($check->fetch()) api_json(409, ['success'=>false,'message'=>'This email is already registered.']);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO students (full_name,email,mobile,course,study_year,password_hash,role)
             VALUES (:name,:email,:mobile,:course,:year,:password_hash,'student')"
        );
        $stmt->execute([
            'name'=>$name, 'email'=>$email, 'mobile'=>$mobile !== '' ? $mobile : null,
            'course'=>$course, 'year'=>(int)$year, 'password_hash'=>password_hash($password, PASSWORD_DEFAULT)
        ]);
        $id = (int)$pdo->lastInsertId();
        api_json(201, ['success'=>true,'message'=>'Student created successfully.','data'=>['student_id'=>$id]]);
    } catch (PDOException $e) {
        error_log('StudentHub API create student: '.$e->getMessage());
        api_json(500, ['success'=>false,'message'=>'Could not create student.']);
    }
}

if ($method === 'PUT') {
    api_require_csrf();
    $data = api_read_json();
    $id = filter_var($data['student_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) api_json(422, ['success'=>false,'message'=>'A valid student_id is required.']);
    if ($id === (int)$_SESSION['student_id'] && isset($data['role']) && $data['role'] !== 'admin') {
        api_json(422, ['success'=>false,'message'=>'You cannot change your own Admin role.']);
    }
    $get = $pdo->prepare('SELECT student_id FROM students WHERE student_id=:id');
    $get->execute(['id'=>$id]);
    if (!$get->fetch()) api_json(404, ['success'=>false,'message'=>'Student not found.']);

    $fields = [];
    $params = ['id'=>$id];
    $allowed = ['full_name','email','mobile','course','study_year','role'];
    foreach ($allowed as $field) {
        if (!array_key_exists($field, $data)) continue;
        $value = $data[$field];
        if ($field === 'full_name') {
            $value = trim((string)$value);
            if (mb_strlen($value)<3 || mb_strlen($value)>100) api_json(422,['success'=>false,'message'=>'Name must be 3–100 characters.']);
        } elseif ($field === 'email') {
            $value = strtolower(trim((string)$value));
            if (!filter_var($value,FILTER_VALIDATE_EMAIL) || strlen($value)>150) api_json(422,['success'=>false,'message'=>'Enter a valid email.']);
        } elseif ($field === 'mobile') {
            $value = trim((string)$value);
            if ($value !== '' && !preg_match('/^[0-9]{10,15}$/',$value)) api_json(422,['success'=>false,'message'=>'Mobile must contain 10–15 digits.']);
            if ($value === '') $value = null;
        } elseif ($field === 'course') {
            $value = (string)$value;
            if (!in_array($value,['B.Tech Computer Science','B.Tech Information Technology','B.Tech AI & Machine Learning','B.Tech Computer Engineering','Diploma Information Technology','Other'],true)) api_json(422,['success'=>false,'message'=>'Invalid course.']);
        } elseif ($field === 'study_year') {
            if (!is_numeric($value) || (int)$value<1 || (int)$value>6) api_json(422,['success'=>false,'message'=>'Study year must be between 1 and 6.']);
            $value = (int)$value;
        } elseif ($field === 'role') {
            $value = (string)$value;
            if (!in_array($value,['admin','student'],true)) api_json(422,['success'=>false,'message'=>'Invalid role.']);
        }
        $fields[] = $field . '=:' . $field;
        $params[$field] = $value;
    }
    if (!$fields) api_json(422,['success'=>false,'message'=>'Provide at least one editable field.']);

    if (isset($params['email'])) {
        $dupe = $pdo->prepare('SELECT student_id FROM students WHERE email=:email AND student_id<>:id LIMIT 1');
        $dupe->execute(['email'=>$params['email'],'id'=>$id]);
        if ($dupe->fetch()) api_json(409,['success'=>false,'message'=>'Another account already uses this email.']);
    }
    try {
        $sql = 'UPDATE students SET '.implode(', ',$fields).' WHERE student_id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        api_json(200,['success'=>true,'message'=>'Student updated successfully.']);
    } catch (PDOException $e) {
        error_log('StudentHub API update student: '.$e->getMessage());
        api_json(500,['success'=>false,'message'=>'Could not update student.']);
    }
}

if ($method === 'DELETE') {
    api_require_csrf();
    $data = api_read_json();
    $id = filter_var($data['student_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) api_json(422,['success'=>false,'message'=>'A valid student_id is required.']);
    if ($id === (int)$_SESSION['student_id']) api_json(403,['success'=>false,'message'=>'You cannot delete your own Admin account.']);
    $stmt = $pdo->prepare('DELETE FROM students WHERE student_id=:id');
    try {
        $stmt->execute(['id'=>$id]);
        if ($stmt->rowCount()===0) api_json(404,['success'=>false,'message'=>'Student not found.']);
        api_json(200,['success'=>true,'message'=>'Student deleted successfully.']);
    } catch (PDOException $e) {
        error_log('StudentHub API delete student: '.$e->getMessage());
        api_json(409,['success'=>false,'message'=>'Could not delete student. The record may be linked to registrations or tokens.']);
    }
}

header('Allow: GET, POST, PUT, DELETE');
api_json(405,['success'=>false,'message'=>'Method not allowed.']);
