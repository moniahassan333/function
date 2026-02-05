<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Category;
use Core\Session;

/**
 * Controller للمنشورات
 */
class PostController
{
    private Post $postModel;
    private Category $categoryModel;
    
    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
    }
    
    /**
     * عرض جميع المنشورات مع بيانات المستخدمين والتصنيفات
     * هذا مثال على استخدام JOIN
     */
    public function index(): void
    {
        // التأكد من تسجيل الدخول
        Session::requireLogin();
        
        // جلب المنشورات مع بيانات المستخدمين والتصنيفات باستخدام JOIN
        $posts = $this->postModel->getAllWithUserAndCategories();
        
        // تمرير البيانات إلى View
        require_once __DIR__ . '/../Views/posts/index.php';
    }
    
    /**
     * عرض منشور محدد
     */
    public function show(): void
    {
        // التأكد من تسجيل الدخول
        Session::requireLogin();
        
        $postId = (int)($_GET['id'] ?? 0);
        
        if ($postId === 0) {
            header('Location: ' . url('posts'));
            exit;
        }
        
        // جلب المنشور مع التفاصيل باستخدام JOIN
        $post = $this->postModel->getByIdWithDetails($postId);
        
        if (!$post) {
            header('Location: ' . url('posts'));
            exit;
        }

        // جلب التعليقات
        $commentModel = new \App\Models\Comment();
        $comments = $commentModel->getByPostId($postId);
        
        require_once __DIR__ . '/../Views/posts/show.php';
    }

    /**
     * عرض صفحة إنشاء منشور جديد (للمستخدمين)
     */
    public function create(): void
    {
        Session::requireLogin();
        $categories = $this->categoryModel->getAll();
        require_once __DIR__ . '/../Views/posts/create.php';
    }

    /**
     * معالجة حفظ المنشور الجديد
     */
    public function store(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('posts'));
            exit;
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'user_id' => Session::get('user_id'),
        ];

        $postId = $this->postModel->create($data);

        if ($postId) {
            if (!empty($_POST['categories'])) {
                $this->postModel->attachCategories($postId, $_POST['categories']);
            }
            Session::setFlash('success', 'تم نشر موضوعك بنجاح');
            header('Location: ' . url('posts'));
        } else {
            Session::setFlash('error', 'فشل نشر الموضوع');
            header('Location: ' . url('posts/create'));
        }
        exit;
    }
}
