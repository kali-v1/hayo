<?php include_once __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">تعديل درس - <?php echo htmlspecialchars($course['title']); ?></h2>
        <a href="/admin/courses/<?php echo $course['id']; ?>/lessons" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة للدروس
        </a>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card neo-brutalism-card">
                <form action="/admin/courses/<?php echo $course['id']; ?>/lessons/<?php echo $lesson['id']; ?>/update" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">عنوان الدرس <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($lesson['title']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="duration" class="form-label">المدة (بالدقائق)</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="1" value="<?php echo $lesson['duration']; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="order_number" class="form-label">الترتيب</label>
                            <input type="number" class="form-control" id="order_number" name="order_number" min="1" value="<?php echo $lesson['order_number']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف مختصر</label>
                        <textarea class="form-control" id="description" name="description" rows="2"><?php echo htmlspecialchars($lesson['description']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">محتوى الدرس</label>
                        <textarea class="form-control" id="content" name="content" rows="10"><?php echo htmlspecialchars($lesson['content']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="video_url" class="form-label">رابط الفيديو</label>
                        <input type="url" class="form-control" id="video_url" name="video_url" value="<?php echo htmlspecialchars($lesson['video_url']); ?>" placeholder="https://www.youtube.com/watch?v=...">
                        <small class="text-muted">يمكنك إضافة رابط فيديو من YouTube أو Vimeo أو أي منصة أخرى</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" <?php echo $lesson['is_free'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_free">
                                    درس مجاني (متاح للجميع)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">حالة الدرس</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?php echo $lesson['status'] === 'draft' ? 'selected' : ''; ?>>مسودة</option>
                                <option value="published" <?php echo $lesson['status'] === 'published' ? 'selected' : ''; ?>>منشور</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/admin/courses/<?php echo $course['id']; ?>/lessons" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php if (!empty($lesson['video_url'])): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card neo-brutalism-card">
                <div class="card-header">
                    <h5 class="mb-0">معاينة الفيديو</h5>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9 lesson-video-preview">
                    <?php
                    $videoUrl = $lesson['video_url'];
                    $videoId = '';
                    
                    // Extract YouTube video ID
                    if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $videoUrl, $matches) || 
                        preg_match('/youtu\.be\/([^&]+)/', $videoUrl, $matches)) {
                        $videoId = $matches[1];
                        echo '<iframe src="https://www.youtube.com/embed/' . $videoId . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    } else {
                        echo '<div class="alert alert-info">لا يمكن عرض معاينة للفيديو. تأكد من أن الرابط صحيح.</div>';
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- TinyMCE temporarily disabled for troubleshooting -->
<!-- 
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        directionality: 'rtl',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        height: 400
    });
</script>
-->

<?php include_once __DIR__ . '/includes/footer.php'; ?>