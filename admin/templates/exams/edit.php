<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">تعديل الاختبار</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/exams">الاختبارات</a></li>
                    <li class="breadcrumb-item active">تعديل الاختبار</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card neo-card">
        <div class="card-header">
            <h5 class="card-title">معلومات الاختبار</h5>
        </div>
        <div class="card-body">
            <?php
            // Display errors if any
            if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                echo '<div class="alert alert-danger neo-alert-danger">';
                echo '<ul class="mb-0">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</div>';
                
                // Clear errors
                unset($_SESSION['errors']);
            }
            
            // Get form data if any
            $form_data = $_SESSION['form_data'] ?? $exam;
            unset($_SESSION['form_data']);
            ?>
            
            <form action="/admin/exams/update/<?php echo $exam['id']; ?>" method="post" class="neo-form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3 neo-form-group">
                            <label for="title" class="form-label">عنوان الاختبار <span class="text-danger">*</span></label>
                            <input type="text" class="form-control neo-form-control" id="title" name="title" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 neo-form-group">
                    <label for="description" class="form-label">وصف الاختبار <span class="text-danger">*</span></label>
                    <textarea class="form-control neo-form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3 neo-form-group">
                            <label for="duration" class="form-label">مدة الاختبار (دقيقة) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-form-control" id="duration" name="duration" min="1" value="<?php echo htmlspecialchars($form_data['duration'] ?? '60'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3 neo-form-group">
                            <label for="passing_score" class="form-label">درجة النجاح (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-form-control" id="passing_score" name="passing_score" min="0" max="100" value="<?php echo htmlspecialchars($form_data['passing_score'] ?? '70'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3 neo-form-group">
                            <label for="status" class="form-label">الحالة</label>
                            <?php
                            // Get current admin role
                            global $currentAdmin;
                            $adminRole = $currentAdmin['role'] ?? '';
                            
                            // For data entry users, show only a disabled field with "draft" status
                            if ($adminRole === 'data_entry'):
                            ?>
                                <input type="text" class="form-control neo-form-control" value="مسودة" disabled>
                                <input type="hidden" name="status" value="draft">
                                <small class="form-text text-muted">سيتم حفظ الاختبار كمسودة حتى يتم مراجعته من قبل المدير</small>
                            <?php else: ?>
                                <select class="form-select neo-form-select" id="status" name="status">
                                    <option value="draft" <?php echo (isset($form_data['status']) && $form_data['status'] === 'draft') ? 'selected' : ''; ?>>مسودة</option>
                                    <option value="published" <?php echo (isset($form_data['status']) && $form_data['status'] === 'published') ? 'selected' : ''; ?>>منشور</option>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 mb-4">
                    <h5>الدورات الموصى بها بعد الاختبار</h5>
                    <p class="text-muted">اختر الدورات التي ترغب في توصيتها للمستخدمين بعد إكمال هذا الاختبار.</p>
                    
                    <div class="row">
                        <div class="col-12">
                            <?php
                            // Get all courses
                            global $conn;
                            $courses = [];
                            $recommended_courses = [];
                            if ($conn) {
                                $stmt = $conn->prepare("
                                    SELECT id, title, company_url
                                    FROM courses
                                    WHERE is_published = 1
                                    ORDER BY title
                                ");
                                $stmt->execute();
                                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                // Get recommended courses for this exam
                                $stmt = $conn->prepare("
                                    SELECT course_id
                                    FROM exam_course_recommendations
                                    WHERE exam_id = ?
                                ");
                                $stmt->execute([$exam['id']]);
                                $recommended_courses = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            }
                            
                            if (!empty($courses)):
                            ?>
                            <select class="form-select neo-form-select select2-tags" id="recommended_courses" name="recommended_courses[]" multiple>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo in_array($course['id'], $recommended_courses) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                    <?php if (!empty($course['company_url'])): ?>
                                    (<?php echo htmlspecialchars($course['company_url']); ?>)
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                            <div class="alert alert-info">
                                لا توجد دورات متاحة للتوصية. يرجى إضافة دورات أولاً.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php
                // Add Select2 CSS and JS
                $extraStyles = $extraStyles ?? '';
                $extraStyles .= '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';
                
                $extraScripts = $extraScripts ?? '';
                $extraScripts .= '
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $(".select2-tags").select2({
                            dir: "rtl",
                            language: "ar",
                            placeholder: "اختر الدورات الموصى بها",
                            allowClear: true,
                            width: "100%",
                            tags: false
                        });
                    });
                </script>';
                ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <a href="/admin/exams" class="btn btn-secondary neo-btn-secondary">إلغاء</a>
                        <a href="/admin/questions?exam_id=<?php echo $exam['id']; ?>" class="btn btn-info neo-btn-info">
                            <i class="fas fa-question-circle"></i> إدارة الأسئلة
                        </a>
                    </div>
                    <button type="submit" class="btn btn-primary neo-btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>