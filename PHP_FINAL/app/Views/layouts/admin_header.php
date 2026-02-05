<?php
use Core\Session;
$currentUser = Session::get('user_data');
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'لوحة التحكم' ?> - إدارة المحتوى</title>
    <link rel="stylesheet" href="<?= asset('fonts/roboto.css') ?>">
    <link rel="stylesheet" href="<?= asset('fonts/material-icons/material-icons.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>
                    <span class="material-icons">dashboard</span>
                    رواياتيي
                </h2>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('admin') ?>" class="sidebar-nav-link <?= $currentPath === '/admin' ? 'active' : '' ?>">
                            <span class="material-icons">home</span>
                            <span>الرئيسية</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-divider"></li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('admin/posts') ?>" class="sidebar-nav-link <?= str_contains($currentPath, '/admin/posts') ? 'active' : '' ?>">
                            <span class="material-icons">article</span>
                            <span>الروايات</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('admin/posts/create') ?>" class="sidebar-nav-link">
                            <span class="material-icons">add_circle</span>
                            <span>رواية جديده</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-divider"></li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('admin/categories') ?>" class="sidebar-nav-link <?= str_contains($currentPath, '/admin/categories') ? 'active' : '' ?>">
                            <span class="material-icons">category</span>
                            <span>التصنيفات</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('admin/categories/create') ?>" class="sidebar-nav-link">
                            <span class="material-icons">add_box</span>
                            <span>تصنيف جديد</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-divider"></li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('admin/comments') ?>" class="sidebar-nav-link <?= str_contains($currentPath, '/admin/comments') ? 'active' : '' ?>">
                            <span class="material-icons">comment</span>
                            <span>إدارة التعليقات</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-divider"></li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('profile') ?>" class="sidebar-nav-link">
                            <span class="material-icons">person</span>
                            <span>الملف الشخصي</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('posts') ?>" class="sidebar-nav-link">
                            <span class="material-icons">visibility</span>
                            <span>عرض الموقع</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-divider"></li>
                    
                    <li class="sidebar-nav-item">
                        <a href="<?= url('logout') ?>" class="sidebar-nav-link">
                            <span class="material-icons">logout</span>
                            <span>تسجيل الخروج</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="top-bar-title">
                    <h1><?= $pageTitle ?? 'لوحة التحكم' ?></h1>
                </div>
                
                <div class="top-bar-actions">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php if (!empty($currentUser['profile_image'])): ?>
                                <img src="<?= asset('uploads/profiles/' . $currentUser['profile_image']) ?>" alt="Profile">
                            <?php else: ?>
                                <?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <span><?= htmlspecialchars($currentUser['username'] ?? 'مستخدم') ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Content Container -->
            <div class="content-container">
                <?php
                // عرض رسائل Flash
                if (Session::hasFlash('success')):
                ?>
                    <div class="alert alert-success">
                        <span class="material-icons">check_circle</span>
                        <span><?= Session::getFlash('success') ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (Session::hasFlash('error')): ?>
                    <div class="alert alert-error">
                        <span class="material-icons">error</span>
                        <span><?= Session::getFlash('error') ?></span>
                    </div>
                <?php endif; ?>
