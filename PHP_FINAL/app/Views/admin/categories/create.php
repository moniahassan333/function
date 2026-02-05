<?php
$pageTitle = 'إنشاء تصنيف جديد';
require_once __DIR__ . '/../../layouts/admin_header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">إنشاء رواية جديده</h2>
        <a href="<?= url('admin/categories') ?>" class="btn btn-outline">
            <span class="material-icons">arrow_forward</span>
            رجوع
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/categories/store') ?>">
            <div class="form-group">
                <label for="name" class="form-label required">اسم التصنيف</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control" 
                    placeholder="مثال:رعب، علم, غموض"
                    required
                >
                <span class="form-help">اختر اسماً واضحاً ومميزاً للتصنيف</span>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">الوصف</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control" 
                    placeholder="وصف مختصر للتصنيف (اختياري)"
                    rows="4"
                ></textarea>
                <span class="form-help">أضف وصفاً يساعد المستخدمين على فهم محتوى هذا التصنيف</span>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-icons">save</span>
                    حفظ التصنيف
                </button>
                <a href="<?= url('admin/categories') ?>" class="btn btn-outline btn-lg">
                    <span class="material-icons">cancel</span>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/admin_footer.php'; ?>
