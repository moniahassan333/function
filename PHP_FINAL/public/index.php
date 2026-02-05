<?php
/**
 * نقطة الدخول الرئيسية للتطبيق
 */

// تحميل Autoloader
require_once __DIR__ . '/../autoload.php';

// بدء الجلسة
\Core\Session::start();

// إنشاء Router
$router = new \Core\Router();

// تعريف المسارات

// صفحة رئيسية - إعادة توجيه للمنشورات
$router->get('/', \App\Controllers\PostController::class, 'index');

// مسارات المصادقة
$router->get('/login', \App\Controllers\AuthController::class, 'showLogin');
$router->post('/login', \App\Controllers\AuthController::class, 'login');
$router->get('/register', \App\Controllers\AuthController::class, 'showRegister');
$router->post('/register', \App\Controllers\AuthController::class, 'register');
$router->get('/logout', \App\Controllers\AuthController::class, 'logout');

// مسارات الملف الشخصي
$router->get('/profile', \App\Controllers\ProfileController::class, 'show');
$router->get('/profile/edit', \App\Controllers\ProfileController::class, 'edit');
$router->post('/profile/update', \App\Controllers\ProfileController::class, 'update');

// مسارات المنشورات
$router->get('/posts', \App\Controllers\PostController::class, 'index');
$router->get('/posts/create', \App\Controllers\PostController::class, 'create');
$router->post('/posts/store', \App\Controllers\PostController::class, 'store');
$router->get('/post', \App\Controllers\PostController::class, 'show');

// مسارات التعليقات
$router->post('/comments/store', \App\Controllers\CommentController::class, 'store');

// مسارات لوحة التحكم الإدارية
$router->get('/admin', \App\Controllers\AdminController::class, 'index');

// مسارات إدارة المنشورات
$router->get('/admin/posts', \App\Controllers\PostAdminController::class, 'index');
$router->get('/admin/posts/create', \App\Controllers\PostAdminController::class, 'create');
$router->post('/admin/posts/store', \App\Controllers\PostAdminController::class, 'store');
$router->get('/admin/posts/edit', \App\Controllers\PostAdminController::class, 'edit');
$router->post('/admin/posts/update', \App\Controllers\PostAdminController::class, 'update');
$router->post('/admin/posts/delete', \App\Controllers\PostAdminController::class, 'delete');

// مسارات إدارة التصنيفات
$router->get('/admin/categories', \App\Controllers\CategoryAdminController::class, 'index');
$router->get('/admin/categories/create', \App\Controllers\CategoryAdminController::class, 'create');
$router->post('/admin/categories/store', \App\Controllers\CategoryAdminController::class, 'store');
$router->get('/admin/categories/edit', \App\Controllers\CategoryAdminController::class, 'edit');
$router->post('/admin/categories/update', \App\Controllers\CategoryAdminController::class, 'update');
$router->post('/admin/categories/delete', \App\Controllers\CategoryAdminController::class, 'delete');

// مسارات إدارة التعليقات
$router->get('/admin/comments', \App\Controllers\CommentAdminController::class, 'index');
$router->post('/admin/comments/delete', \App\Controllers\CommentAdminController::class, 'delete');

// تشغيل Router
$router->run();
