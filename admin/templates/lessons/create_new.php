<div class="container-fluid">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title">إضافة درس جديد</h1>
            <div>
                <a href="/admin/courses/<?php echo $course['id']; ?>/lessons" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة للدروس
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="/admin/courses">الدورات</a></li>
                <li class="breadcrumb-item"><a href="/admin/courses/view/<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a></li>
                <li class="breadcrumb-item"><a href="/admin/courses/<?php echo $course['id']; ?>/lessons">الدروس</a></li>
                <li class="breadcrumb-item active" aria-current="page">إضافة درس جديد</li>
            </ol>
        </nav>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="neo-alert neo-alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card neo-card">
                <div class="card-header">
                    <h5 class="card-title">معلومات الدرس</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/courses/<?php echo $course['id']; ?>/lessons/store" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">عنوان الدرس <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-3">
                                <label for="duration" class="form-label">المدة (بالدقائق)</label>
                                <input type="number" class="form-control" id="duration" name="duration" min="1" value="0">
                            </div>
                            <div class="col-md-3">
                                <label for="order_number" class="form-label">الترتيب</label>
                                <input type="number" class="form-control" id="order_number" name="order_number" min="1" value="<?php echo $nextOrder; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف مختصر</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">محتوى الدرس</label>
                            <textarea class="form-control" id="content" name="content" rows="10"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="video_url" class="form-label">رابط الفيديو</label>
                            <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">يمكنك إضافة رابط فيديو من YouTube أو Vimeo أو أي منصة أخرى</small>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_free" name="is_free">
                                    <label class="form-check-label" for="is_free">
                                        درس مجاني (متاح للجميع)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">حالة الدرس</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft">مسودة</option>
                                    <option value="published">منشور</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="attachments" class="form-label">مرفقات الدرس (أدوات، ملفات PDF، الخ)</label>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                            <div class="form-text">يمكنك تحميل عدة ملفات مرة واحدة. الأنواع المسموح بها: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX, ZIP.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/admin/courses/<?php echo $course['id']; ?>/lessons" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الدرس
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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