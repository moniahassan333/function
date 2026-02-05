<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Category;
use Core\Session;

/**
 * Controller لإدارة المنشورات (CRUD)
 */
class PostAdminController
{
    private Post $postModel;
    private Category $categoryModel;
    
    public function __construct()
    {
        // التحقق من تسجيل الدخول والصلاحيات
        if (!Session::isLoggedIn()) {
            header('Location: ' . url('login'));
            exit;
        }

        if (!Session::isAdmin()) {
            Session::setFlash('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
            header('Location: ' . url('posts'));
            exit;
        }
        
        $this->postModel = new Post();
        $this->categoryModel = new Category();
    }
    
    /**
     * عرض قائمة المنشورات
     */
    public function index(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $posts = $this->postModel->getAllWithPagination($page, $limit);
        $totalPosts = $this->postModel->count();
        $totalPages = ceil($totalPosts / $limit);
        
        require_once __DIR__ . '/../Views/admin/posts/index.php';
    }
    
    /**
     * عرض نموذج إنشاء منشور جديد
     */
    public function create(): void
    {
        $categories = $this->categoryModel->getAll();
        require_once __DIR__ . '/../Views/admin/posts/create.php';
    }
    
    /**
     * حفظ منشور جديد
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'user_id' => Session::get('user_id'),
        ];
        
        $postId = $this->postModel->create($data);
        
        if ($postId) {
            // ربط التصنيفات
            if (!empty($_POST['categories'])) {
                $this->postModel->attachCategories($postId, $_POST['categories']);
            }
            
            Session::setFlash('success', 'تم إنشاء المنشور بنجاح');
            header('Location: ' . url('admin/posts'));
        } else {
            Session::setFlash('error', 'فشل إنشاء المنشور');
            header('Location: ' . url('admin/posts/create'));
        }
        exit;
    }
    
    /**
     * عرض نموذج تعديل منشور
     */
    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $post = $this->postModel->getByIdWithDetails($id);
        
        if (!$post) {
            Session::setFlash('error', 'المنشور غير موجود');
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $categories = $this->categoryModel->getAll();
        $postCategories = $this->postModel->getCategories($id);
        $selectedCategoryIds = array_column($postCategories, 'id');
        
        require_once __DIR__ . '/../Views/admin/posts/edit.php';
    }
    
    /**
     * تحديث منشور
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
        ];
        
        $success = $this->postModel->update($id, $data);
        
        if ($success) {
            // تحديث التصنيفات
            $this->postModel->detachCategories($id);
            if (!empty($_POST['categories'])) {
                $this->postModel->attachCategories($id, $_POST['categories']);
            }
            
            Session::setFlash('success', 'تم تحديث المنشور بنجاح');
        } else {
            Session::setFlash('error', 'فشل تحديث المنشور');
        }
        
        header('Location: ' . url('admin/posts'));
        exit;
    }
    
    /**
     * حذف منشور
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            header('Location: ' . url('admin/posts'));
            exit;
        }
        
        $success = $this->postModel->delete($id);
        
        if ($success) {
            Session::setFlash('success', 'تم حذف المنشور بنجاح');
        } else {
            Session::setFlash('error', 'فشل حذف المنشور');
        }
        
        header('Location: ' . url('admin/posts'));
        exit;
    }
}
