<?php
$pageTitle = 'إدارة التعليقات';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">إدارة التعليقات</h2>
    </div>
    <div class="card-body">
        <?php if (empty($comments)): ?>
            <div class="empty-state">
                <span class="material-icons">comment_bank</span>
                <p>لا يوجد تعليقات حالياً</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>المنشور</th>
                            <th>التعليق</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?= htmlspecialchars($comment['username']) ?></td>
                                <td><?= htmlspecialchars($comment['post_title']) ?></td>
                                <td><?= htmlspecialchars(substr($comment['content'], 0, 50)) . (strlen($comment['content']) > 50 ? '...' : '') ?></td>
                                <td><?= date('Y-m-d', strtotime($comment['created_at'])) ?></td>
                                <td>
                                    <form method="POST" action="<?= url('admin/comments/delete') ?>" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                                        <input type="hidden" name="id" value="<?= $comment['id'] ?>">
                                        <button type="submit" class="btn btn-icon btn-danger" title="حذف">
                                            <span class="material-icons">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
