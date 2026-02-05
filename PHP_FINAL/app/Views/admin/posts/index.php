<?php
$pageTitle = 'إدارة المنشورات';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">جميع المنشورات</h2>
        <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary">
            <span class="material-icons">add</span>
            منشور جديد
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($posts)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>العنوان</th>
                            <th>الكاتب</th>
                            <th>التصنيفات</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?= $post['id'] ?></td>
                                <td><?= htmlspecialchars($post['title']) ?></td>
                                <td><?= htmlspecialchars($post['username']) ?></td>
                                <td>
                                    <?php if (!empty($post['categories'])): ?>
                                        <?php foreach (explode(', ', $post['categories']) as $category): ?>
                                            <span class="badge badge-primary"><?= htmlspecialchars($category) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge badge-warning">بدون تصنيف</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?= url('admin/posts/edit?id=' . $post['id']) ?>" class="btn btn-sm btn-primary">
                                            <span class="material-icons">edit</span>
                                        </a>
                                        <form method="POST" action="<?= url('admin/posts/delete') ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنشور؟')">
                                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <span class="material-icons">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="pagination-btn">
                            <span class="material-icons">chevron_right</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="pagination-btn">
                            <span class="material-icons">chevron_left</span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">inbox</span>
                <h3>لا توجد منشورات بعد</h3>
                <p>ابدأ بإنشاء أول منشور لك</p>
                <a href="<?= url('admin/posts/create') ?>" class="btn btn-primary">
                    <span class="material-icons">add</span>
                    إنشاء منشور
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- FAB Button -->
<a href="<?= url('admin/posts/create') ?>" class="fab">
    <span class="material-icons">add</span>
</a>
<a href="<?= url('logout') ?>">logout</a>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
