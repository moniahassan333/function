<?php
/**
 * صفحة عرض المنشورات - مثال على استخدام JOIN
 * لاحظ: لا يوجد أي استعلامات SQL في هذا الملف
 * جميع البيانات تأتي من Controller
 */

$title = 'الروايات';
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_header.php';
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>

<div class="posts-container p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>المنشورات</h1>
        <?php if (!Core\Session::isAdmin() && Core\Session::isLoggedIn()): ?>
            <a href="<?= url('posts/create') ?>" class="btn btn-primary d-flex align-items-center gap-2">
                <span class="material-icons">add</span>
                أنشئ رواية جديده
            </a>
        <?php endif; ?>
    </div>
    
    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <p>لا توجد روايات حالياً</p>
        </div>
    <?php else: ?>
        <div class="posts-grid">
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <div class="post-header">
                        <div class="post-author">
                            <?php if (!empty($post['profile_image'])): ?>
                                <img 
                                    src="<?= asset('uploads/profiles/' . htmlspecialchars($post['profile_image'])) ?>" 
                                    alt="<?= htmlspecialchars($post['username']) ?>"
                                    class="author-avatar"
                                >
                            <?php else: ?>
                                <div class="author-avatar-placeholder">
                                    <?= strtoupper(substr($post['username'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="author-info">
                                <h3><?= htmlspecialchars($post['username']) ?></h3>
                                <time><?= htmlspecialchars($post['created_at']) ?></time>
                            </div>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <h2><?= htmlspecialchars($post['title']) ?></h2>
                        <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
                    </div>
                    
                    <?php if (!empty($post['categories'])): ?>
                        <div class="post-categories">
                            <strong>التصنيفات:</strong>
                            <span class="categories-list"><?= htmlspecialchars($post['categories']) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-footer">
                        <a href="<?= url('post?id=' . $post['id']) ?>" class="btn btn-secondary">قراءة المزيد</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php 
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_footer.php';
} else {
    require_once __DIR__ . '/../layouts/footer.php';
}
?>
