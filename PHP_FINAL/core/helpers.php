<?php

/**
 * دالة لتوليد الروابط بشكل صحيح مع المجلدات الفرعية
 */
function url(string $path = ''): string
{
    // الحصول على المسار الأساسي للمشروع
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = str_replace('\\', '/', dirname($scriptName));
    
    // إزالة public من المسار إذا كان موجوداً في النهاية (لأننا في index.php داخل public)
    if (str_ends_with($basePath, '/public')) {
        $basePath = substr($basePath, 0, -7);
    }
    
    // تنظيف المسار المطلوب
    $path = ltrim($path, '/');
    
    return rtrim($basePath, '/') . '/' . $path;
}

/**
 * دالة لتوليد روابط الأصول (CSS, JS, Images)
 */
function asset(string $path): string
{
    // الأصول دائماً داخل مجلد public
    // إذا كنت تستخدم Laragon مع .htaccess في الجذر، ستحتاج لإضافة public/
    // لكن بما أننا نوجه الطلبات لـ public، فالأصول تُطلب مباشرة
    
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = str_replace('\\', '/', dirname($scriptName));
    
    // تأكد من أننا نستخدم المسار الذي يؤدي إلى public
    if (!str_contains($basePath, '/public')) {
        // إذا كنا في الجذر (بسبب .htaccess)، يجب إضافة public
        return rtrim($basePath, '/') . '/public/' . ltrim($path, '/');
    }
    
    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}
