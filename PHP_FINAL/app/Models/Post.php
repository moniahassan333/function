<?php

namespace App\Models;

use Core\Database;

/**
 * Model للمنشورات
 */
class Post
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * جلب جميع المنشورات مع بيانات المستخدم والتصنيفات باستخدام JOIN
     * 
     * @return array
     */
    public function getAllWithUserAndCategories(): array
    {
        $query = "
            SELECT 
                p.id,
                p.title,
                p.content,
                p.created_at,
                u.id as user_id,
                u.username,
                u.profile_image,
                GROUP_CONCAT(c.name SEPARATOR ', ') as categories
            FROM posts p
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN post_categories pc ON p.id = pc.post_id
            LEFT JOIN categories c ON pc.category_id = c.id
            GROUP BY p.id, p.title, p.content, p.created_at, u.id, u.username, u.profile_image
            ORDER BY p.created_at DESC
        ";
        
        return $this->db->query($query);
    }
    
    /**
     * جلب منشور محدد مع جميع التفاصيل باستخدام JOIN
     * 
     * @param int $id
     * @return array|null
     */
    public function getByIdWithDetails(int $id): ?array
    {
        $query = "
            SELECT 
                p.id,
                p.title,
                p.content,
                p.created_at,
                u.id as user_id,
                u.username,
                u.email,
                u.profile_image,
                GROUP_CONCAT(c.name SEPARATOR ', ') as categories,
                GROUP_CONCAT(c.id) as category_ids
            FROM posts p
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN post_categories pc ON p.id = pc.post_id
            LEFT JOIN categories c ON pc.category_id = c.id
            WHERE p.id = ?
            GROUP BY p.id, p.title, p.content, p.created_at, u.id, u.username, u.email, u.profile_image
        ";
        
        $result = $this->db->query($query, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * إنشاء منشور جديد
     * 
     * @param array $data
     * @return bool|int
     */
    public function create(array $data)
    {
        // التحقق من المدخلات
        if (empty($data['title']) || empty($data['content']) || empty($data['user_id'])) {
            return false;
        }
        
        // تنظيف المدخلات
        $title = htmlspecialchars(trim($data['title']), ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars(trim($data['content']), ENT_QUOTES, 'UTF-8');
        $userId = (int)$data['user_id'];
        
        // إدراج المنشور
        $query = "INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())";
        $success = $this->db->execute($query, [$userId, $title, $content]);
        
        if ($success) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * ربط المنشور بالتصنيفات
     * 
     * @param int $postId
     * @param array $categoryIds
     * @return bool
     */
    public function attachCategories(int $postId, array $categoryIds): bool
    {
        if (empty($categoryIds)) {
            return true;
        }
        
        foreach ($categoryIds as $categoryId) {
            $query = "INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)";
            $this->db->execute($query, [$postId, (int)$categoryId]);
        }
        
        return true;
    }
    
    /**
     * إزالة جميع التصنيفات من المنشور
     * 
     * @param int $postId
     * @return bool
     */
    public function detachCategories(int $postId): bool
    {
        $query = "DELETE FROM post_categories WHERE post_id = ?";
        return $this->db->execute($query, [$postId]);
    }
    
    /**
     * تحديث منشور
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if (empty($data['title']) || empty($data['content'])) {
            return false;
        }
        
        $title = htmlspecialchars(trim($data['title']), ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars(trim($data['content']), ENT_QUOTES, 'UTF-8');
        
        $query = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
        return $this->db->execute($query, [$title, $content, $id]);
    }
    
    /**
     * حذف منشور
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM posts WHERE id = ?";
        return $this->db->execute($query, [$id]);
    }
    
    /**
     * جلب المنشورات مع ترقيم الصفحات
     * 
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllWithPagination(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        
        $query = "
            SELECT 
                p.id,
                p.title,
                p.content,
                p.created_at,
                u.id as user_id,
                u.username,
                GROUP_CONCAT(c.name SEPARATOR ', ') as categories
            FROM posts p
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN post_categories pc ON p.id = pc.post_id
            LEFT JOIN categories c ON pc.category_id = c.id
            GROUP BY p.id, p.title, p.content, p.created_at, u.id, u.username
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        return $this->db->query($query, [$limit, $offset]);
    }
    
    /**
     * عد إجمالي المنشورات
     * 
     * @return int
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM posts";
        $result = $this->db->query($query);
        return (int)($result[0]['total'] ?? 0);
    }
    
    /**
     * جلب التصنيفات المرتبطة بمنشور
     * 
     * @param int $postId
     * @return array
     */
    public function getCategories(int $postId): array
    {
        $query = "
            SELECT c.id, c.name
            FROM categories c
            INNER JOIN post_categories pc ON c.id = pc.category_id
            WHERE pc.post_id = ?
        ";
        
        return $this->db->query($query, [$postId]);
    }
}
