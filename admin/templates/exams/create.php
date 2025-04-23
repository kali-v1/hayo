<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إضافة اختبار جديد</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/exams">الاختبارات</a></li>
                    <li class="breadcrumb-item active">إضافة اختبار</li>
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
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);
            ?>
            
            <form action="/admin/exams/store" method="post" class="neo-form">
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
                    <div class="col-md-6">
                        <div class="mb-3 neo-form-group">
                            <label for="company_id" class="form-label">الشركة</label>
                            <select class="form-select neo-form-select" id="company_id" name="company_id">
                                <option value="">اختر الشركة</option>
                                <?php 
                                // Get companies for dropdown
                                $companyObj = new Company($conn);
                                $companies = $companyObj->getAllForDropdown();
                                foreach ($companies as $company): 
                                ?>
                                <option value="<?php echo $company['id']; ?>" <?php echo (isset($form_data['company_id']) && $form_data['company_id'] == $company['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($company['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">اختر الشركة المرتبطة بالاختبار</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 neo-form-group">
                            <label for="company_url" class="form-label">رابط الشركة</label>
                            <input type="url" class="form-control neo-form-control" id="company_url" name="company_url" value="<?php echo htmlspecialchars($form_data['company_url'] ?? ''); ?>">
                            <div class="form-text">أدخل رابط موقع الشركة المرتبطة بالاختبار (مثال: cisco.com لاختبار CCNA)</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3 neo-form-group">
                            <label for="duration" class="form-label">مدة الاختبار (دقيقة) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-form-control" id="duration" name="duration" min="1" value="<?php echo htmlspecialchars($form_data['duration'] ?? '60'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3 neo-form-group">
                            <label for="pass_criteria_type" class="form-label">نوع معيار النجاح <span class="text-danger">*</span></label>
                            <select class="form-select neo-form-select" id="pass_criteria_type" name="pass_criteria_type">
                                <option value="percentage" <?php echo (isset($form_data['pass_criteria_type']) && $form_data['pass_criteria_type'] === 'percentage') ? 'selected' : ''; ?>>نسبة مئوية</option>
                                <option value="fixed_score" <?php echo (isset($form_data['pass_criteria_type']) && $form_data['pass_criteria_type'] === 'fixed_score') ? 'selected' : ''; ?>>درجة ثابتة</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3 neo-form-group">
                            <label for="passing_score" class="form-label">درجة النجاح <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-form-control" id="passing_score" name="passing_score" min="0" value="<?php echo htmlspecialchars($form_data['passing_score'] ?? '70'); ?>" required>
                            <small id="passing_score_help" class="form-text text-muted">
                                <span id="percentage_text">النسبة المئوية للنجاح (0-100)</span>
                                <span id="fixed_score_text" style="display: none;">الدرجة المطلوبة للنجاح</span>
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
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
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 neo-form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" <?php echo (isset($form_data['is_free']) && $form_data['is_free']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_free">
                                    اختبار مجاني
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3 neo-form-group">
                            <label for="price" class="form-label">السعر (ر.س)</label>
                            <input type="number" class="form-control neo-form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($form_data['price'] ?? '0.00'); ?>">
                            <div class="form-text">سيتم تجاهل هذا الحقل إذا كان الاختبار مجانيًا.</div>
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
                            $recommended_courses = $form_data['recommended_courses'] ?? [];
                            $all_courses = [];
                            if ($conn) {
                                $stmt = $conn->prepare("
                                    SELECT id, title, company_url
                                    FROM courses
                                    WHERE is_published = 1
                                    ORDER BY title
                                ");
                                $stmt->execute();
                                $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }
                            
                            if (!empty($all_courses)):
                            ?>
                            <select class="form-select neo-form-select select2-tags" id="recommended_courses" name="recommended_courses[]" multiple>
                                <?php foreach ($all_courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo (in_array($course['id'], $recommended_courses)) ? 'selected' : ''; ?>>
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
                        
                        // Toggle price field based on is_free checkbox
                        const isFreeCheckbox = $("#is_free");
                        const priceField = $("#price");
                        
                        function togglePriceField() {
                            if (isFreeCheckbox.is(":checked")) {
                                priceField.val("0.00");
                                priceField.prop("disabled", true);
                            } else {
                                priceField.prop("disabled", false);
                            }
                        }
                        
                        // Initial toggle
                        togglePriceField();
                        
                        // Toggle on change
                        isFreeCheckbox.on("change", togglePriceField);
                        
                        // Toggle passing score help text based on pass criteria type
                        const passCriteriaType = $("#pass_criteria_type");
                        const percentageText = $("#percentage_text");
                        const fixedScoreText = $("#fixed_score_text");
                        
                        function togglePassingScoreHelp() {
                            if (passCriteriaType.val() === "percentage") {
                                percentageText.show();
                                fixedScoreText.hide();
                                $("#passing_score").attr("max", "100");
                            } else {
                                percentageText.hide();
                                fixedScoreText.show();
                                $("#passing_score").removeAttr("max");
                            }
                        }
                        
                        // Initial toggle
                        togglePassingScoreHelp();
                        
                        // Toggle on change
                        passCriteriaType.on("change", togglePassingScoreHelp);
                    });
                </script>';
                ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <?php if ($adminRole === 'data_entry'): ?>
                    <a href="/admin/questions" class="btn btn-secondary neo-btn-secondary">إلغاء</a>
                    <?php else: ?>
                    <a href="/admin/exams" class="btn btn-secondary neo-btn-secondary">إلغاء</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary neo-btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>