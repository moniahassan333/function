<?php
$pageTitle = 'إنشاء رواية جديد';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">إنشاء رواية جديده</h2>
        <a href="<?= url('admin/posts') ?>" class="btn btn-outline">
            <span class="material-icons">arrow_forward</span>
            رجوع
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/posts/store') ?>">
            <div class="form-group">
                <label for="title" class="form-label required">عنوان الرواية</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control" 
                    placeholder="أدخل عنوان الرواية"
                    required
                >
                <span class="form-help">اختر عنواناً واضحاً وجذاباً للرواية</span>
            </div>
            
            <div class="form-group">
                <label for="content" class="form-label required">محتوى الرواية</label>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-control" 
                    placeholder="اكتب محتوى الرواية هنا..."
                    rows="10"
                    required
                ></textarea>
                <span class="form-help">اكتب محتوى مفصل وقيم للقراء</span>
            </div>
            
            <div class="form-group">
                <label for="categories" class="form-label">التصنيفات</label>
                <select id="categories" name="categories[]" class="form-control" multiple size="5">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-help">اضغط Ctrl (أو Cmd) لاختيار أكثر من تصنيف</span>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-icons">save</span>
                    حفظ الرواية
                </button>
                <a href="<?= url('admin/posts') ?>" class="btn btn-outline btn-lg">
                    <span class="material-icons">cancel</span>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<a href="<?= url('logout') ?>">logout</a>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
