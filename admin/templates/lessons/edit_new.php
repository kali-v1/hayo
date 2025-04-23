<div class="container-fluid">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title">تعديل درس: <?php echo htmlspecialchars($lesson['title']); ?></h1>
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
                <li class="breadcrumb-item active" aria-current="page">تعديل درس</li>
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
                    <form action="/admin/courses/<?php echo $course['id']; ?>/lessons/<?php echo $lesson['id']; ?>/update" method="POST" enctype="multipart/form-data">
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
                        
                        <div class="mb-3">
                            <label for="attachments" class="form-label">إضافة مرفقات جديدة (أدوات، ملفات PDF، الخ)</label>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                            <div class="form-text">يمكنك تحميل عدة ملفات مرة واحدة. الأنواع المسموح بها: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX, ZIP.</div>
                        </div>
                        
                        <?php
                        // Get existing attachments
                        $attachmentsStmt = $this->db->prepare("
                            SELECT id, title, file_path, file_type, file_size
                            FROM lesson_attachments
                            WHERE lesson_id = ?
                            ORDER BY created_at DESC
                        ");
                        $attachmentsStmt->execute([$lesson['id']]);
                        $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($attachments)):
                        ?>
                        <div class="mb-3">
                            <label class="form-label">المرفقات الحالية</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>اسم الملف</th>
                                            <th>النوع</th>
                                            <th>الحجم</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attachments as $attachment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($attachment['title']); ?></td>
                                            <td><?php echo htmlspecialchars($attachment['file_type']); ?></td>
                                            <td><?php echo round($attachment['file_size'] / 1024, 2); ?> KB</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo htmlspecialchars($attachment['file_path']); ?>" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-download"></i> تحميل
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-attachment" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteLessonAttachmentModal" 
                                                        data-id="<?php echo $attachment['id']; ?>"
                                                        data-lesson-id="<?php echo $lesson['id']; ?>"
                                                        data-course-id="<?php echo $course['id']; ?>"
                                                        onclick="setLessonAttachmentId(<?php echo $attachment['id']; ?>, <?php echo $lesson['id']; ?>, <?php echo $course['id']; ?>)">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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
    </div>
    
    <?php if (!empty($lesson['video_url'])): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card neo-card">
                <div class="card-header">
                    <h5 class="card-title">معاينة الفيديو</h5>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
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

<!-- Modal for deleting lesson attachment -->
<div class="modal fade" id="deleteLessonAttachmentModal" tabindex="-1" aria-labelledby="deleteLessonAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLessonAttachmentModalLabel">تأكيد حذف المرفق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا المرفق؟ لا يمكن التراجع عن هذا الإجراء.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteLessonAttachmentForm" action="/admin/lessons/delete-attachment" method="POST" onsubmit="return validateLessonDeleteForm()">
                    <input type="hidden" id="delete_lesson_attachment_id" name="attachment_id" value="">
                    <input type="hidden" id="delete_lesson_id" name="lesson_id" value="">
                    <input type="hidden" id="delete_course_id" name="course_id" value="">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to set attachment ID directly
    function setLessonAttachmentId(attachmentId, lessonId, courseId) {
        console.log('setLessonAttachmentId called with ID:', attachmentId, 'lesson ID:', lessonId, 'course ID:', courseId);
        
        // Set the values immediately
        setTimeout(() => {
            const attachmentInput = document.getElementById('delete_lesson_attachment_id');
            const lessonInput = document.getElementById('delete_lesson_id');
            const courseInput = document.getElementById('delete_course_id');
            
            if (attachmentInput && lessonInput && courseInput) {
                attachmentInput.value = attachmentId;
                lessonInput.value = lessonId;
                courseInput.value = courseId;
                console.log('Attachment ID set to:', attachmentId);
                console.log('Lesson ID set to:', lessonId);
                console.log('Course ID set to:', courseId);
            } else {
                console.error('Could not find one or more input fields');
            }
        }, 50);
    }
    
    // Validate form before submission
    function validateLessonDeleteForm() {
        const attachmentId = document.getElementById('delete_lesson_attachment_id').value;
        const lessonId = document.getElementById('delete_lesson_id').value;
        const courseId = document.getElementById('delete_course_id').value;
        
        console.log('Validating form - attachment ID:', attachmentId, 'lesson ID:', lessonId, 'course ID:', courseId);
        
        if (!attachmentId || attachmentId <= 0) {
            alert('خطأ: لم يتم تحديد المرفق بشكل صحيح');
            return false;
        }
        
        if (!lessonId || lessonId <= 0) {
            alert('خطأ: لم يتم تحديد الدرس بشكل صحيح');
            return false;
        }
        
        if (!courseId || courseId <= 0) {
            alert('خطأ: لم يتم تحديد الدورة بشكل صحيح');
            return false;
        }
        
        return true;
    }
    
    // Add event listener for modal shown event
    document.getElementById('deleteLessonAttachmentModal').addEventListener('shown.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (button) {
            const attachmentId = button.getAttribute('data-id');
            const lessonId = button.getAttribute('data-lesson-id');
            const courseId = button.getAttribute('data-course-id');
            
            console.log('Modal shown event - attachment ID:', attachmentId, 'lesson ID:', lessonId, 'course ID:', courseId);
            
            // Set the values again to ensure they are set
            setLessonAttachmentId(attachmentId, lessonId, courseId);
        }
    });
</script>