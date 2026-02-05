<?php

namespace App\Controllers;

use App\Models\Comment;
use Core\Session;

/**
 * Controller لإدارة التعليقات (للآدمن)
 */
class CommentAdminController
{
    private Comment $commentModel;
    
    public function __construct()
    {
        // التحقق من الصلاحيات
        if (!Session::isAdmin()) {
            Session::setFlash('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
            header('Location: ' . url('posts'));
            exit;
        }
        
        $this->commentModel = new Comment();
    }
    
    /**
     * عرض جميع التعليقات
     */
    public function index(): void
    {
        $comments = $this->commentModel->getAllWithDetails();
        $pageTitle = 'إدارة التعليقات';
        require_once __DIR__ . '/../Views/admin/comments/index.php';
    }
    
    /**
     * حذف تعليق
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/comments'));
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        if ($id > 0) {
            if ($this->commentModel->delete($id)) {
                Session::setFlash('success', 'تم حذف التعليق بنجاح');
            } else {
                Session::setFlash('error', 'فشل حذف التعليق');
            }
        }
        
        header('Location: ' . url('admin/comments'));
        exit;
    }
}
