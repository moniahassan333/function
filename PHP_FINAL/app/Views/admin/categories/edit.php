<?php
$pageTitle = 'تعديل التصنيف';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">تعديل التصنيف</h2>
        <a href="<?= url('admin/categories') ?>" class="btn btn-outline">
            <span class="material-icons">arrow_forward</span>
            رجوع
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/categories/update') ?>">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            
            <div class="form-group">
                <label for="name" class="form-label required">اسم التصنيف</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control" 
                    value="<?= htmlspecialchars($category['name']) ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">الوصف</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control" 
                    rows="4"
                ><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-icons">save</span>
                    حفظ التعديلات
                </button>
                <a href="<?= url('admin/categories') ?>" class="btn btn-outline btn-lg">
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
            حذف هذا التصنيف سيؤدي إلى إزالته من جميع المنشورات المرتبطة به. هذا الإجراء لا يمكن التراجع عنه.
        </p>
        <form method="POST" action="<?= url('admin/categories/delete') ?>" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ لا يمكن التراجع عن هذا الإجراء.')">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <button type="submit" class="btn btn-danger">
                <span class="material-icons">delete_forever</span>
                حذف التصنيف نهائياً
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
