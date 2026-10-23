<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'GET') {
    $search = trim((string)($_GET['q'] ?? ''));
    if ($search !== '') {
        $term = '%' . $search . '%';
        $stmt = $pdo->prepare(
            'SELECT e.event_id,e.title,e.description,e.event_date,e.venue,e.capacity,
             (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.event_id AND r.status <> "cancelled") AS registration_count
             FROM events e WHERE e.title LIKE :title OR e.venue LIKE :venue OR e.description LIKE :description
             ORDER BY e.event_date DESC LIMIT 200'
        );
        $stmt->execute(['title'=>$term,'venue'=>$term,'description'=>$term]);
    } else {
        $stmt = $pdo->query(
            'SELECT e.event_id,e.title,e.description,e.event_date,e.venue,e.capacity,
             (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.event_id AND r.status <> "cancelled") AS registration_count
             FROM events e ORDER BY e.event_date DESC LIMIT 200'
        );
    }
    api_json(200,['success'=>true,'data'=>$stmt->fetchAll()]);
}
if ($method === 'POST') {
    api_require_csrf();
    $data = api_read_json();
    $title = trim((string)($data['title']??''));
    $description = trim((string)($data['description']??''));
    $eventDate = trim((string)($data['event_date']??''));
    $venue = trim((string)($data['venue']??''));
    $capacity = $data['capacity']??null;
    $date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i',$eventDate);
    if ($title==='' || mb_strlen($title)>150) api_json(422,['success'=>false,'message'=>'Title is required (max 150 characters).']);
    if ($description==='' || mb_strlen($description)>5000) api_json(422,['success'=>false,'message'=>'Description is required (max 5000 characters).']);
    if (!$date || $date->format('Y-m-d\TH:i')!==$eventDate) api_json(422,['success'=>false,'message'=>'Use a valid event date and time.']);
    if ($venue==='' || mb_strlen($venue)>200) api_json(422,['success'=>false,'message'=>'Venue is required (max 200 characters).']);
    if (!is_numeric($capacity) || (int)$capacity<1 || (int)$capacity>100000) api_json(422,['success'=>false,'message'=>'Capacity must be between 1 and 100000.']);
    $stmt=$pdo->prepare('INSERT INTO events(title,description,event_date,venue,capacity) VALUES(:title,:description,:date,:venue,:capacity)');
    $stmt->execute(['title'=>$title,'description'=>$description,'date'=>$date->format('Y-m-d H:i:s'),'venue'=>$venue,'capacity'=>(int)$capacity]);
    api_json(201,['success'=>true,'message'=>'Event created successfully.','data'=>['event_id'=>(int)$pdo->lastInsertId()]]);
}
if ($method === 'PUT') {
    api_require_csrf();
    $data=api_read_json();
    $id=filter_var($data['event_id']??null,FILTER_VALIDATE_INT);
    if (!$id || $id<1) api_json(422,['success'=>false,'message'=>'A valid event_id is required.']);
    $get=$pdo->prepare('SELECT event_id FROM events WHERE event_id=:id');
    $get->execute(['id'=>$id]);
    if (!$get->fetch()) api_json(404,['success'=>false,'message'=>'Event not found.']);
    $allowed=['title','description','event_date','venue','capacity'];
    $fields=[];$params=['id'=>$id];
    foreach($allowed as $field){
        if(!array_key_exists($field,$data)) continue;
        $value=$data[$field];
        if($field==='title'){
            $value=trim((string)$value);
            if($value===''||mb_strlen($value)>150) api_json(422,['success'=>false,'message'=>'Invalid title.']);
        }elseif($field==='description'){
            $value=trim((string)$value);
            if($value===''||mb_strlen($value)>5000) api_json(422,['success'=>false,'message'=>'Invalid description.']);
        }elseif($field==='event_date'){
            $value=(string)$value;
            $dt=DateTimeImmutable::createFromFormat('Y-m-d\TH:i',$value);
            if(!$dt||$dt->format('Y-m-d\TH:i')!==$value) api_json(422,['success'=>false,'message'=>'Use a valid event date and time.']);
            $value=$dt->format('Y-m-d H:i:s');
        }elseif($field==='venue'){
            $value=trim((string)$value);
            if($value===''||mb_strlen($value)>200) api_json(422,['success'=>false,'message'=>'Invalid venue.']);
        }elseif($field==='capacity'){
            if(!is_numeric($value)||(int)$value<1||(int)$value>100000) api_json(422,['success'=>false,'message'=>'Capacity must be between 1 and 100000.']);
            $count=$pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id=:id AND status <> 'cancelled'");
            $count->execute(['id'=>$id]);
            if((int)$value<(int)$count->fetchColumn()) api_json(409,['success'=>false,'message'=>'Capacity cannot be lower than active registrations.']);
            $value=(int)$value;
        }
        $fields[]=$field.'=:'.$field;$params[$field]=$value;
    }
    if(!$fields) api_json(422,['success'=>false,'message'=>'Provide at least one editable field.']);
    $stmt=$pdo->prepare('UPDATE events SET '.implode(', ',$fields).' WHERE event_id=:id');
    $stmt->execute($params);
    api_json(200,['success'=>true,'message'=>'Event updated successfully.']);
}
if ($method === 'DELETE') {
    api_require_csrf();
    $data=api_read_json();
    $id=filter_var($data['event_id']??null,FILTER_VALIDATE_INT);
    if(!$id||$id<1) api_json(422,['success'=>false,'message'=>'A valid event_id is required.']);
    $count=$pdo->prepare('SELECT COUNT(*) FROM registrations WHERE event_id=:id');
    $count->execute(['id'=>$id]);
    if((int)$count->fetchColumn()>0) api_json(409,['success'=>false,'message'=>'Cannot delete an event that has registrations.']);
    $stmt=$pdo->prepare('DELETE FROM events WHERE event_id=:id');
    $stmt->execute(['id'=>$id]);
    if($stmt->rowCount()===0) api_json(404,['success'=>false,'message'=>'Event not found.']);
    api_json(200,['success'=>true,'message'=>'Event deleted successfully.']);
}
header('Allow: GET, POST, PUT, DELETE');
api_json(405,['success'=>false,'message'=>'Method not allowed.']);
