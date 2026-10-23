<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';
studenthub_require_role($pdo, 'admin');
if (empty($_SESSION['api_csrf'])) $_SESSION['api_csrf'] = bin2hex(random_bytes(32));
$csrf = htmlspecialchars($_SESSION['api_csrf'], ENT_QUOTES, 'UTF-8');
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Practical 13 - AJAX/Fetch API CRUD</title><link rel="stylesheet" href="css/style.css">
<style>
.api-wrap{max-width:1200px;margin:2rem auto;padding:0 1rem}.panel{background:var(--card-bg,#fff);border:1px solid #ddd;border-radius:12px;padding:1.25rem;margin:1rem 0}
.toolbar{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between}.filters{display:flex;gap:.5rem;flex-wrap:wrap}.filters input,.filters select,.field input,.field textarea,.field select{padding:.7rem;border:1px solid #aaa;border-radius:8px;box-sizing:border-box}
.table-wrap{overflow-x:auto}table{border-collapse:collapse;width:100%;min-width:850px}th,td{text-align:left;padding:.7rem;border-bottom:1px solid #ddd;vertical-align:top}th{background:#f2f5fa}.actions{display:flex;gap:.4rem;flex-wrap:wrap}.small-btn{padding:.4rem .6rem;border:1px solid #aaa;border-radius:6px;background:transparent;cursor:pointer}.danger{color:#a11;border-color:#a11}.status{padding:.8rem 1rem;border-radius:8px;background:#edf4ff;margin:1rem 0}.status.error{background:#fff0f0;color:#8a1f1f}.status.ok{background:#e8f7e8;color:#175c2c}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:1rem}.field label{display:block;margin-bottom:.4rem}.field input,.field textarea,.field select{width:100%}.field textarea{min-height:90px}.hidden{display:none!important}.note{opacity:.75;font-size:.92rem}
</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="admin-dashboard.php">Dashboard</a><a href="admin/students.php">Manage Students</a><a href="admin/events.php">Manage Events</a><a href="db-check.php">Database Check</a><a href="logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Practical 13: AJAX / Fetch API CRUD</h1><p>Manage students and events without a full page reload.</p></div></section>
<div class="api-wrap">
<div id="status" class="status" role="status">Loading data…</div>
<section class="panel"><div class="toolbar"><div><h2>Student Records</h2><p class="note">Admin-only JSON API · maximum 200 records per request</p></div>
<div class="filters"><input id="studentSearch" type="search" placeholder="Search name, email or course" aria-label="Search students"><button class="btn btn-primary" id="studentSearchBtn" type="button">Search</button><button class="small-btn" id="studentRefreshBtn" type="button">Refresh</button></div></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Role</th><th>Actions</th></tr></thead><tbody id="studentsBody"><tr><td colspan="7">Loading…</td></tr></tbody></table></div>
<details><summary>Add student with Fetch API</summary><form id="studentForm"><div class="grid">
<div class="field"><label for="sName">Full name</label><input id="sName" required minlength="3" maxlength="100"></div>
<div class="field"><label for="sEmail">Email</label><input id="sEmail" type="email" required maxlength="150"></div>
<div class="field"><label for="sMobile">Mobile (optional)</label><input id="sMobile" pattern="[0-9]{10,15}"></div>
<div class="field"><label for="sCourse">Course</label><select id="sCourse" required><option value="">Select course</option><option>B.Tech Computer Science</option><option>B.Tech Information Technology</option><option>B.Tech AI &amp; Machine Learning</option><option>B.Tech Computer Engineering</option><option>Diploma Information Technology</option><option>Other</option></select></div>
<div class="field"><label for="sYear">Study year</label><select id="sYear" required><option value="">Select year</option><option value="1">Year 1</option><option value="2">Year 2</option><option value="3">Year 3</option><option value="4">Year 4</option><option value="5">Year 5</option><option value="6">Year 6</option></select></div>
<div class="field"><label for="sPassword">Temporary password</label><input id="sPassword" type="password" required autocomplete="new-password" minlength="8"></div>
</div><p><button class="btn btn-primary" type="submit">Create Student</button></p></form></details>
</section>
<section class="panel"><div class="toolbar"><div><h2>Event Records</h2><p class="note">Event CRUD uses the same JSON API pattern.</p></div>
<div class="filters"><input id="eventSearch" type="search" placeholder="Search title or venue" aria-label="Search events"><button class="btn btn-primary" id="eventSearchBtn" type="button">Search</button><button class="small-btn" id="eventRefreshBtn" type="button">Refresh</button></div></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Title</th><th>Date</th><th>Venue</th><th>Capacity</th><th>Registrations</th><th>Actions</th></tr></thead><tbody id="eventsBody"><tr><td colspan="7">Loading…</td></tr></tbody></table></div>
<details><summary>Add event with Fetch API</summary><form id="eventForm"><div class="grid">
<div class="field"><label for="eTitle">Title</label><input id="eTitle" required maxlength="150"></div>
<div class="field"><label for="eDate">Date and time</label><input id="eDate" type="datetime-local" required></div>
<div class="field"><label for="eVenue">Venue</label><input id="eVenue" required maxlength="200"></div>
<div class="field"><label for="eCapacity">Capacity</label><input id="eCapacity" type="number" min="1" max="100000" value="50" required></div>
<div class="field" style="grid-column:1/-1"><label for="eDescription">Description</label><textarea id="eDescription" required maxlength="5000"></textarea></div>
</div><p><button class="btn btn-primary" type="submit">Create Event</button></p></form></details>
</section>
<p class="note">Edit uses a small prompt for this practical demo. For full form-based editing, use the regular Manage Students and Manage Events pages. Do not use this page on a public production site without HTTPS and additional hardening.</p>
</div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer>
<script src="js/script.js"></script>
<script>
'use strict';
const API_CSRF = '<?= $csrf ?>';
const statusEl = document.getElementById('status');
const esc = (value) => String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
function showStatus(message, kind='') { statusEl.textContent = message; statusEl.className = 'status' + (kind ? ' '+kind : ''); }
async function requestApi(url, options={}) {
    const headers = {'Accept':'application/json', ...(options.headers||{})};
    if (options.body) { headers['Content-Type']='application/json'; headers['X-CSRF-Token']=API_CSRF; }
    const response = await fetch(url, {...options, headers, credentials:'same-origin'});
    let result;
    try { result = await response.json(); } catch { throw new Error('Server returned a non-JSON response. Check login/session and PHP errors.'); }
    if (!response.ok || !result.success) throw new Error(result.message || 'Request failed.');
    return result;
}
async function loadStudents() {
    const q=document.getElementById('studentSearch').value.trim();
    const body=document.getElementById('studentsBody'); body.innerHTML='<tr><td colspan="7">Loading…</td></tr>';
    try {
        const result=await requestApi('api/students.php'+(q?'?q='+encodeURIComponent(q):''));
        body.innerHTML=result.data.length?result.data.map(s=>`<tr><td>${Number(s.student_id)}</td><td>${esc(s.full_name)}</td><td>${esc(s.email)}</td><td>${esc(s.course)}</td><td>${Number(s.study_year)}</td><td>${esc(s.role)}</td><td><div class="actions"><button class="small-btn" type="button" data-student-edit="${Number(s.student_id)}" data-name="${esc(s.full_name)}" data-email="${esc(s.email)}" data-course="${esc(s.course)}" data-year="${Number(s.study_year)}" data-role="${esc(s.role)}">Edit</button>${Number(s.student_id)!==CURRENT_ADMIN_ID?`<button class="small-btn danger" type="button" data-student-delete="${Number(s.student_id)}">Delete</button>`:''}</div></td></tr>`).join(''):'<tr><td colspan="7">No students found.</td></tr>';
        showStatus('Student records loaded successfully.','ok');
    } catch(e) { body.innerHTML='<tr><td colspan="7">Could not load records.</td></tr>'; showStatus(e.message,'error'); }
}
async function loadEvents() {
    const q=document.getElementById('eventSearch').value.trim();
    const body=document.getElementById('eventsBody'); body.innerHTML='<tr><td colspan="7">Loading…</td></tr>';
    try {
        const result=await requestApi('api/events.php'+(q?'?q='+encodeURIComponent(q):''));
        body.innerHTML=result.data.length?result.data.map(e=>`<tr><td>${Number(e.event_id)}</td><td>${esc(e.title)}</td><td>${esc(e.event_date)}</td><td>${esc(e.venue)}</td><td>${Number(e.capacity)}</td><td>${Number(e.registration_count)}</td><td><div class="actions"><button class="small-btn" type="button" data-event-edit="${Number(e.event_id)}" data-title="${esc(e.title)}" data-description="${esc(e.description)}" data-date="${esc(String(e.event_date).replace(' ','T').slice(0,16))}" data-venue="${esc(e.venue)}" data-capacity="${Number(e.capacity)}">Edit</button><button class="small-btn danger" type="button" data-event-delete="${Number(e.event_id)}">Delete</button></div></td></tr>`).join(''):'<tr><td colspan="7">No events found.</td></tr>';
        showStatus('Event records loaded successfully.','ok');
    } catch(e) { body.innerHTML='<tr><td colspan="7">Could not load records.</td></tr>'; showStatus(e.message,'error'); }
}
document.getElementById('studentSearchBtn').addEventListener('click',loadStudents);
document.getElementById('studentRefreshBtn').addEventListener('click',()=>{document.getElementById('studentSearch').value='';loadStudents();});
document.getElementById('eventSearchBtn').addEventListener('click',loadEvents);
document.getElementById('eventRefreshBtn').addEventListener('click',()=>{document.getElementById('eventSearch').value='';loadEvents();});
document.getElementById('studentForm').addEventListener('submit',async ev=>{
    ev.preventDefault();
    const data={full_name:document.getElementById('sName').value.trim(),email:document.getElementById('sEmail').value.trim(),mobile:document.getElementById('sMobile').value.trim(),course:document.getElementById('sCourse').value,study_year:Number(document.getElementById('sYear').value),password:document.getElementById('sPassword').value};
    try { const r=await requestApi('api/students.php',{method:'POST',body:JSON.stringify(data)}); ev.target.reset();showStatus(r.message,'ok');await loadStudents(); }
    catch(e){showStatus(e.message,'error');}
});
document.getElementById('eventForm').addEventListener('submit',async ev=>{
    ev.preventDefault();
    const data={title:document.getElementById('eTitle').value.trim(),description:document.getElementById('eDescription').value.trim(),event_date:document.getElementById('eDate').value,venue:document.getElementById('eVenue').value.trim(),capacity:Number(document.getElementById('eCapacity').value)};
    try {const r=await requestApi('api/events.php',{method:'POST',body:JSON.stringify(data)});ev.target.reset();document.getElementById('eCapacity').value='50';showStatus(r.message,'ok');await loadEvents();}
    catch(e){showStatus(e.message,'error');}
});
document.addEventListener('click',async ev=>{
    const t=ev.target;
    if(t.matches('[data-student-delete]')){
        const id=Number(t.dataset.studentDelete);if(!confirm('Delete this student record?'))return;
        try{const r=await requestApi('api/students.php',{method:'DELETE',body:JSON.stringify({student_id:id})});showStatus(r.message,'ok');await loadStudents();}
        catch(e){showStatus(e.message,'error');}
    }
    if(t.matches('[data-student-edit]')){
        const id=Number(t.dataset.studentEdit);
        const name=prompt('Full name:',t.dataset.name);if(name===null)return;
        const email=prompt('Email:',t.dataset.email);if(email===null)return;
        const course=prompt('Course (must match a valid course option):',t.dataset.course);if(course===null)return;
        const year=prompt('Study year (1–6):',t.dataset.year);if(year===null)return;
        const role=prompt('Role (student/admin):',t.dataset.role);if(role===null)return;
        try{const r=await requestApi('api/students.php',{method:'PUT',body:JSON.stringify({student_id:id,full_name:name,email,course,study_year:Number(year),role})});showStatus(r.message,'ok');await loadStudents();}
        catch(e){showStatus(e.message,'error');}
    }
    if(t.matches('[data-event-delete]')){
        const id=Number(t.dataset.eventDelete);if(!confirm('Delete this event? Events with registrations cannot be deleted.'))return;
        try{const r=await requestApi('api/events.php',{method:'DELETE',body:JSON.stringify({event_id:id})});showStatus(r.message,'ok');await loadEvents();}
        catch(e){showStatus(e.message,'error');}
    }
    if(t.matches('[data-event-edit]')){
        const id=Number(t.dataset.eventEdit);
        const title=prompt('Event title:',t.dataset.title);if(title===null)return;
        const description=prompt('Description:',t.dataset.description);if(description===null)return;
        const event_date=prompt('Date/time (YYYY-MM-DDTHH:MM):',t.dataset.date);if(event_date===null)return;
        const venue=prompt('Venue:',t.dataset.venue);if(venue===null)return;
        const capacity=prompt('Capacity:',t.dataset.capacity);if(capacity===null)return;
        try{const r=await requestApi('api/events.php',{method:'PUT',body:JSON.stringify({event_id:id,title,description,event_date,venue,capacity:Number(capacity)})});showStatus(r.message,'ok');await loadEvents();}
        catch(e){showStatus(e.message,'error');}
    }
});
const CURRENT_ADMIN_ID = Number(<?= (int)$_SESSION['student_id'] ?>);
loadStudents();loadEvents();
</script></body></html>
