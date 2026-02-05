<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Model للتعليقات
 */
class Comment
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * إنشاء تعليق جديد
     */
    public function create(array $data): bool
    {
        $query = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
        return $this->db->execute($query, [
            $data['post_id'],
            $data['user_id'],
            $data['content']
        ]);
    }
    
    /**
     * جلب تعليقات منشور معين
     */
    public function getByPostId(int $postId): array
    {
        $query = "SELECT c.*, u.username, u.profile_image 
                  FROM comments c 
                  JOIN users u ON c.user_id = u.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.created_at DESC";
        return $this->db->query($query, [$postId]);
    }
    
    /**
     * جلب جميع التعليقات (للإدارة)
     */
    public function getAllWithDetails(): array
    {
        $query = "SELECT c.*, u.username, p.title as post_title 
                  FROM comments c 
                  JOIN users u ON c.user_id = u.id 
                  JOIN posts p ON c.post_id = p.id 
                  ORDER BY c.created_at DESC";
        return $this->db->query($query);
    }
    
    /**
     * حذف تعليق
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM comments WHERE id = ?";
        return $this->db->execute($query, [$id]);
    }
    
    /**
     * عد التعليقات
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM comments";
        $result = $this->db->query($query);
        return (int)($result[0]['total'] ?? 0);
    }
}
