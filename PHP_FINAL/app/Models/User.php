<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Model للمستخدمين
 */
class User
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * تسجيل مستخدم جديد
     * 
     * @param array $data
     * @return bool|int
     * 
     */
    public function register(array $data)
    {
        // التحقق من المدخلات
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return false;
        }
        

// منع الرموز الخاصة في اسم المستخدم
if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
    return false;
}

        
        // تنظيف المدخلات
        $username = htmlspecialchars(trim($data['username']), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        
        // التحقق من صحة البريد الإلكتروني
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // تشفير كلمة المرور
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // التحقق من عدم وجود المستخدم مسبقاً
        $query = "SELECT id FROM users WHERE email = ? OR username = ?";
        $result = $this->db->query($query, [$email, $username]);
        
        if (!empty($result)) {
            return false; // المستخدم موجود مسبقاً
        }
        
        // إدراج المستخدم الجديد
        $role = $data['role'] ?? 'user';
        $query = "INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        $success = $this->db->execute($query, [$username, $email, $password, $role]);
        
        if ($success) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * تسجيل دخول المستخدم
     * 
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function login(string $email, string $password)
    {
        // تنظيف المدخلات
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        
        if (empty($email) || empty($password)) {
            return false;
        }
        
        // البحث عن المستخدم
        $query = "SELECT * FROM users WHERE email = ?";
        $result = $this->db->query($query, [$email]);
        
        if (empty($result)) {
            return false;
        }
        
        $user = $result[0];
        
        // التحقق من كلمة المرور
        if (password_verify($password, $user['password'])) {
            // إزالة كلمة المرور من البيانات المرجعة
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * البحث عن مستخدم بواسطة ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $query = "SELECT id, username, email, role, profile_image, created_at FROM users WHERE id = ?";
        $result = $this->db->query($query, [$id]);
        
        return $result[0] ?? null;
    }

    


    
    /**
     * تحديث الملف الشخصي
     * 
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $userId, array $data): bool
    {
        // التحقق من المدخلات
        if (empty($data['username']) || empty($data['email'])) {
            return false;
        }
        
        // تنظيف المدخلات
        $username = htmlspecialchars(trim($data['username']), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        
        // التحقق من صحة البريد الإلكتروني
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // التحقق من عدم وجود مستخدم آخر بنفس البريد أو اسم المستخدم
        $query = "SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?";
        $result = $this->db->query($query, [$email, $username, $userId]);
        
        if (!empty($result)) {
            return false;
        }
        
        // تحديث البيانات
        $query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        return $this->db->execute($query, [$username, $email, $userId]);
    }
    
    /**
     * رفع صورة الملف الشخصي
     * 
     * @param int $userId
     * @param array $file
     * @return bool|string
     */
    public function uploadProfileImage(int $userId, array $file)
    {
        // التحقق من وجود ملف
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        
        // التحقق من عدم وجود أخطاء
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // التحقق من نوع الملف
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
        
        // التحقق من حجم الملف (2MB كحد أقصى)
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        // إنشاء اسم فريد للملف
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/profiles/' . $fileName;
        
        // نقل الملف
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // تحديث قاعدة البيانات
            $query = "UPDATE users SET profile_image = ? WHERE id = ?";
            $this->db->execute($query, [$fileName, $userId]);
            
            return $fileName;
        }
        
        return false;
    }
    
    /**
     * عد إجمالي المستخدمين
     * 
     * @return int
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM users";
        $result = $this->db->query($query);
        return (int)($result[0]['total'] ?? 0);
    }
}

