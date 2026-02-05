<?php
$title = 'إنشاء حساب جديد';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>إنشاء حساب جديد</h1>
        
        <form method="POST" action="<?= url('register') ?>" class="auth-form">
            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    placeholder="أدخل اسم المستخدم"
                >
            </div>
            
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
                    minlength="6"
                    placeholder="أدخل كلمة المرور (6 أحرف على الأقل)"
                >
            </div>

            <div class="form-group">
                <label for="role">نوع الحساب (Role)</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="user">مستخدم عادي (User)</option>
                    <!-- <option value="Admin">مدير  (admin)</option> -->
                    
                </select>
                <span class="form-help">اختر نوع الحساب لتحديد الصلاحيات</span>
            </div>
            
            <button type="submit" class="btn btn-primary">إنشاء الحساب</button>
        </form>
        
        <p class="auth-link">
            لديك حساب بالفعل؟ <a href="<?= url('login') ?>">تسجيل الدخول</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
