<?php
/**
 * صفحة عرض منشور محدد - مثال على استخدام JOIN
 * لاحظ: لا يوجد أي استعلامات SQL في هذا الملف
 */

$title = $post['title'] ?? 'عرض الروايات';
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_header.php';
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>


<div class="post-detail-container">
    <article class="post-detail">
        <header class="post-detail-header">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            
            <div class="post-meta">
                <div class="post-author-info">
                    <?php if (!empty($post['profile_image'])): ?>
                        <img 
                            src="<?= asset('uploads/profiles/' . htmlspecialchars($post['profile_image'])) ?>" 
                            alt="<?= htmlspecialchars($post['username']) ?>"
                            class="author-avatar-large"
                        >
                    <?php else: ?>
                        <div class="author-avatar-placeholder-large">
                            <?= strtoupper(substr($post['username'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <strong><?= htmlspecialchars($post['username']) ?></strong>
                        <br>
                        <small><?= htmlspecialchars($post['email']) ?></small>
                        <br>
                        <time><?= htmlspecialchars($post['created_at']) ?></time>
                    </div>
                </div>
                
                <?php if (!empty($post['categories'])): ?>
                    <div class="post-categories-detail">
                        <strong>التصنيفات:</strong>
                        <div class="categories-tags">
                            <?php
                            $categories = explode(', ', $post['categories']);
                            foreach ($categories as $category):
                            ?>
                                <span class="category-tag"><?= htmlspecialchars($category) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </header>
        
        <div class="post-detail-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
        
        <footer class="post-detail-footer">
            <a href="<?= url('posts') ?>" class="btn btn-secondary">العودة </a>
        </footer>
    </article>

    <hr class="my-5">

    <!-- قسم التعليقات -->
    <section class="comments-section mt-5">
        <h3 class="mb-4">التعليقات (<?= count($comments) ?>)</h3>

        <!-- نموذج إضافة تعليق -->
        <?php if (Core\Session::isLoggedIn()): ?>
            <div class="comment-form-card card mb-4">
                <div class="card-body">
                    <form method="POST" action="<?= url('comments/store') ?>">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <div class="form-group mb-3">
                            <label for="content" class="form-label">أضف تعليقك</label>
                            <textarea id="content" name="content" class="form-control" rows="3" required placeholder="اكتب تعليقك هنا..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">إرسال التعليق</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted mb-4">يجب عليك <a href="<?= url('login') ?>">تسجيل الدخول</a> للتعليق.</p>
        <?php endif; ?>

        <!-- قائمة التعليقات -->
        <?php if (empty($comments)): ?>
            <p class="text-muted">لا توجد تعليقات بعد. كن أول من يعلق!</p>
        <?php else: ?>
            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="user-avatar me-3" style="width: 40px; height: 40px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <?php if (!empty($comment['profile_image'])): ?>
                                        <img src="<?= asset('uploads/profiles/' . htmlspecialchars($comment['profile_image'])) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="material-icons" style="font-size: 24px;">person</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($comment['username']) ?></h6>
                                    <small class="text-muted"><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></small>
                                </div>
                            </div>
                            <div class="comment-content">
                                <p class="mb-0"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<a href="<?= url('logout') ?>">logout</a>

<?php 
if (Core\Session::isAdmin()) {
    require_once __DIR__ . '/../layouts/admin_footer.php';
} else {
    require_once __DIR__ . '/../layouts/footer.php';
}
?>
