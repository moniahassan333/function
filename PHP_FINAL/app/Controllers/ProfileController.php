<?php

namespace App\Controllers;

use App\Models\User;
use Core\Session;

/**
 * Controller للملف الشخصي
 */
class ProfileController
{
    private User $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * عرض الملف الشخصي
     */
    public function show(): void
    {
        // التأكد من تسجيل الدخول
        Session::requireLogin();
        
        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);
        
        require_once __DIR__ . '/../Views/profile/show.php';
    }
    
    /**
     * عرض صفحة تعديل الملف الشخصي
     */
    public function edit(): void
    {
        // التأكد من تسجيل الدخول
        Session::requireLogin();
        
        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);
        
        require_once __DIR__ . '/../Views/profile/edit.php';
    }
    
    /**
     * معالجة تحديث الملف الشخصي
     */
    public function update(): void
    {
        // التأكد من تسجيل الدخول
        Session::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('profile/edit'));
            exit;
        }
        
        $userId = Session::getUserId();
        
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
        ];
        
        // تحديث البيانات الأساسية
        $success = $this->userModel->updateProfile($userId, $data);
        
        // معالجة رفع الصورة إذا وجدت
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imageResult = $this->userModel->uploadProfileImage($userId, $_FILES['profile_image']);
            
            if (!$imageResult) {
                Session::setFlash('error', 'فشل رفع الصورة، تأكد من نوع وحجم الملف');
                header('Location: ' . url('profile/edit'));
                exit;
            }
        }
        
        if ($success) {
            // تحديث بيانات الجلسة
            $user = $this->userModel->findById($userId);
            Session::set('user_data', $user);
            
            Session::setFlash('success', 'تم تحديث الملف الشخصي بنجاح');
            header('Location: ' . url('profile'));
            exit;
        } else {
            Session::setFlash('error', 'فشل تحديث الملف الشخصي');
            header('Location: ' . url('profile/edit'));
            exit;
        }
    }
}
