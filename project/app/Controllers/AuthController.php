<?php
namespace app\Controllers;

use app\Core\Database;
use app\Core\Session;
use app\Core\Validator;
use app\Models\User;

header('Content-Type: application/json; charset=utf-8');
session_start();

$action = $_POST['action'] ?? '';

$db = Database::connect();
$session = new Session();

if($action==='register'){
    $name = Validator::clean($_POST['name'] ?? '');
    $email = Validator::clean($_POST['email'] ?? '');
    $phone = Validator::clean($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if(!$name||!$email||!$phone||!$pass||!$confirm){
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }
    if(!Validator::validateName($name)){
        echo json_encode(['success'=>false,'message'=>'الاسم غير صالح']); exit();
    }
    if(!Validator::validateEmail($email)){
        echo json_encode(['success'=>false,'message'=>'البريد الإلكتروني غير صالح']); exit();
    }
    if(!Validator::validatePhone($phone)){
        echo json_encode(['success'=>false,'message'=>'رقم الهاتف غير صالح']); exit();
    }
    if(!Validator::validatePassword($pass)){
        echo json_encode(['success'=>false,'message'=>'كلمة المرور قصيرة']); exit();
    }
    if($pass!==$confirm){
        echo json_encode(['success'=>false,'message'=>'كلمة المرور وتأكيدها غير متطابقين']); exit();
    }

    $userModel = new User($db);
    if($userModel->emailExists($email)){
        echo json_encode(['success'=>false,'message'=>'البريد مسجل مسبقاً']); exit();
    }

    $hashed = password_hash($pass,PASSWORD_DEFAULT);
    $userModel->create($name,$email,$phone,$hashed);

    echo json_encode(['success'=>true,'message'=>'تم إنشاء الحساب بنجاح']);
    exit();
}

elseif($action==='login'){
    $email = Validator::clean($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if(!$email||!$pass){
        echo json_encode(['success'=>false,'message'=>'جميع الحقول مطلوبة']); exit();
    }

    $userModel = new User($db);
    $user = $userModel->getByEmail($email);
    if(!$user){
        echo json_encode(['success'=>false,'message'=>'البريد أو كلمة المرور خاطئة']); exit();
    }
    if(!password_verify($pass,$user['password'])){
        echo json_encode(['success'=>false,'message'=>'البريد أو كلمة المرور خاطئة']); exit();
    }

    $session->create($user['id'],$user['name']);
    echo json_encode(['success'=>true,'message'=>'تم تسجيل الدخول']);
    exit();
}

else{
    echo json_encode(['success'=>false,'message'=>'عملية غير معروفة']);
}
