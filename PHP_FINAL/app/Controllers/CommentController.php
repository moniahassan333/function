<?php

namespace App\Controllers;

use App\Models\Comment;
use Core\Session;

/**
 * Controller للتعليقات
 */
class CommentController
{
    private Comment $commentModel;
    
    public function __construct()
    {
        $this->commentModel = new Comment();
    }
    
    /**
     * حفظ تعليق جديد
     */
    public function store(): void
    {
        Session::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('posts'));
            exit;
        }
        
        $postId = (int)($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        
        if ($postId === 0 || empty($content)) {
            Session::setFlash('error', 'التعليق لا يمكن أن يكون فارغاً');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?: url('posts')));
            exit;
        }
        
        $data = [
            'post_id' => $postId,
            'user_id' => Session::get('user_id'),
            'content' => $content
        ];
        
        if ($this->commentModel->create($data)) {
            Session::setFlash('success', 'تم إضافة التعليق بنجاح');
        } else {
            Session::setFlash('error', 'فشل إضافة التعليق');
        }
        
        header('Location: ' . url('post?id=' . $postId));
        exit;
    }
}
