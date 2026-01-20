<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول - InstaClone</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<style>
body{margin:0;padding:0;font-family:'Cairo',sans-serif;background:#fafafa;display:flex;justify-content:center;align-items:center;height:100vh;}
.container{background:#fff;padding:30px;border-radius:15px;box-shadow:0 8px 20px rgba(0,0,0,0.1);width:350px;}
h2{text-align:center;color:#1e3a8a;margin-bottom:20px;}
input{width:100%;padding:12px;margin:10px 0;border-radius:8px;border:1px solid #ccc;font-size:14px;}
button{width:100%;padding:12px;background:#1e3a8a;color:#fff;border:none;border-radius:8px;font-size:16px;margin-top:10px;cursor:pointer;transition:0.3s;}
button:hover{background:#2563eb;}
.toggle{background:#22c55e;margin-top:5px;}
.toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);padding:12px 20px;background:#1e3a8a;color:#fff;border-radius:10px;display:none;z-index:999;}
</style>
</head>
<body>

<div class="container" id="loginDiv">
    <h2>تسجيل الدخول</h2>
    <input type="email" id="loginEmail" placeholder="البريد الإلكتروني">
    <input type="password" id="loginPass" placeholder="كلمة المرور">
    <button onclick="login()">دخول</button>
    <button class="toggle" onclick="toggleForm()">إنشاء حساب</button>
</div>

<div class="container" id="registerDiv" style="display:none">
    <h2>إنشاء حساب</h2>
    <input type="text" id="regName" placeholder="الاسم">
    <input type="email" id="regEmail" placeholder="البريد الإلكتروني">
    <input type="text" id="regPhone" placeholder="رقم الهاتف">
    <input type="password" id="regPass" placeholder="كلمة المرور">
    <input type="password" id="regConfirm" placeholder="تأكيد كلمة المرور">
    <button onclick="register()">إنشاء حساب</button>
    <button class="toggle" onclick="toggleForm()">تسجيل الدخول</button>
</div>

<div class="toast" id="toast"></div>

<script>
function toast(msg){
    const t=document.getElementById('toast');
    t.innerText=msg;
    t.style.display='block';
    setTimeout(()=>t.style.display='none',3000);
}

function toggleForm(){
    const loginDiv=document.getElementById('loginDiv');
    const registerDiv=document.getElementById('registerDiv');
    loginDiv.style.display = loginDiv.style.display==='none'?'block':'none';
    registerDiv.style.display = registerDiv.style.display==='none'?'block':'none';
}

function login(){
    const email=document.getElementById('loginEmail').value.trim();
    const pass=document.getElementById('loginPass').value;

    if(!email || !pass){ toast('جميع الحقول مطلوبة'); return; }

    const f = new FormData();
    f.append('action','login');
    f.append('email',email);
    f.append('password',pass);

    fetch('backend.php',{method:'POST',body:f})
    .then(r=>r.json())
    .then(d=>{
        toast(d.message);
        if(d.success) setTimeout(()=>window.location.href='dashboard.php',1000);
    })
    .catch(()=>toast('حدث خطأ في الاتصال بالخادم'));
}

function register(){
    const name=document.getElementById('regName').value.trim();
    const email=document.getElementById('regEmail').value.trim();
    const phone=document.getElementById('regPhone').value.trim();
    const pass=document.getElementById('regPass').value;
    const confirm=document.getElementById('regConfirm').value;

    if(!name || !email || !phone || !pass || !confirm){ toast('جميع الحقول مطلوبة'); return; }

    const f = new FormData();
    f.append('action','register');
    f.append('name',name);
    f.append('email',email);
    f.append('phone',phone);
    f.append('password',pass);
    f.append('confirm_password',confirm);

    fetch('backend.php',{method:'POST',body:f})
    .then(r=>r.json())
    .then(d=>{
        toast(d.message);
        if(d.success) setTimeout(()=>toggleForm(),1000);
    })
    .catch(()=>toast('حدث خطأ في الاتصال بالخادم'));
}
</script>

</body>
</html>

