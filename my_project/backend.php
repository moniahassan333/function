<?php
/* =========================
   بدء الجلسة + إعدادات أمان
========================= */
ini_set('session.use_strict_mode', 1);
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
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
        
    );
} catch (PDOException $e) {
    
    writeLog("DB Error: ".$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'خطأ في الاتصال بقاعدة البيانات']);
    exit();
}

/* =========================
   دوال مساعدة
========================= */
function clean($data){
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
function validate_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function validate_phone($phone){
    $phone = preg_replace('/[^0-9]/','',$phone);
    return strlen($phone)===9 && in_array(substr($phone,0,2), ['70','71','73','77','78']);
}
function validate_password($pass){
    return strlen($pass)>=6;
}

/* =========================
   تسجيل الأخطاء
========================= */
function writeLog($message){
    $file = __DIR__.'/error.log';
    $date = date("Y-m-d H:i:s");
    file_put_contents($file,"[$date] $message\n",FILE_APPEND);
}

/* =========================
   CSRF Protection
========================= */
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function checkCSRF($token){
    return isset($_SESSION['csrf_token']) && 
    hash_equals($_SESSION['csrf_token'],$token);
}

/* =========================
   تحديد الأكشن
========================= */
$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* =========================
   تسجيل حساب
========================= */
if($action==='register'){
    $name  = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';


    if(!$name||!$email||!$phone||!$pass||!$confirm){
        
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }
    if($pass!==$confirm){
        echo json_encode(['success'=>false,'message'=>'كلمة المرور غير متطابقة']); exit();
    }
    if(!validate_email($email)||!validate_phone($phone)||!validate_password($pass)){
        echo json_encode(['success'=>false,'message'=>'بيانات غير صحيحة']); exit();
    }

    $check=$pdo->prepare("SELECT id FROM users WHERE email=?");
    $check->execute([$email]);
    if($check->fetch()){
        echo json_encode(['success'=>false,'message'=>'البريد مسجل مسبقاً']); exit();
    }

    $hash=password_hash($pass,PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)")
        ->execute([$name,$email,$phone,$hash]);

    echo json_encode(['success'=>true,'message'=>'تم التسجيل']);
}

/* =========================
   تسجيل دخول
========================= */
elseif($action==='login'){
    $email = clean($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if(!$email||!$pass){

        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }

    $stmt=$pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user=$stmt->fetch();

    if(!$user || !password_verify($pass,$user['password'])){
        echo json_encode(['success'=>false,'message'=>'بيانات الدخول غير صحيحة']); exit();
    }
     /*يمنع خطف الجلسة*/
    session_regenerate_id(true);
    
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['logged_in'] = true;

    echo json_encode([
        'success'=>true,
        'message'=>'تم تسجيل الدخول',
        'csrf'=>$_SESSION['csrf_token']
    ]);
}

/* =========================
   تسجيل خروج (تدمير كامل)
========================= */
elseif($action==='logout'){

    $_SESSION = [];

    if(ini_get("session.use_cookies")){
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time()-42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    echo json_encode(['success'=>true,'message'=>'تم تسجيل الخروج نهائياً']);
}

/* =========================
   التحقق من الجلسة
========================= */
elseif($action==='check_session'){
    if(
        isset($_SESSION['logged_in'],$_SESSION['user_id'],$_SESSION['user_name']) &&
        $_SESSION['logged_in']===true
    ){
        echo json_encode([
            'success'=>true,
            'user'=>[
                'id'=>$_SESSION['user_id'],
                'name'=>$_SESSION['user_name']
            ],
            'csrf'=>$_SESSION['csrf_token']
        ]);
    }else{
        echo json_encode(['success'=>false,'message'=>'غير مسجل دخول']);
    }
}

/* =========================
   CRUD المهام
========================= */
elseif(in_array($action,['create_task','read_tasks','update_task','delete_task'])){

    if(empty($_SESSION['logged_in'])){
        echo json_encode(['success'=>false,'message'=>'يجب تسجيل الدخول']); exit();
    }
    if(!checkCSRF($_POST['csrf_token'] ?? '')){
        echo json_encode(['success'=>false,'message'=>'CSRF غير صالح']); exit();
    }

    if($action==='create_task'){
        $title=clean($_POST['title'] ?? '');
        $desc =clean($_POST['description'] ?? '');
        if(!$title){ echo json_encode(['success'=>false,'message'=>'العنوان مطلوب']); exit(); }

        $pdo->prepare("INSERT INTO tasks (user_id,title,description) VALUES (?,?,?)")
            ->execute([$_SESSION['user_id'],$title,$desc]);

        echo json_encode(['success'=>true,'message'=>'تمت الإضافة']);
    }

    elseif($action==='read_tasks'){
        $stmt=$pdo->prepare("SELECT * FROM tasks WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode(['success'=>true,'tasks'=>$stmt->fetchAll()]);
    }

    elseif($action==='update_task'){
        $id=intval($_POST['task_id'] ?? 0);
        $title=clean($_POST['title'] ?? '');
        $desc =clean($_POST['description'] ?? '');
        $pdo->prepare("UPDATE tasks SET title=?,description=? WHERE id=? AND user_id=?")
            ->execute([$title,$desc,$id,$_SESSION['user_id']]);
        echo json_encode(['success'=>true,'message'=>'تم التحديث']);
    }

    elseif($action==='delete_task'){
        $id=intval($_POST['task_id'] ?? 0);
        $pdo->prepare("DELETE FROM tasks WHERE id=? AND user_id=?")
            ->execute([$id,$_SESSION['user_id']]);
        echo json_encode(['success'=>true,'message'=>'تم الحذف']);
    }
}

else{
    echo json_encode(['success'=>true,'message'=>'API يعمل']);
}
?>


