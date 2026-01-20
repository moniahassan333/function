<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول / إنشاء حساب</title>
<style>
body{
  font-family:sans-serif;
  background:#2f375eff;
  margin:0;
  padding:0;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
  transition:0.5s;
}
.container{
  width:350px;
  padding:30px;
  border-radius:15px;
  box-shadow:0 0 20px rgba(0,0,0,0.1);
  background:#fff;
  transition:0.5s;
}
h2{text-align:center;color:#1e3a8a;}
.input-group{
  position:relative;
}
input{
  width:100%;
  padding:10px;
  margin:10px 0;
  border-radius:8px;
  border:1px solid #404284ff;
  transition:0.3s;
}
input:focus{
  border-color:#2563eb;
  box-shadow:0 0 8px #2563eb;
}
.toggle-pass{
  position:absolute;
  top:50%;
  left:10px;
  transform:translateY(-50%);
  cursor:pointer;
  user-select:none;
  font-size:18px;
  color:#2563eb;
}
button{
  width:100%;
  padding:10px;
  background:#1e3a8a;
  color:#fff;
  border:none;
  border-radius:8px;
  margin-top:10px;
  cursor:pointer;
  transition:0.3s;
}
button:hover{
  transform: translateY(-3px);
  box-shadow:0 5px 15px rgba(0,0,0,0.2);
  background:#2563eb;
}
.toggle{
  background:#2563eb;
  margin-top:5px;
}
.toast{
  position:fixed;
  bottom:20px;
  left:50%;
  transform:translateX(-50%) translateY(50px);
  padding:10px 20px;
  background:#1e3a8a;
  color:#fff;
  border-radius:10px;
  display:none;
  opacity:0;
  transition:0.5s;
}
.toast.show{
  display:block;
  opacity:1;
  transform:translateX(-50%) translateY(0);
}
.fade{
  animation: fadeEffect 0.5s ease;
}
@keyframes fadeEffect{
  from{opacity:0; transform:translateY(20px);}
  to{opacity:1; transform:translateY(0);}
}
</style>
</head>
<body>

<!-- تسجيل الدخول -->
<div class="container fade" id="loginDiv">
  <form autocomplete="off" onsubmit="return false;">
    <p style="text-align:center; font-size:16px; color:#2563eb; margin-bottom:10px;">
     ⚜ أدِر مهامك بكل سهولة ⚜
    </p>
    <h2>تسجيل الدخول</h2>
    <input type="email" id="loginEmail" placeholder="البريد الإلكتروني" autocomplete="off">
    <div class="input-group">
      <input type="password" id="loginPass" placeholder="كلمة المرور" autocomplete="new-password">
      <span class="toggle-pass" onclick="togglePassword('loginPass', this)">*</span>
    </div>
    <button type="button" onclick="login()">دخول</button>
    <button type="button" class="toggle" onclick="toggleForm()">إنشاء حساب</button>
  </form>
</div>

<!-- إنشاء حساب -->
<div class="container fade" id="registerDiv" style="display:none">
  <form autocomplete="off" onsubmit="return false;">
    <h2>إنشاء حساب</h2>
    <input type="text" id="regName" placeholder="الاسم" autocomplete="off">
    <input type="email" id="regEmail" placeholder="البريد الإلكتروني" autocomplete="off">
    <input type="text" id="regPhone" placeholder="رقم الهاتف" autocomplete="off">
    <div class="input-group">
      <input type="password" id="regPass" placeholder="كلمة المرور" autocomplete="new-password">
      <span class="toggle-pass" onclick="togglePassword('regPass', this)">*</span>
    </div>
    <div class="input-group">
      <input type="password" id="regConfirm" placeholder="تأكيد كلمة المرور" autocomplete="new-password">
      <span class="toggle-pass" onclick="togglePassword('regConfirm', this)">*</span>
    </div>
    <button type="button" onclick="register()">إنشاء حساب</button>
    <button type="button" class="toggle" onclick="toggleForm()">تسجيل الدخول</button>
  </form>
</div>

<div class="toast" id="toast"></div>

<script>

window.onload = function(){
  document.getElementById('loginEmail').value='';
  document.getElementById('loginPass').value='';
};

function toast(msg){
  const t=document.getElementById('toast');
  t.innerText=msg;
  t.classList.add('show');
  setTimeout(()=>{
    t.classList.remove('show');
  },3000);
}

function toggleForm(){
  const loginDiv=document.getElementById('loginDiv');
  const registerDiv=document.getElementById('registerDiv');
  loginDiv.style.display = loginDiv.style.display==='none'?'block':'none';
  registerDiv.style.display = registerDiv.style.display==='none'?'block':'none';
  loginDiv.classList.add('fade');
  registerDiv.classList.add('fade');
}

// toggle كلمة المرور
function togglePassword(id, el){
  const input = document.getElementById(id);
  if(input.type==='password'){
    input.type='text';
    el.textContent='O';
  } else {
    input.type='password';
    el.textContent='*';
  }
}

// تسجيل دخول
function login(){
  const f = new FormData();
  f.append('action','login');
  f.append('email',document.getElementById('loginEmail').value);
  f.append('password',document.getElementById('loginPass').value);

  fetch('backend.php',{method:'POST',body:f,credentials:'include'})
  .then(r=>r.json())
  .then(d=>{
    toast(d.message);
    if(d.success){
      window.location.href='dashboard.php';
    }
  });
}

// إنشاء حساب
function register(){
  const f = new FormData();
  f.append('action','register');
  f.append('name',document.getElementById('regName').value);
  f.append('email',document.getElementById('regEmail').value);
  f.append('phone',document.getElementById('regPhone').value);
  f.append('password',document.getElementById('regPass').value);
  f.append('confirm_password',document.getElementById('regConfirm').value);

  fetch('backend.php',{method:'POST',body:f,credentials:'include'})
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


