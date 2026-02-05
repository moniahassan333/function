<?php
/**
 * Autoloader للمشروع
 * يقوم بتحميل الكلاسات تلقائياً بناءً على Namespace
 */

// تحميل ملف دوال المساعدة
require_once __DIR__ . '/core/helpers.php';

spl_autoload_register(function ($class) {
    // تحويل namespace إلى مسار ملف
    // مثال: App\Controllers\AuthController -> app/Controllers/AuthController.php
    
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    // التحقق من أن الكلاس يبدأ بـ prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // إذا كان الكلاس من Core
        if (strpos($class, 'Core\\') === 0) {
            $relative_class = substr($class, 5); // إزالة 'Core\'
            $file = __DIR__ . '/core/' . str_replace('\\', '/', $relative_class) . '.php';
            
            if (file_exists($file)) {
                require $file;
            }
            return;
        }
        return;
    }
    
    // الحصول على اسم الكلاس النسبي
    $relative_class = substr($class, $len);
    
    // تحويل namespace إلى مسار ملف
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // إذا كان الملف موجود، قم بتحميله
    if (file_exists($file)) {
        require $file;
    }
});
