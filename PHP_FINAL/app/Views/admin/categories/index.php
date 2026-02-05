<?php
$pageTitle = 'إدارة التصنيفات';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">جميع التصنيفات</h2>
        <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary">
            <span class="material-icons">add</span>
            تصنيف جديد
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($categories)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>الوصف</th>
                            <th>عدد المنشورات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($category['name']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($category['description'] ?? 'لا يوجد وصف') ?></td>
                                <td>
                                    <span class="badge badge-primary"><?= $category['post_count'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?= url('admin/categories/edit?id=' . $category['id']) ?>" class="btn btn-sm btn-primary">
                                            <span class="material-icons">edit</span>
                                        </a>
                                        <form method="POST" action="<?= url('admin/categories/delete') ?>" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
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
        <?php else: ?>
            <div class="empty-state">
                <span class="material-icons">category</span>
                <h3>لا توجد تصنيفات بعد</h3>
                <p>ابدأ بإنشاء أول تصنيف لك</p>
                <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary">
                    <span class="material-icons">add</span>
                    إنشاء تصنيف
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- FAB Button -->
<a href="<?= url('admin/categories/create') ?>" class="fab">
    <span class="material-icons">add</span>
</a>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
