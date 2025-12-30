

<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'task_manager';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في الاتصال بقاعدة البيانات']);
    exit();
}

// تنظيف المدخلات
function clean_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// تحقق البريد
function validate_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// تحقق الهاتف
function validate_phone($phone){
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if(strlen($phone) !== 9) return false;
    $prefixes = ['70','71','73','77','78'];
    $prefix = substr($phone,0,2);
    return in_array($prefix,$prefixes) && ctype_digit($phone);
}

// تحقق كلمة المرور
function validate_password($password){
    return strlen($password) >= 6;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// تسجيل حساب جديد
if($action==='register'){
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($name) || empty($email) || empty($phone) || empty($password)){
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }
    if(!validate_email($email)){
        echo json_encode(['success'=>false,'message'=>'صيغة البريد غير صحيحة']); exit();
    }
    if(!validate_phone($phone)){
        echo json_encode(['success'=>false,'message'=>'رقم الهاتف يجب أن يكون 9 أرقام ويبدأ بـ 70,71,73,77,78']); exit();
    }
    if(!validate_password($password)){
        echo json_encode(['success'=>false,'message'=>'كلمة المرور يجب أن تكون 6 أحرف على الأقل']); exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if($stmt->fetch()){ echo json_encode(['success'=>false,'message'=>'البريد الإلكتروني مسجل مسبقاً']); exit(); }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    try{
        $stmt = $pdo->prepare("INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)");
        $stmt->execute([$name,$email,$phone,$hashed]);
        echo json_encode(['success'=>true,'message'=>'تم التسجيل بنجاح']);
    }catch(PDOException $e){ echo json_encode(['success'=>false,'message'=>'حدث خطأ في التسجيل']); }
}

// تسجيل الدخول
elseif($action==='login'){
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)){
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if(!$user || !password_verify($password,$user['password'])){
        echo json_encode(['success'=>false,'message'=>'البريد أو كلمة المرور غير صحيحة']); exit();
    }
    if($user['is_active'] != 1){
        echo json_encode(['success'=>false,'message'=>'الحساب غير نشط']); exit();
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['logged_in'] = true;

    echo json_encode(['success'=>true,'message'=>'تم تسجيل الدخول','user'=>['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email']]]);
}

// تسجيل الخروج
elseif($action==='logout'){
    session_destroy();
    echo json_encode(['success'=>true,'message'=>'تم تسجيل الخروج']);
}

// التحقق من الجلسة
elseif($action==='check_session'){
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']===true){
        echo json_encode(['success'=>true,'user'=>['id'=>$_SESSION['user_id'],'name'=>$_SESSION['user_name']]]);
    }else{ echo json_encode(['success'=>false,'message'=>'غير مسجل دخول']); }
}

// إنشاء مهمة جديدة
elseif($action==='create_task'){
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!==true){
        echo json_encode(['success'=>false,'message'=>'يجب تسجيل الدخول أولاً']); exit();
    }
    $title = clean_input($_POST['title'] ?? '');
    $desc = clean_input($_POST['description'] ?? '');
    if(empty($title)){ echo json_encode(['success'=>false,'message'=>'عنوان المهمة مطلوب']); exit(); }
    try{
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id,title,description) VALUES (?,?,?)");
        $stmt->execute([$_SESSION['user_id'],$title,$desc]);
        echo json_encode(['success'=>true,'message'=>'تم إنشاء المهمة']);
    }catch(PDOException $e){ echo json_encode(['success'=>false,'message'=>'حدث خطأ في إنشاء المهمة']); }
}

// عرض المهام
elseif($action==='read_tasks'){
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!==true){
        echo json_encode(['success'=>false,'message'=>'يجب تسجيل الدخول أولاً']); exit();
    }
    try{
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id=? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success'=>true,'tasks'=>$tasks]);
    }catch(PDOException $e){ echo json_encode(['success'=>false,'message'=>'حدث خطأ في جلب المهام']); }
}

// تعديل مهمة
elseif($action==='update_task'){
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!==true){
        echo json_encode(['success'=>false,'message'=>'يجب تسجيل الدخول أولاً']); exit();
    }
    $task_id = $_POST['task_id'] ?? 0;
    $title = clean_input($_POST['title'] ?? '');
    $desc = clean_input($_POST['description'] ?? '');
    $status = clean_input($_POST['status'] ?? 'pending');
    if(empty($title)){ echo json_encode(['success'=>false,'message'=>'عنوان المهمة مطلوب']); exit(); }

    try{
        $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, status=? WHERE id=? AND user_id=?");
        $stmt->execute([$title,$desc,$status,$task_id,$_SESSION['user_id']]);
        if($stmt->rowCount()===0){ echo json_encode(['success'=>false,'message'=>'المهمة غير موجودة أو لا تملك صلاحية']); }
        else{ echo json_encode(['success'=>true,'message'=>'تم تحديث المهمة']); }
    }catch(PDOException $e){ echo json_encode(['success'=>false,'message'=>'حدث خطأ في التحديث']); }
}

// حذف مهمة
elseif($action==='delete_task'){
    if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in']!==true){
        echo json_encode(['success'=>false,'message'=>'يجب تسجيل الدخول أولاً']); exit();
    }
    $task_id = $_POST['task_id'] ?? 0;
    try{
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
        $stmt->execute([$task_id,$_SESSION['user_id']]);
        if($stmt->rowCount()===0){ echo json_encode(['success'=>false,'message'=>'المهمة غير موجودة أو لا تملك صلاحية']); }
        else{ echo json_encode(['success'=>true,'message'=>'تم حذف المهمة']); }
    }catch(PDOException $e){ echo json_encode(['success'=>false,'message'=>'حدث خطأ في الحذف']); }
}

// حالة افتراضية
else{
    echo json_encode(['success'=>true,'message'=>'API نظام إدارة المهام']);
}
?>


