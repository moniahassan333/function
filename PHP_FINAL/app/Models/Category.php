<?php

namespace App\Models;

use Core\Database;

/**
 * Model للتصنيفات
 */
class Category
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * جلب جميع التصنيفات
     * 
     * @return array
     */
    public function getAll(): array
    {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->query($query);
    }
    
    /**
     * جلب تصنيف بواسطة ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $query = "SELECT * FROM categories WHERE id = ?";
        $result = $this->db->query($query, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * إنشاء تصنيف جديد
     * 
     * @param array $data
     * @return bool|int
     */
    public function create(array $data)
    {
        if (empty($data['name'])) {
            return false;
        }
        
        $name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $success = $this->db->execute($query, [$name, $description]);
        
        if ($success) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * تحديث تصنيف
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if (empty($data['name'])) {
            return false;
        }
        
        $name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        $query = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
        return $this->db->execute($query, [$name, $description, $id]);
    }
    
    /**
     * حذف تصنيف
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM categories WHERE id = ?";
        return $this->db->execute($query, [$id]);
    }
    
    /**
     * عد إجمالي التصنيفات
     * 
     * @return int
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM categories";
        $result = $this->db->query($query);
        return (int)($result[0]['total'] ?? 0);
    }
    
    /**
     * جلب التصنيفات مع عدد المنشورات
     * 
     * @return array
     */
    public function getAllWithPostCount(): array
    {
        $query = "
            SELECT 
                c.id,
                c.name,
                c.description,
                COUNT(pc.post_id) as post_count
            FROM categories c
            LEFT JOIN post_categories pc ON c.id = pc.category_id
            GROUP BY c.id, c.name, c.description
            ORDER BY c.name ASC
        ";
        
        return $this->db->query($query);
    }
}
