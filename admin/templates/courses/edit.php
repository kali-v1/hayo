<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">تعديل الدورة</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses">الدورات</a></li>
                    <li class="breadcrumb-item active">تعديل الدورة</li>
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
            $form_data = $_SESSION['form_data'] ?? $course;
            unset($_SESSION['form_data']);
            ?>
            
            <form action="/admin/courses/update/<?php echo $course['id']; ?>" method="post" enctype="multipart/form-data">
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
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" <?php echo (isset($form_data['is_free']) && $form_data['is_free']) ? 'checked' : ''; ?>>
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
                <!-- For instructors, show course status information -->
                <div class="alert alert-info">
                    <p><strong>حالة الدورة:</strong> 
                    <?php 
                    $statusText = 'غير معروف';
                    $statusClass = 'text-secondary';
                    
                    if (isset($form_data['status'])) {
                        if ($form_data['status'] === 'draft') {
                            $statusText = 'مسودة (في انتظار الموافقة)';
                            $statusClass = 'text-warning';
                        } else if ($form_data['status'] === 'published') {
                            $statusText = 'منشورة';
                            $statusClass = 'text-success';
                        }
                    }
                    ?>
                    <span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                    </p>
                    
                    <?php if (isset($form_data['status']) && $form_data['status'] === 'draft'): ?>
                    <p>سيتم مراجعة الدورة من قبل المدير قبل نشرها.</p>
                    <?php endif; ?>
                    
                    <?php if (isset($form_data['is_free']) && $form_data['is_free']): ?>
                    <p><strong>نوع الدورة:</strong> <span class="text-success">مجانية</span></p>
                    <?php else: ?>
                    <p><strong>نوع الدورة:</strong> <span class="text-primary">مدفوعة</span></p>
                    <p><strong>السعر:</strong> <?php echo htmlspecialchars($form_data['price'] ?? '0.00'); ?> $</p>
                    <?php endif; ?>
                </div>
                
                <!-- Hidden fields for instructors -->
                <input type="hidden" name="admin_id" value="<?php echo $form_data['admin_id']; ?>">
                <input type="hidden" name="status" value="<?php echo $form_data['status']; ?>">
                <input type="hidden" name="is_free" value="<?php echo $form_data['is_free']; ?>">
                <input type="hidden" name="price" value="<?php echo $form_data['price']; ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="image" class="form-label">صورة الدورة</label>
                    <?php if (!empty($course['image'])): ?>
                    <div class="mb-2">
                        <img src="<?php echo $course['image']; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-thumbnail" style="max-width: 200px;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image" value="1">
                            <label class="form-check-label" for="delete_image">
                                حذف الصورة الحالية
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">الحد الأقصى لحجم الملف: 2 ميجابايت. الأنواع المسموح بها: JPG, JPEG, PNG. اتركه فارغًا للاحتفاظ بالصورة الحالية.</div>
                </div>
                
                <div class="mb-3">
                    <label for="attachments" class="form-label">إضافة مرفقات جديدة</label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    <div class="form-text">يمكنك تحميل عدة ملفات مرة واحدة. الأنواع المسموح بها: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX, ZIP.</div>
                </div>

                <?php
                // Get course attachments
                $attachmentsStmt = $conn->prepare("
                    SELECT id, title, file_path, file_type, file_size
                    FROM course_attachments
                    WHERE course_id = ?
                    ORDER BY created_at DESC
                ");
                $attachmentsStmt->execute([$course['id']]);
                $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($attachments)):
                ?>
                <div class="mb-4">
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
                                    <td>
                                        <?php
                                        $icon = 'fa-file';
                                        if (strpos($attachment['file_type'], 'pdf') !== false) {
                                            $icon = 'fa-file-pdf';
                                        } elseif (strpos($attachment['file_type'], 'word') !== false || strpos($attachment['file_type'], 'doc') !== false) {
                                            $icon = 'fa-file-word';
                                        } elseif (strpos($attachment['file_type'], 'excel') !== false || strpos($attachment['file_type'], 'sheet') !== false || strpos($attachment['file_type'], 'xls') !== false) {
                                            $icon = 'fa-file-excel';
                                        } elseif (strpos($attachment['file_type'], 'powerpoint') !== false || strpos($attachment['file_type'], 'presentation') !== false || strpos($attachment['file_type'], 'ppt') !== false) {
                                            $icon = 'fa-file-powerpoint';
                                        } elseif (strpos($attachment['file_type'], 'zip') !== false || strpos($attachment['file_type'], 'archive') !== false) {
                                            $icon = 'fa-file-archive';
                                        }
                                        ?>
                                        <i class="fas <?php echo $icon; ?> me-2"></i>
                                        <?php echo htmlspecialchars($attachment['title']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($attachment['file_type']); ?></td>
                                    <td><?php echo formatFileSize($attachment['file_size']); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo htmlspecialchars($attachment['file_path']); ?>" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-attachment" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteAttachmentModal" 
                                                data-id="<?php echo $attachment['id']; ?>"
                                                onclick="setAttachmentId(<?php echo $attachment['id']; ?>)">
                                                <i class="fas fa-trash"></i>
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

                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/courses" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Debug function to check all modal elements
    function debugModals() {
        console.log('Debugging modals...');
        const deleteModal = document.getElementById('deleteAttachmentModal');
        console.log('Delete modal element:', deleteModal);
        
        const deleteForm = document.getElementById('deleteAttachmentForm');
        console.log('Delete form element:', deleteForm);
        
        const attachmentIdInput = document.getElementById('delete_attachment_id');
        console.log('Attachment ID input element:', attachmentIdInput);
        console.log('Current attachment ID value:', attachmentIdInput ? attachmentIdInput.value : 'not found');
    }
    
    // Function to set attachment ID directly
    function setAttachmentId(id) {
        console.log('setAttachmentId called with ID:', id);
        // Set the value immediately
        setTimeout(() => {
            const input = document.getElementById('delete_attachment_id');
            if (input) {
                input.value = id;
                console.log('Attachment ID set directly to:', id);
            } else {
                console.error('Could not find attachment_id input field');
            }
        }, 50);
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');
        debugModals();
        
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
        
        // Add event listener for modal shown event
        const deleteModal = document.getElementById('deleteAttachmentModal');
        if (deleteModal) {
            deleteModal.addEventListener('shown.bs.modal', function() {
                console.log('Modal shown event fired');
                debugModals();
            });
        }
        
        // Handle delete attachment button
        const deleteButtons = document.querySelectorAll('.delete-attachment');
        console.log('Found delete buttons:', deleteButtons.length);
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const attachmentId = this.getAttribute('data-id');
                console.log('Delete button clicked with attachment ID:', attachmentId);
                
                if (!attachmentId) {
                    console.error('No attachment ID found on button:', this);
                    alert('خطأ: لم يتم العثور على معرف المرفق');
                    return;
                }
                
                // Find the input field in the modal form
                const attachmentIdInput = document.getElementById('delete_attachment_id');
                
                if (attachmentIdInput) {
                    attachmentIdInput.value = attachmentId;
                    console.log('Attachment ID set to:', attachmentIdInput.value);
                } else {
                    console.error('Could not find attachment_id input field');
                    setTimeout(() => {
                        // Try again after a short delay
                        const retryInput = document.getElementById('delete_attachment_id');
                        if (retryInput) {
                            retryInput.value = attachmentId;
                            console.log('Attachment ID set on retry:', attachmentId);
                        } else {
                            console.error('Still could not find attachment_id input field after delay');
                        }
                    }, 100);
                }
            });
        });
    });
</script>

<!-- Delete Attachment Modal -->
<div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-labelledby="deleteAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAttachmentModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا المرفق؟ لا يمكن التراجع عن هذا الإجراء.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="/admin/courses/delete-attachment" method="post" id="deleteAttachmentForm" onsubmit="return validateDeleteForm()">
                    <input type="hidden" name="attachment_id" id="delete_attachment_id" value="">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
                
                <script>
                    function validateDeleteForm() {
                        const attachmentId = document.getElementById('delete_attachment_id').value;
                        console.log('Validating form submission with attachment_id:', attachmentId);
                        
                        if (!attachmentId || attachmentId === '' || attachmentId === '0') {
                            alert('خطأ: لم يتم تحديد المرفق بشكل صحيح');
                            return false;
                        }
                        return true;
                    }
                </script>

            </div>
        </div>
    </div>
</div>

<?php
// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<script>
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

<style>
    /* Fix CKEditor height and make it responsive */
    .ck-editor__editable {
        min-height: 300px;
        max-height: 600px;
        direction: rtl;
    }
    
    /* Fix RTL issues in CKEditor */
    .ck.ck-editor__editable:not(.ck-editor__nested-editable) {
        text-align: right;
    }
</style>