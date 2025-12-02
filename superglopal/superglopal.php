

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// COOKIE افتراضي
setcookie("username", "منيا", time()+3600);

// SESSION افتراضي
if(!isset($_SESSION['saved_name'])){
    $_SESSION['saved_name'] = null;
}

// UPLOAD
$upload_result = null;
if(isset($_POST['upload_btn']) && isset($_FILES['myfile'])){
    $dir = "uploads/";
    if(!is_dir($dir)) mkdir($dir, 0777, true);
    $target = $dir . basename($_FILES['myfile']['name']);
    $upload_result = move_uploaded_file($_FILES['myfile']['tmp_name'],$target)
        ? "تم رفع الملف بنجاح"
        : "فشل رفع الملف";
}

// POST تسجيل دخول
$login_msg = null;
if(isset($_POST['login_btn'])){
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $login_msg = ($user=="مروى" && $pass=="2025") ? "أهلاً مروى" : "بيانات خاطئة";
}

// SESSION حفظ/إنهاء
if(isset($_POST['save_session'])){
    $_SESSION['saved_name'] = $_POST['session_name'] ?? '';
    header("Refresh:0"); exit;
}
if(isset($_POST['logout'])){
    session_destroy();
    header("Refresh:0"); exit;
}
?><!DOCTYPE html><html>
<head>
<meta charset="UTF-8">
<title>Superglobals – 15 مثال PHP</title>
</head>
<body>
<h1>  تكليف 2 </h1><!-- 1. SERVER بسيط --><h2>1. مثال $_SERVER البسيط</h2>
<pre><?php print_r($_SERVER); ?></pre><!-- 2. SERVER متقدم --><h2>2. مثال $_SERVER المتقدم</h2>
<p>نوع الطلب: <?= $_SERVER['REQUEST_METHOD'] ?></p>
<p>عنوان الصفحة: <?= $_SERVER['REQUEST_URI'] ?></p>
<p>IP المستخدم: <?= $_SERVER['REMOTE_ADDR'] ?></p>
<p>اسم السكربت: <?= $_SERVER['SCRIPT_NAME'] ?></p><!-- 3. قراءة GET --><h2>3. قراءة $_GET</h2>
<form method="get">
<input type="text" name="name" placeholder="اكتب اسمك GET">
<button type="submit">إرسال GET</button>
</form>
<?php if(isset($_GET['name'])) echo "GET: مرحباً ".htmlspecialchars($_GET['name']); ?><!-- 4. قراءة POST --><h2>4. قراءة $_POST</h2>
<form method="post">
<input type="text" name="username" placeholder="اسم المستخدم">
<input type="password" name="password" placeholder="كلمة المرور">
<button name="login_btn">تسجيل الدخول</button>
</form>
<?php if($login_msg) echo $login_msg; ?><!-- 5. استخدام REQUEST --><h2>5. استخدام $_REQUEST</h2>
<form method="get">
<input type="text" name="reqname" placeholder="Request Name">
<button>إرسال</button>
</form>
<?php $req = $_REQUEST['reqname'] ?? null; if($req) echo "REQUEST: ".htmlspecialchars($req); ?>
<pre><?php echo ' $name = $_REQUEST[\'name\'] ?? null; '; ?></pre><!-- 6. عرض FILES --><h2>6. عرض $_FILES</h2>
<form method="post" enctype="multipart/form-data">
<input type="file" name="myfile">
<button name="upload_btn">رفع</button>
</form>
<?php if(isset($_FILES['myfile'])){ echo "<pre>"; print_r($_FILES); echo "</pre>"; } ?>
<?php if($upload_result) echo $upload_result; ?><!-- 7. رفع ملف --><h2>7. رفع ملف إلى مجلد uploads/</h2>
<p>تم التعامل معه في القسم 6 أيضًا</p><!-- 8. إنشاء Cookie --><h2>8. إنشاء Cookie</h2>
<p>تم إنشاء كوكي باسم username بقيمة "منيا"</p><!-- 9. قراءة Cookie --><h2>9. قراءة Cookie</h2>
<p>
<?php
if(isset($_COOKIE['username'])){
    echo "الكوكي محفوظ: " . htmlspecialchars($_COOKIE['username']);
} else {
    echo "لا يوجد كوكي محفوظ";
}
?>
</p><!-- 10. بدء وتخزين Session --><h2>10. بدء وتخزين Session</h2>
<form method="post">
<input type="text" name="session_name" placeholder="اكتب اسمك للجلسة">
<button name="save_session">حفظ في الجلسة</button>
</form><!-- 11. قراءة Session --><h2>11. قراءة Session</h2>
<?php if(isset($_SESSION['saved_name']) && $_SESSION['saved_name']) echo "SESSION: مرحباً ".htmlspecialchars($_SESSION['saved_name']); ?><!-- 12. حذف قيمة Session --><h2>12. حذف قيمة Session</h2>
<form method="post">
<button name="logout">إنهاء الجلسة</button>
</form><!-- 13. إنهاء Session --><h2>13. إنهاء Session</h2>
<p>انتهى عند الضغط على زر القسم 12</p><!-- 14. استخدام $GLOBALS --><h2>14. استخدام $GLOBALS</h2>
<?php $x=10; function show(){ echo $GLOBALS['x']; } ?>
<p>قيمة x باستخدام $GLOBALS: <?php show(); ?></p>
<pre><?php echo ' $x=10; function show(){ echo $GLOBALS[\'x\']; } '; ?></pre><!-- 15. ملف info.php --><h2>15. ملف info.php</h2>
<pre><?php phpinfo(); ?></pre></body>
</html>


