<?php

namespace Core;

/**
 * كلاس Session لإدارة الجلسات
 */
class Session
{
    /**
     * بدء الجلسة
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * تعيين قيمة في الجلسة
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * الحصول على قيمة من الجلسة
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * التحقق من وجود قيمة في الجلسة
     * 
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * حذف قيمة من الجلسة
     * 
     * @param string $key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * إنهاء الجلسة بالكامل
     */
    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }
    
    /**
     * التحقق من تسجيل دخول المستخدم
     * 
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }
    
    /**
     * الحصول على معرف المستخدم المسجل
     * 
     * @return int|null
     */
    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }
    
    /**
     * تسجيل دخول المستخدم
     * 
     * @param int $userId
     * @param array $userData
     */
    public static function login(int $userId, array $userData = []): void
    {
        self::set('user_id', $userId);
        self::set('user_data', $userData);
    }
    
    /**
     * تسجيل خروج المستخدم
     */
    public static function logout(): void
    { self::start();

        // حذف كل بيانات الجلسة
        $_SESSION = [];

        // حذف كوكي الجلسة من المتصفح
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // تدمير الجلسة من السيرفر
        session_destroy();
    }
    
    /**
     * حماية الصفحة - إعادة توجيه إذا لم يكن مسجل دخول
     * 
     * @param string $redirectTo
     */
    public static function requireLogin(string $redirectTo = 'login'): void
    {
        if (!self::isLoggedIn()) {
            header("Location: " . url($redirectTo));
            exit;
        }
    }
    
    /**
     * تعيين رسالة flash
     * 
     * @param string $key
     * @param string $message
     */
    public static function setFlash(string $key, string $message): void
    {
        self::set("flash_$key", $message);
    }
    
    /**
     * الحصول على رسالة flash وحذفها
     * 
     * @param string $key
     * @return string|null
     */
    public static function getFlash(string $key): ?string
    {
        $message = self::get("flash_$key");
        self::remove("flash_$key");
        return $message;
    }
    
    /**
     * التحقق من وجود رسالة flash
     * 
     * @param string $key
     * @return bool
     */
    public static function hasFlash(string $key): bool
    {
        return self::has("flash_$key");
    }

    /**
     * التحقق ما إذا كان المستخدم الحالي هو مسؤول (Admin)
     * 
     * @return bool
     */
    public static function isAdmin(): bool
    {
        $userData = self::get('user_data');
        return isset($userData['role']) && $userData['role'] === 'admin';
    }
}
