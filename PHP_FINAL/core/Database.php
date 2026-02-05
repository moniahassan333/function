<?php

namespace Core;

use PDO;
use PDOException;

/**
 * كلاس Database باستخدام Singleton Pattern
 * يضمن وجود اتصال واحد فقط بقاعدة البيانات
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    
    /**
     * Constructor خاص لمنع إنشاء كائنات مباشرة
     */
    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
            
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }
    
    /**
     * منع استنساخ الكائن
     */
    private function __clone() {}
    
    /**
     * منع unserialize
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    /**
     * الحصول على instance الوحيد من Database
     * 
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        
        return self::$instance;
    }
    
    /**
     * الحصول على PDO connection
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    /**
     * تنفيذ استعلام SELECT مع Prepared Statement
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function query(string $query, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * تنفيذ استعلام INSERT/UPDATE/DELETE مع Prepared Statement
     * 
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function execute(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Execute Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * الحصول على ID آخر صف تم إدراجه
     * 
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }
}
