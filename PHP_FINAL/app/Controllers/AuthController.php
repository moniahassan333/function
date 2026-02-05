<?php

namespace App\Controllers;

use App\Models\User;
use Core\Session;

/**
 * Controller للمصادقة (تسجيل دخول/خروج/تسجيل)
 */
class AuthController
{
    private User $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLogin(): void
    {
        // إذا كان مسجل دخول، إعادة توجيه للصفحة الرئيسية
        if (Session::isLoggedIn()) {
            header('Location: ' . url('posts'));
            exit;
        }
        
        require_once __DIR__ . '/../Views/auth/login.php';
    }
    
    /**
     * معالجة تسجيل الدخول
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('login'));
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->login($email, $password);
        
        if ($user) {
            Session::login($user['id'], $user);
            Session::setFlash('success', 'تم تسجيل الدخول بنجاح');
            
            // التوجيه بناءً على الصلاحيات
            if ($user['role'] === 'admin') {
                header('Location: ' . url('admin'));
            } else {
                header('Location: ' . url('posts'));
            }
            exit;
        } else {
            Session::setFlash('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
            header('Location: ' . url('login'));
            exit;
        }
    }
    
    /**
     * عرض صفحة التسجيل
     */
    public function showRegister(): void
    {
        // إذا كان مسجل دخول، إعادة توجيه للصفحة الرئيسية
        if (Session::isLoggedIn()) {
            header('Location: ' . url('posts'));
            exit;
        }
        
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    
    /**
     * معالجة التسجيل
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('register'));
            exit;
        }
        
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'user',
        ];
        
           
        $userId = $this->userModel->register($data);
        
        if ($userId) {
            Session::setFlash('success', 'تم إنشاء الحساب بنجاح، يمكنك تسجيل الدخول الآن');
            header('Location: ' . url('login'));
            exit;
        } else {
            Session::setFlash('error', 'فشل إنشاء الحساب، تأكد من صحة البيانات أو أن الحساب غير موجود مسبقاً');
            header('Location: ' . url('register'));
            exit;
        }
    }
    /**
     * تسجيل الخروج
     */
    public function logout(): void
    {
        Session::logout();

       if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// مسح جميع بيانات الجلسة
$_SESSION = [];

// مسح كوكي الجلسة
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// تدمير الجلسة نهائيًا
session_destroy();


// header('Location: ' . url('login'));
//exit;   




    }
}


