<?php
$pageTitle = 'تعديل الرواية';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">تعديل الرواية</h2>
        <a href="<?= url('admin/posts') ?>" class="btn btn-outline">
            <span class="material-icons">arrow_forward</span>
            رجوع
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/posts/update') ?>">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            
            <div class="form-group">
                <label for="title" class="form-label required">عنوان الرواية</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control" 
                    value="<?= htmlspecialchars($post['title']) ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="content" class="form-label required">محتوى الرواية</label>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-control" 
                    rows="10"
                    required
                ><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="categories" class="form-label">التصنيفات</label>
                <select id="categories" name="categories[]" class="form-control" multiple size="5">
                    <?php foreach ($categories as $category): ?>
                        <option 
                            value="<?= $category['id'] ?>"
                            <?= in_array($category['id'], $selectedCategoryIds) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-help">اضغط Ctrl (أو Cmd) لاختيار أكثر من تصنيف</span>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-icons">save</span>
                    حفظ التعديلات
                </button>
                <a href="<?= url('admin/posts') ?>" class="btn btn-outline btn-lg">
                    <span class="material-icons">cancel</span>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Delete Card -->
<div class="card mt-3">
    <div class="card-header">
        <h2 class="card-title">منطقة الخطر</h2>
    </div>
    <div class="card-body">
        <p style="color: var(--text-secondary); margin-bottom: 16px;">
            حذف هذا الرواية سيؤدي إلى إزالتها نهائياً من قاعدة البيانات. هذا الإجراء لا يمكن التراجع عنه.
        </p>
        <form method="POST" action="<?= url('admin/posts/delete') ?>" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنشور؟ لا يمكن التراجع عن هذا الإجراء.')">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <button type="submit" class="btn btn-danger">
                <span class="material-icons">delete_forever</span>
                حذف الرواية نهائياً
            </button>
        </form>
    </div>
</div>
<a href="<?= url('logout') ?>">logout</a>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
