

<?php
session_start();

// لو المستخدم مسجل دخول، ودّه مباشرة للداشبورد
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول</title>

<style>
body{
  font-family:sans-serif;
  background:#2f375eff;
  margin:0;
  padding:0;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh
}
.container{
  width:350px;
  padding:30px;
  border-radius:15px;
  box-shadow:0 0 20px rgba(0,0,0,0.1);
  background:#fff
}
h2{text-align:center;color:#1e3a8a}
input{
  width:100%;
  padding:10px;
  margin:10px 0;
  border-radius:8px;
  border:1px solid #404284ff
}
button{
  width:100%;
  padding:10px;
  background:#1e3a8a;
  color:#fff;
  border:none;
  border-radius:8px;
  margin-top:10px;
  cursor:pointer
}
.toggle{
  background:#2563eb;
  margin-top:5px
}
.toast{
  position:fixed;
  bottom:20px;
  left:50%;
  transform:translateX(-50%);
  padding:10px 20px;
  background:#1e3a8a;
  color:#fff;
  border-radius:10px;
  display:none
}
</style>
</head>
<body>

<!-- تسجيل الدخول -->
<div class="container" id="loginDiv">
<form autocomplete="off" onsubmit="return false;">
  <h2>تسجيل الدخول</h2>

  <input type="email" id="loginEmail"
         placeholder="البريد الإلكتروني"
         autocomplete="off">

  <input type="password" id="loginPass"
         placeholder="كلمة المرور"
         autocomplete="new-password">

  <button type="button" onclick="login()">دخول</button>
  <button type="button" class="toggle" onclick="toggleForm()">إنشاء حساب</button>
</form>
</div>

<!-- إنشاء حساب -->
<div class="container" id="registerDiv" style="display:none">
<form autocomplete="off" onsubmit="return false;">
  <h2>إنشاء حساب</h2>

  <input type="text" id="regName" placeholder="الاسم" autocomplete="off">
  <input type="email" id="regEmail" placeholder="البريد الإلكتروني" autocomplete="off">
  <input type="text" id="regPhone" placeholder="رقم الهاتف" autocomplete="off">
  <input type="password" id="regPass" placeholder="كلمة المرور" autocomplete="new-password">

  <button type="button" onclick="register()">إنشاء حساب</button>
  <button type="button" class="toggle" onclick="toggleForm()">تسجيل الدخول</button>
</form>
</div>

<div class="toast" id="toast"></div>

<script>
// تفريغ الحقول عند تحميل الصفحة (منع Autofill)
window.onload = function(){
  document.getElementById('loginEmail').value = '';
  document.getElementById('loginPass').value = '';
};

function toast(msg){
  const t=document.getElementById('toast');
  t.innerText=msg;
  t.style.display='block';
  setTimeout(()=>t.style.display='none',3000);
}

function toggleForm(){
  document.getElementById('loginDiv').style.display =
    document.getElementById('loginDiv').style.display==='none'?'block':'none';

  document.getElementById('registerDiv').style.display =
    document.getElementById('registerDiv').style.display==='none'?'block':'none';
}

function login(){
  const f = new FormData();
  f.append('action','login');
  f.append('email',document.getElementById('loginEmail').value);
  f.append('password',document.getElementById('loginPass').value);

  fetch('backend.php',{
    method:'POST',
    body:f,
    credentials:'include'
  })
  .then(r=>r.json())
  .then(d=>{
    toast(d.message);
    if(d.success){
      window.location.href='dashboard.php';
    }
  });
}

function register(){
  const f = new FormData();
  f.append('action','register');
  f.append('name',document.getElementById('regName').value);
  f.append('email',document.getElementById('regEmail').value);
  f.append('phone',document.getElementById('regPhone').value);
  f.append('password',document.getElementById('regPass').value);

  fetch('backend.php',{
    method:'POST',
    body:f,
    credentials:'include'
  })
  .then(r=>r.json())
  .then(d=>{
    toast(d.message);
    if(d.success){
      toggleForm();
    }
  });
}
</script>

</body>
</html>


