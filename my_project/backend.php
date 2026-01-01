

<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

/* =========================
   إعداد قاعدة البيانات
========================= */
$host = 'localhost';
$dbname = 'tasks';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    writeLog("Database connection error: ".$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'خطأ في الاتصال بقاعدة البيانات']);
    exit();
}

/* =========================
   دالة تنظيف البيانات
========================= */
function clean($data){ return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8'); }
function validate_email($email){ return filter_var($email, FILTER_VALIDATE_EMAIL); }
function validate_phone($phone){
    $phone = preg_replace('/[^0-9]/','',$phone);
    return strlen($phone)===9 && in_array(substr($phone,0,2), ['70','71','73','77','78']);
}
function validate_password($pass){ return strlen($pass)>=6; }

/* =========================
   دالة CSRF
========================= */
if(!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
function checkCSRF($token){ return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token']===$token; }

/* =========================
   دالة تسجيل الأخطاء
========================= */
function writeLog($message){
    $logFile = __DIR__.'/error.log';
    $date = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$date] $message\n", FILE_APPEND);
}

/* =========================
   التعامل مع الأكشن
========================= */
$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* =========================
   تسجيل حساب
========================= */
if($action==='register'){
    $name = clean($_POST['name']??'');
    $email = clean($_POST['email']??'');
    $phone = clean($_POST['phone']??'');
    $pass = $_POST['password']??'';
    $confirm = $_POST['confirm_password']??'';

    if(!$name||!$email||!$phone||!$pass||!$confirm){
        writeLog("Register failed: Missing fields - $email");
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }
    if($pass !== $confirm){
        writeLog("Register failed: Password mismatch - $email");
        echo json_encode(['success'=>false,'message'=>'كلمة المرور وتأكيدها غير متطابقين']); exit();
    }
    if(!validate_email($email)){
        writeLog("Register failed: Invalid email - $email");
        echo json_encode(['success'=>false,'message'=>'صيغة البريد غير صحيحة']); exit();
    }
    if(!validate_phone($phone)){
        writeLog("Register failed: Invalid phone - $phone");
        echo json_encode(['success'=>false,'message'=>'رقم الهاتف غير صالح']); exit();
    }
    if(!validate_password($pass)){
        writeLog("Register failed: Short password - $email");
        echo json_encode(['success'=>false,'message'=>'كلمة المرور قصيرة']); exit();
    }

    $stmt=$pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if($stmt->fetch()){
        writeLog("Register failed: Email exists - $email");
        echo json_encode(['success'=>false,'message'=>'البريد مسجل مسبقاً']); exit();
    }

    $hashed=password_hash($pass,PASSWORD_DEFAULT);
    $stmt=$pdo->prepare("INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)");
    $stmt->execute([$name,$email,$phone,$hashed]);
    echo json_encode(['success'=>true,'message'=>'تم التسجيل بنجاح']);
}

/* =========================
   تسجيل دخول
========================= */
elseif($action==='login'){
    $email = clean($_POST['email']??'');
    $pass = $_POST['password']??'';

    if(!$email||!$pass){
        writeLog("Login failed: Missing fields - $email");
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }

    $stmt=$pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user=$stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        writeLog("Login failed: User not found - $email");
        echo json_encode(['success'=>false,'message'=>'البريد أو كلمة المرور غير صحيحة']); exit();
    }

    
    if($user['blocked_until'] && strtotime($user['blocked_until'])>time()){
        $remain = strtotime($user['blocked_until'])-time();
        writeLog("Login blocked: $email for $remain seconds");
        echo json_encode(['success'=>false,'message'=>"تم حظرك مؤقتاً، حاول بعد {$remain} ثانية"]); exit();
    }

    if(!password_verify($pass,$user['password'])){
        $attempts = $user['login_attempts']+1;
        $blocked_until = $attempts>=3 ? date('Y-m-d H:i:s', time()+180) : null;
        $pdo->prepare("UPDATE users SET login_attempts=?, blocked_until=? WHERE id=?")->execute([$attempts,$blocked_until,$user['id']]);
        $msg = $blocked_until ? "تم حظرك 3 دقائق" : "البريد أو كلمة المرور خاطئة";
        writeLog("Login failed: Wrong password - $email, attempts: $attempts");
        echo json_encode(['success'=>false,'message'=>$msg]); exit();
    }

    if($user['is_active']!=1){
        writeLog("Login failed: Account inactive - $email");
        echo json_encode(['success'=>false,'message'=>'الحساب غير نشط']); exit();
    }

    $pdo->prepare("UPDATE users SET login_attempts=0, blocked_until=NULL WHERE id=?")->execute([$user['id']]);

    $_SESSION['user_id']=$user['id'];
    $_SESSION['user_name']=$user['name'];
    $_SESSION['logged_in']=true;


    echo json_encode(['success'=>true,'message'=>'تم تسجيل الدخول','csrf'=>$_SESSION['csrf_token']]);

}


/* =========================
   تسجيل خروج
========================= */
elseif($action==='logout'){
    session_destroy();
    echo json_encode(['success'=>true,'message'=>'تم تسجيل الخروج']);
}

/* =========================
   التحقق من الجلسة
========================= */
elseif($action==='check_session'){
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']===true){
        echo json_encode(['success'=>true,'user'=>['id'=>$_SESSION['user_id'],'name'=>$_SESSION['user_name'],'csrf'=>$_SESSION['csrf_token']]]);
    }else{
        echo json_encode(['success'=>false,'message'=>'غير مسجل دخول']);
    }
}

/* =========================
   CRUD المهام + رفع الملفات
========================= */
elseif(in_array($action,['create_task','read_tasks','update_task','delete_task'])){
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!==true){
        writeLog("Action $action failed: User not logged in");
        echo json_encode(['success'=>false,'message'=>'يجب تسجيل الدخول']); exit();
    }
    if(!checkCSRF($_POST['csrf_token']??'')){
        writeLog("Action $action failed: Invalid CSRF");
        echo json_encode(['success'=>false,'message'=>'رمز CSRF غير صالح']); exit();
    }

    if($action==='create_task'){
        $title = clean($_POST['title']??'');
        $desc = clean($_POST['description']??'');
        if(!$title){ echo json_encode(['success'=>false,'message'=>'العنوان مطلوب']); exit(); }

        $filename = null;
        if(isset($_FILES['file']) && $_FILES['file']['error']===0){
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            if(!is_dir('uploads')) mkdir('uploads',0777,true);
            if(!move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$filename)){
                writeLog("File upload failed for user_id=".$_SESSION['user_id']);
            }
        }

        $stmt=$pdo->prepare("INSERT INTO tasks (user_id,title,description,file) VALUES (?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'],$title,$desc,$filename]);
        echo json_encode(['success'=>true,'message'=>'تم إنشاء المهمة']);
    }

    elseif($action==='read_tasks'){
        $stmt=$pdo->prepare("SELECT * FROM tasks WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success'=>true,'tasks'=>$tasks]);
    }

    elseif($action==='update_task'){
        $id = intval($_POST['task_id']??0);
        $title = clean($_POST['title']??'');
        $desc = clean($_POST['description']??'');
        if(!$title){ echo json_encode(['success'=>false,'message'=>'العنوان مطلوب']); exit(); }

        $stmt=$pdo->prepare("UPDATE tasks SET title=?,description=? WHERE id=? AND user_id=?");
        $stmt->execute([$title,$desc,$id,$_SESSION['user_id']]);
        echo json_encode(['success'=>true,'message'=>'تم التحديث']);
    }

    elseif($action==='delete_task'){
        $id=intval($_POST['task_id']??0);
        $stmt=$pdo->prepare("SELECT file FROM tasks WHERE id=? AND user_id=?");
        $stmt->execute([$id,$_SESSION['user_id']]);
        $task=$stmt->fetch(PDO::FETCH_ASSOC);
        if($task && $task['file'] && file_exists('uploads/'.$task['file'])) unlink('uploads/'.$task['file']);

        $stmt=$pdo->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
        $stmt->execute([$id,$_SESSION['user_id']]);
        echo json_encode(['success'=>true,'message'=>'تم الحذف']);
    }
}

else{
    echo json_encode(['success'=>true,'message'=>'API جاهز']);
}
?>


