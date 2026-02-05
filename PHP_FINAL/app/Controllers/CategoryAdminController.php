<?php

namespace App\Controllers;

use App\Models\Category;
use Core\Session;

/**
 * Controller لإدارة التصنيفات (CRUD)
 */
class CategoryAdminController
{
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
        
        $this->categoryModel = new Category();
    }
    
    /**
     * عرض قائمة التصنيفات
     */
    public function index(): void
    {
        $categories = $this->categoryModel->getAllWithPostCount();
        require_once __DIR__ . '/../Views/admin/categories/index.php';
    }
    
    /**
     * عرض نموذج إنشاء تصنيف جديد
     */
    public function create(): void
    {
        require_once __DIR__ . '/../Views/admin/categories/create.php';
    }
    
    /**
     * حفظ تصنيف جديد
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];
        
        $categoryId = $this->categoryModel->create($data);
        
        if ($categoryId) {
            Session::setFlash('success', 'تم إنشاء التصنيف بنجاح');
            header('Location: ' . url('admin/categories'));
        } else {
            Session::setFlash('error', 'فشل إنشاء التصنيف');
            header('Location: ' . url('admin/categories/create'));
        }
        exit;
    }
    
    /**
     * عرض نموذج تعديل تصنيف
     */
    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        $category = $this->categoryModel->findById($id);
        
        if (!$category) {
            Session::setFlash('error', 'التصنيف غير موجود');
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        require_once __DIR__ . '/../Views/admin/categories/edit.php';
    }
    
    /**
     * تحديث تصنيف
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];
        
        $success = $this->categoryModel->update($id, $data);
        
        if ($success) {
            Session::setFlash('success', 'تم تحديث التصنيف بنجاح');
        } else {
            Session::setFlash('error', 'فشل تحديث التصنيف');
        }
        
        header('Location: ' . url('admin/categories'));
        exit;
    }
    
    /**
     * حذف تصنيف
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            header('Location: ' . url('admin/categories'));
            exit;
        }
        
        $success = $this->categoryModel->delete($id);
        
        if ($success) {
            Session::setFlash('success', 'تم حذف التصنيف بنجاح');
        } else {
            Session::setFlash('error', 'فشل حذف التصنيف');
        }
        
        header('Location: ' . url('admin/categories'));
        exit;
    }
}
