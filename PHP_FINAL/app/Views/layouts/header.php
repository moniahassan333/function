<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '' ?></title>
    <link rel="stylesheet" href="<?= asset('fonts/roboto.css') ?>">
    <link rel="stylesheet" href="<?= asset('fonts/material-icons/material-icons.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?= url('posts') ?>" class="logo"></a>
            <ul class="nav-links">
                <?php if (\Core\Session::isLoggedIn()): ?>
                    <li><a href="<?= url('posts') ?>">المنشورات</a></li>
                    <li><a href="<?= url('profile') ?>">الملف الشخصي</a></li>
                    <li><a href="<?= url('logout') ?>">تسجيل الخروج</a></li>
                <?php else: ?>
                    <li><a href="<?= url('login') ?>">تسجيل الدخول</a></li>
                    <li><a href="<?= url('register') ?>">إنشاء حساب</a></li>
                    
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <main class="container">
        <?php
        // عرض رسائل Flash
        $successMsg = \Core\Session::getFlash('success');
        $errorMsg = \Core\Session::getFlash('error');
        
        if ($successMsg):
        ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
