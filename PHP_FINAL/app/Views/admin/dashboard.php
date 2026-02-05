<?php
$pageTitle = 'لوحة التحكم الرئيسية';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="material-icons stat-icon">article</span>
        <div class="stat-value"><?= $stats['total_posts'] ?? 0 ?></div>
        <div class="stat-label">إجمالي الروايات</div>
    </div>
    
    <div class="stat-card accent">
        <span class="material-icons stat-icon">category</span>
        <div class="stat-value"><?= $stats['total_categories'] ?? 0 ?></div>
        <div class="stat-label">إجمالي التصنيفات</div>
    </div>

    <div class="stat-card info">
        <span class="material-icons stat-icon">comment</span>
        <div class="stat-value"><?= $stats['total_comments'] ?? 0 ?></div>
        <div class="stat-label">إجمالي التعليقات</div>
    </div>
    
    <div class="stat-card success">
        <span class="material-icons stat-icon">people</span>
        <div class="stat-value"><?= $stats['total_users'] ?? 0 ?></div>
        <div class="stat-label">إجمالي المستخدمين</div>
    </div>
</div>

<!-- Recent Posts -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">آخر الروايات</h2>
        <a href="<?= url('admin/posts') ?>" class="btn btn-primary btn-sm">
            <span class="material-icons">visibility</span>
            عرض الكل
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($recentPosts)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>الكاتب</th>
                            <th>التصنيفات</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPosts as $post): ?>
                            <tr>
                                <td><?= htmlspecialchars($post['title']) ?></td>
                                <td><?= htmlspecialchars($post['username']) ?></td>
                                <td>
                                    <?php if (!empty($post['categories'])): ?>
                                        <?php foreach (explode(', ', $post['categories']) as $category): ?>
                                            <span class="badge badge-primary"><?= htmlspecialchars($category) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">inbox</span>
                <h3>لا توجد روايات بعد</h3>
                <p>ابدأ بإنشاء رواية جديده</p>
                <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary">
                    <span class="material-icons">add</span>
                    إنشاء منشور
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="flex gap-2 mt-3">
    <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary btn-lg">
        <span class="material-icons">add</span>
        رواية جديده
    </a>
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-accent btn-lg">
        <span class="material-icons">add</span>
        تصنيف جديد
    </a>
</div>
<a href="<?= url('logout') ?>">logout</a>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
