<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Models\Comment;
use Core\Session;

/**
 * Controller للوحة التحكم
 */
class AdminController
{
    private Post $postModel;
    private Category $categoryModel;
    private User $userModel;
    private Comment $commentModel;
    
    public function __construct()
    {
        // التحقق من تسجيل الدخول والصلاحيات
        if (!Session::isLoggedIn()) {
            header('Location: ' . url('login'));
            exit;
        }

        if (!Session::isAdmin()) {
            Session::setFlash('error', 'ليس لديك صلاحية للوصول إلى لوحة التحكم');
            header('Location: ' . url('posts'));
            exit;
        }
        
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        $this->commentModel = new Comment();
    }
    
    /**
     * عرض الصفحة الرئيسية للوحة التحكم
     */
    public function index(): void
    {
        // جلب الإحصائيات
        $stats = [
            'total_posts' => $this->postModel->count(),
            'total_categories' => $this->categoryModel->count(),
            'total_users' => $this->userModel->count(),
            'total_comments' => $this->commentModel->count(),
        ];
        
        // جلب آخر المنشورات
        $recentPosts = $this->postModel->getAllWithPagination(1, 5);
        
        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }
}
