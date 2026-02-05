<?php
$title = 'تسجيل الدخول';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>تسجيل الدخول</h1>
        
        <form method="POST" action="<?= url('login') ?>" class="auth-form">
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="أدخل بريدك الإلكتروني"
                >
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="أدخل كلمة المرور"
                >
            </div>
            
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
        </form>
        
        <p class="auth-link">
            ليس لديك حساب؟ <a href="<?= url('register') ?>">إنشاء حساب جديد</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
