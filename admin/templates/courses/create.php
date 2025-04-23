<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إضافة دورة جديدة</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses">الدورات</a></li>
                    <li class="breadcrumb-item active">إضافة دورة</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">معلومات الدورة</h5>
        </div>
        <div class="card-body">
            <?php
            // Display errors if any
            if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                echo '<div class="alert alert-danger">';
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
            
            <form action="/admin/courses/store" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدورة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden slug field - will be auto-generated -->
                <input type="hidden" id="slug" name="slug" value="<?php echo htmlspecialchars($form_data['slug'] ?? ''); ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_id" class="form-label">الشركة</label>
                            <select class="form-select" id="company_id" name="company_id">
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
                            <div class="form-text">اختر الشركة المرتبطة بالدورة</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_url" class="form-label">رابط الشركة</label>
                            <input type="url" class="form-control" id="company_url" name="company_url" value="<?php echo htmlspecialchars($form_data['company_url'] ?? ''); ?>">
                            <div class="form-text">أدخل رابط موقع الشركة المرتبطة بالدورة (مثال: cisco.com لدورة CCNA)</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">وصف الدورة <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="10" required><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                
                <?php
                // Get the current admin
                $adminAuth = new AdminAuth();
                $currentAdmin = $adminAuth->getCurrentAdmin();
                $isAdmin = $currentAdmin['role'] === 'admin';
                ?>
                
                <?php if ($isAdmin): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_id" class="form-label">المسؤول <span class="text-danger">*</span></label>
                            <select class="form-select" id="admin_id" name="admin_id" required>
                                <option value="">اختر المسؤول</option>
                                <?php foreach ($instructors as $instructor): ?>
                                <option value="<?php echo $instructor['id']; ?>" <?php echo (isset($form_data['admin_id']) && $form_data['admin_id'] == $instructor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($instructor['name'] . ' (' . $instructor['username'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?php echo (isset($form_data['status']) && $form_data['status'] === 'draft') ? 'selected' : ''; ?>>مسودة</option>
                                <option value="published" <?php echo (isset($form_data['status']) && $form_data['status'] === 'published') ? 'selected' : ''; ?>>منشور</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" <?php echo (isset($form_data['is_free'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_free">
                                    دورة مجانية
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">السعر (ر.س)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($form_data['price'] ?? '0.00'); ?>">
                            <div class="form-text">سيتم تجاهل هذا الحقل إذا كانت الدورة مجانية.</div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- For instructors, show a message about course approval process -->
                <div class="alert alert-info">
                    <p><strong>ملاحظة:</strong> عند إضافة دورة جديدة، ستكون في وضع المسودة حتى يتم مراجعتها واعتمادها من قبل المدير. سيقوم المدير بتحديد السعر وما إذا كانت الدورة مجانية أم لا.</p>
                </div>
                <!-- Hidden fields for instructors -->
                <input type="hidden" name="admin_id" value="<?php echo $currentAdmin['id']; ?>">
                <input type="hidden" name="status" value="draft">
                <input type="hidden" name="is_free" value="0">
                <input type="hidden" name="price" value="0">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="image" class="form-label">صورة الدورة</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">الحد الأقصى لحجم الملف: 2 ميجابايت. الأنواع المسموح بها: JPG, JPEG, PNG.</div>
                </div>
                
                <div class="mb-3">
                    <label for="attachments" class="form-label">مرفقات الدورة (سلايدات، ملفات PDF، الخ)</label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    <div class="form-text">يمكنك تحميل عدة ملفات مرة واحدة. الأنواع المسموح بها: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX, ZIP.</div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/courses" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle price field based on is_free checkbox
        const isFreeCheckbox = document.getElementById('is_free');
        const priceField = document.getElementById('price');
        
        function togglePriceField() {
            if (isFreeCheckbox.checked) {
                priceField.value = '0.00';
                priceField.disabled = true;
            } else {
                priceField.disabled = false;
            }
        }
        
        // Initial toggle
        togglePriceField();
        
        // Toggle on change
        isFreeCheckbox.addEventListener('change', togglePriceField);
        
        // Generate slug from title
        const titleField = document.getElementById('title');
        const slugField = document.getElementById('slug');
        
        titleField.addEventListener('blur', function() {
            if (slugField.value === '' && titleField.value !== '') {
                // Convert to lowercase, replace spaces and special chars with hyphens
                let slug = titleField.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-')     // Replace spaces with hyphens
                    .replace(/-+/g, '-');     // Replace multiple hyphens with single hyphen
                
                slugField.value = slug;
            }
        });
    });

    // Initialize CKEditor 4 with color support
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('description', {
                language: 'ar',
                contentsLangDirection: 'rtl',
                height: 300,
                toolbarGroups: [
                    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                    { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                    { name: 'forms', groups: [ 'forms' ] },
                    '/',
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                    { name: 'links', groups: [ 'links' ] },
                    { name: 'insert', groups: [ 'insert' ] },
                    '/',
                    { name: 'styles', groups: [ 'styles' ] },
                    { name: 'colors', groups: [ 'colors' ] },
                    { name: 'tools', groups: [ 'tools' ] },
                    { name: 'others', groups: [ 'others' ] },
                    { name: 'about', groups: [ 'about' ] }
                ],
                removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CopyFormatting,RemoveFormat,Outdent,Indent,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,ShowBlocks,About',
                colorButton_colors: '000000,4D4D4D,999999,E6E6E6,FFFFFF,E64C4C,E6994C,E6E64C,4CE64C,4C4CE6,994CE6',
                colorButton_enableMore: true,
                colorButton_foreStyle: {
                    element: 'span',
                    styles: { color: '#(color)' },
                    overrides: [ { element: 'font', attributes: { 'color': null } } ]
                },
                colorButton_backStyle: {
                    element: 'span',
                    styles: { 'background-color': '#(color)' }
                },
                font_names: 'Arial/Arial, Helvetica, sans-serif;' +
                    'Courier New/Courier New, Courier, monospace;' +
                    'Georgia/Georgia, serif;' +
                    'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
                    'Tahoma/Tahoma, Geneva, sans-serif;' +
                    'Times New Roman/Times New Roman, Times, serif;' +
                    'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
                    'Verdana/Verdana, Geneva, sans-serif',
                fontSize_sizes: '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px',
                format_tags: 'p;h1;h2;h3;h4;h5;h6;pre;address;div',
                removeDialogTabs: 'image:advanced;link:advanced',
                extraPlugins: 'colorbutton,font,justify,colordialog'
            });
            console.log('CKEditor 4 initialized successfully');
        } else {
            console.error('CKEDITOR is not defined. Make sure CKEditor script is loaded.');
        }
    });
</script>

