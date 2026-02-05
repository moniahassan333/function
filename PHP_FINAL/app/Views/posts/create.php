<?php
$title = 'إنشاء رواية جديدة';
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_header.php';
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>

<div class="container py-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">إنشاء رواية جديده</h2>
        </div>
        
        <div class="card-body">
            <form method="POST" action="<?= url('posts/store') ?>">
                <div class="form-group mb-3">
                    <label for="title" class="form-label">عنوان الرواية</label>
                    <input type="text" id="title" name="title" class="form-control" required placeholder="أدخل عنوان المنشور">
                </div>
                
                <div class="form-group mb-3">
                    <label for="content" class="form-label">محتوى الرواية</label>
                    <textarea id="content" name="content" class="form-control" rows="8" required placeholder="اكتب محتوى المنشور هنا..."></textarea>
                </div>
                
                <div class="form-group mb-4">
                    <label for="categories" class="form-label">تصنيف الروايات</label>
                    <select id="categories" name="categories[]" class="form-control" multiple size="5">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">اضغط Ctrl لاختيار أكثر من تصنيف</small>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">نشر </button>
                    <a href="<?= url('posts') ?>" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_footer.php';
} else {
    require_once __DIR__ . '/../layouts/footer.php';
}
?>
