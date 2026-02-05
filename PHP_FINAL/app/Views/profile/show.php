<?php
$title = 'الملف الشخصي';
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_header.php';
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>


<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <?php if (!empty($user['profile_image'])): ?>
                <img 
                    src="<?= asset('uploads/profiles/' . $user['profile_image']) ?>" 
                    alt="صورة الملف الشخصي"
                    class="profile-image"
                >
            <?php else: ?>
                <div class="profile-image-placeholder">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            
            <h1><?= htmlspecialchars($user['username']) ?></h1>
        </div>
        
        <div class="profile-info">
            <div class="info-item">
                <strong>البريد الإلكتروني:</strong>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>
            
            <div class="info-item">
                <strong>تاريخ التسجيل:</strong>
                <span><?= htmlspecialchars($user['created_at']) ?></span>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="<?= url('profile/edit') ?>" class="btn btn-primary">تعديل الملف الشخصي</a>
        </div>
    </div>
</div>
<a href="<?= url('logout') ?>">logout</a>

<?php 
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_footer.php';
} else {
    require_once __DIR__ . '/../layouts/footer.php';
}
?>
