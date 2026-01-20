<?php
session_start();
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!==true){
    header("Location: login.php"); exit();
}

$csrf_token = $_SESSION['csrf_token'];
$user_name = htmlspecialchars($_SESSION['user_name']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة التحكم - <?php echo $user_name; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg-dark:#0f172a;
  --card-dark:#1e293b;
  --text:#e5e7eb;
  --blue:#2563eb;
  --blue2:#0ea5e9;
  --danger:#ef4444;
  --success:#22c55e;
}
*{box-sizing:border-box;font-family:'Cairo',sans-serif;margin:0;padding:0; transition:0.3s;}
body{display:flex;background:var(--bg-dark);color:var(--text);min-height:100vh;}
.sidebar{width:250px;background:var(--card-dark);padding:20px;display:flex;flex-direction:column;gap:20px}
.sidebar h2{text-align:center;margin-bottom:20px;color:var(--blue2)}
.sidebar button{background:none;border:none;color:var(--text);text-align:right;padding:12px;font-size:16px;cursor:pointer;border-radius:8px;transition:0.2s}
.sidebar button:hover{background:var(--blue);color:#fff}
.main{flex:1;padding:30px;display:flex;flex-direction:column;gap:30px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:50px}
.card{background:var(--card-dark);padding:25px;background: linear-gradient(145deg,#1e293b , #4c5c74ff);border-radius:15px;box-shadow:0 8px 20px rgba(0,0,0,5);text-align:center}
.card h3{margin-bottom:19px}
.card p{font-size:18px;font-weight:bold}
.card:hover{transform:translatey(-6px);box-shadow:0 18px 40px rgba(0,0,0,5);}
#taskForm{background:var(--card-dark);padding:30px;border-radius:15px;max-width:500px;margin:auto;display:flex;flex-direction:column;gap:15px}
#taskForm input,#taskForm textarea{padding:12px;border-radius:8px;border:1px solid #555;background:#1e293b;color:#fff}
#taskForm button{background:var(--blue);color:#fff;padding:12px;font-weight:bold;cursor:pointer;border:none;border-radius:10px;transition:0.2s}
#taskForm button:hover{background:var(--blue2)}
#taskForm input[type=file]{border:none;padding:5px}
.task-item{background:var(--card-dark);padding:20px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap}
.task-info{flex:1}
.task-info a{color:var(--blue2);text-decoration:underline;font-size:14px}
.task-info img, .task-info iframe{margin-top:5px; max-width:150px; max-height:200px; display:block;}
.task-actions button{margin-left:10px;padding:12px 15px;
border:none;border-radius:12px;
font-weight:bold;cursor:pointer;
transition:0.2s}
.task-actions .edit{background:var(--blue)}
.task-actions .delete{background:var(--danger)}
.task-actions button:hover{opacity:0.9}
/* زر التحميل */
.download-btn {
  display: inline-block;
  padding: 10px 20px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  color: #4f5a8fff;
  font-weight: bold;
  border: none;
  border-radius: 10px;
  text-decoration: none;
  cursor: pointer;
  transition: 0.3s;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.download-btn:hover {
  background: linear-gradient(135deg, #2563eb, #1e90ff);
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}

.download-btn:active {
  transform: translateY(0);
  box-shadow: 0 4px 10px rgba(138, 122, 208, 0.2);
}
h1{font-family:'Roboto',sans-serif;font-size:28px;font-weight:bold;margin-bottom:90px;}
.toast{position:fixed;bottom:20px;right:20px;background:var(--blue);color:#fff;padding:15px 25px;border-radius:12px;display:none;z-index:1000}
</style>
</head>
<body>

<div class="sidebar">
  <h2>لوحة التحكم</h2>
  <button onclick="showPage('welcome')">🏠 الترحيب</button>
  <button onclick="showPage('create')">➕ إنشاء مهمة</button>
  <button onclick="showPage('tasks')">📋 عرض المهام</button>
  <button onclick="logout()">🚪 تسجيل الخروج</button>
</div>


<div class="main">
  
  <div id="welcome" class="page">
    <h1>مرحباً <?php echo $user_name; ?> ⚜</h1>
       
    <div class="cards">
      <div class="card">
        <h3>إجمالي المهام</h3>
        <p id="totalTasks">0</p>
      </div>
      <div class="card">
        <h3>المهام المكتملة</h3>
        <p id="doneTasks">0</p>
      </div>
      <div class="card">
        <h3>المهام المعلقة</h3>
        <p id="pendingTasks">0</p>
      </div>
    </div>
  </div>


  <div id="create" class="page" style="display:none">
    <form id="taskForm" enctype="multipart/form-data">
      <input type="text" id="taskTitle" placeholder="عنوان المهمة" required>
      <textarea id="taskDesc" placeholder="وصف المهمة"></textarea>
      <input type="file" id="taskFile" accept=".pdf,.doc,.docx,.jpg,.png" >
      
      <button type="button" onclick="createTask()">حفظ المهمة</button>
    </form>
  </div>


  <div id="tasks" class="page" style="display:none">
    <div id="tasksList"></div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const csrf_token = '<?php echo $csrf_token; ?>';

function showPage(id){
  document.querySelectorAll('.page').forEach(p=>p.style.display='none');
  document.getElementById(id).style.display='block';
  if(id==='welcome') loadStats();
  if(id==='tasks') loadTasks();
}

function toast(msg){ const t=document.getElementById('toast'); t.innerText=msg; t.style.display='block'; setTimeout(()=>t.style.display='none',3000); }

function createTask(){
  const title=document.getElementById('taskTitle').value.trim();
  const desc=document.getElementById('taskDesc').value.trim();
  const fileInput=document.getElementById('taskFile');
  const file=fileInput.files[0];
  if(!title){ toast('العنوان مطلوب'); return; }

  const f=new FormData();
  f.append('action','create_task');
  f.append('title',title);
  f.append('description',desc);
  f.append('csrf_token',csrf_token);
  if(file) f.append('file',file);

  fetch('backend.php',{method:'POST',body:f}).then(r=>r.json()).then(d=>{
    toast(d.message);
    if(d.success){
      document.getElementById('taskTitle').value='';
      document.getElementById('taskDesc').value='';
      fileInput.value='';
      loadStats(); loadTasks();
    }
  });
}

function loadTasks(){
  const f=new FormData();
  f.append('action','read_tasks');
  f.append('csrf_token',csrf_token);
  fetch('backend.php',{method:'POST',body:f}).then(r=>r.json()).then(d=>{
    const list=document.getElementById('tasksList');
    list.innerHTML='';
    if(d.tasks){
      d.tasks.forEach(t=>{
        const div=document.createElement('div');
        div.className='task-item';
        let fileHTML = '';
        if(t.file){
          const ext = t.file.split('.').pop().toLowerCase();
          if(['jpg','jpeg','png','gif'].includes(ext)){
            fileHTML = `<img src="uploads/${t.file}" alt="ملف المهمة">`;
          } else if(ext==='pdf'){
            fileHTML = `<iframe src="uploads/${t.file}" width="300" height="200"></iframe>`;
          } else {
            fileHTML = `<a href="uploads/${t.file}" target="_blank">تحميل الملف</a>`;
          }
        }
        div.innerHTML=`
          <div class="task-info">
            <strong>${t.title}</strong><br>
            <small>${t.description||''}</small><br>
            ${fileHTML}
          </div>
          <div class="task-actions">
            <button class="edit" onclick="updateTask(${t.id},'${t.title}','${t.description}')">تعديل</button>
            <button class="delete" onclick="deleteTask(${t.id})">حذف</button>
          </div>
        `;
        list.appendChild(div);
      });
    }
  });
}

function updateTask(id,title,desc){
  const newTitle=prompt('أدخل العنوان الجديد:',title);
  const newDesc=prompt('أدخل الوصف الجديد:',desc);
  if(!newTitle) return;

  const f=new FormData();
  f.append('action','update_task');
  f.append('task_id',id);
  f.append('title',newTitle);
  f.append('description',newDesc);
  f.append('csrf_token',csrf_token);
  fetch('backend.php',{method:'POST',body:f}).then(r=>r.json()).then(d=>{
    toast(d.message);
    loadTasks(); loadStats();
  });
}

function deleteTask(id){
  if(!confirm('هل أنت متأكد من حذف المهمة؟')) return;
  const f=new FormData();
  f.append('action','delete_task');
  f.append('task_id',id);
  f.append('csrf_token',csrf_token);
  fetch('backend.php',{method:'POST',body:f}).then(r=>r.json()).then(d=>{
    toast(d.message);
    loadTasks(); loadStats();
  });
}

function loadStats(){
  const f=new FormData();
  f.append('action','read_tasks');
  f.append('csrf_token',csrf_token);
  fetch('backend.php',{method:'POST',body:f}).then(r=>r.json()).then(d=>{
    if(d.tasks){
      document.getElementById('totalTasks').innerText=d.tasks.length;
      document.getElementById('doneTasks').innerText=d.tasks.filter(t=>t.status==='done').length;
      document.getElementById('pendingTasks').innerText=d.tasks.filter(t=>t.status==='pending').length;
    }
  });

}



function logout(){
  const f = new FormData();
  f.append('action','logout');
  f.append('csrf_token',csrf_token);
  fetch('backend.php',{method:'POST',body:f}).then(r=>r.json()).then(d=>{
    if(d.success){ window.location.href='login.php'; } else { toast('حدث خطأ أثناء تسجيل الخروج'); }
  });

}

</script>

</body>
</html>

