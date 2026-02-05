<?php
$title = 'تعديل الملف الشخصي';
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_header.php';
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>

<div class="profile-container">
    <div class="profile-card">
        <h1>تعديل الملف الشخصي</h1>
        
        <form method="POST" action="<?= url('profile/update') ?>" enctype="multipart/form-data" class="profile-form">
            <div class="form-group">
                <label for="username">اسم المستخدم</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    value="<?= htmlspecialchars($user['username']) ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    value="<?= htmlspecialchars($user['email']) ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="profile_image">الصورة الشخصية</label>
                <?php if (!empty($user['profile_image'])): ?>
                    <div class="current-image">
                        <img 
                            src="<?= asset('uploads/profiles/' . $user['profile_image']) ?>" 
                            alt="الصورة الحالية"
                            style="max-width: 150px; border-radius: 8px;"
                        >
                    </div>
                <?php endif; ?>
                <input 
                    type="file" 
                    id="profile_image" 
                    name="profile_image" 
                    accept="image/jpeg,image/png,image/gif,image/webp"
                >
                <small>الحد الأقصى: 2MB - الأنواع المسموحة: JPG, PNG, GIF, WEBP</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="<?= url('profile') ?>" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<?php 
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_footer.php';
} else {
    require_once __DIR__ . '/../layouts/footer.php';
}
?>
