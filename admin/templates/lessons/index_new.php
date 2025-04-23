<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة دروس الدورة: <?php echo htmlspecialchars($course['title']); ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses">الدورات</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses/view/<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a></li>
                    <li class="breadcrumb-item active">الدروس</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-end mb-3">
        <a href="/admin/courses/<?php echo $course['id']; ?>/lessons/create" class="neo-btn neo-btn-primary">
            <i class="fas fa-plus"></i> إضافة درس جديد
        </a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="neo-alert neo-alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="neo-alert neo-alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="neo-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">إحصائيات الدروس</h5>
                </div>
                <div>
                    <div class="stats-row">
                        <div class="stats-item">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stats-info">
                                <h4><?php echo $totalLessons; ?></h4>
                                <p>إجمالي الدروس</p>
                            </div>
                        </div>
                    </div>
                    <div class="stats-row">
                        <div class="stats-item">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h4><?php echo $publishedLessons; ?></h4>
                                <p>الدروس المنشورة</p>
                            </div>
                        </div>
                    </div>
                    <div class="stats-row">
                        <div class="stats-item">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-info">
                                <h4><?php echo $totalDuration; ?></h4>
                                <p>إجمالي الدقائق</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="/admin/courses/view/<?php echo $course['id']; ?>" class="neo-btn neo-btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للدورة
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="neo-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">قائمة الدروس</h5>
                </div>
                <div>
                    <?php if (empty($lessons)): ?>
                        <div class="table-empty-state">
                            <i class="fas fa-book"></i>
                            <h4>لا توجد دروس</h4>
                            <p>لا توجد دروس لهذه الدورة. قم بإضافة درس جديد.</p>
                        </div>
                    <?php else: ?>
                        <div class="neo-alert neo-alert-info mb-3">
                            <i class="fas fa-info-circle"></i> يمكنك سحب وإفلات الصفوف من خلال عمود "الترتيب" لتغيير ترتيب الدروس، ثم اضغط على زر "حفظ الترتيب" لتأكيد التغييرات.
                        </div>
                        <div class="table-responsive">
                            <table class="neo-table neo-table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%" title="اسحب وأفلت لتغيير الترتيب">الترتيب</th>
                                        <th width="30%">عنوان الدرس</th>
                                        <th width="15%">المدة</th>
                                        <th width="10%">مجاني</th>
                                        <th width="15%">الحالة</th>
                                        <th width="25%">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-lessons">
                                    <?php foreach ($lessons as $index => $lesson): ?>
                                        <tr class="lesson-row" data-lesson-id="<?php echo $lesson['id']; ?>">
                                            <td class="handle">☰<?php echo $lesson['order_number']; ?></td>
                                            <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                                            <td><?php echo $lesson['duration']; ?> دقيقة</td>
                                            <td>
                                                <?php if ($lesson['is_free']): ?>
                                                    <span class="status-badge status-active">نعم</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-inactive">لا</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($lesson['status'] === 'published'): ?>
                                                    <span class="status-badge status-active">منشور</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-pending">مسودة</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="/admin/courses/<?php echo $course['id']; ?>/lessons/<?php echo $lesson['id']; ?>/edit" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="neo-btn neo-btn-sm <?php echo $lesson['attachment_count'] > 0 ? 'neo-btn-info' : 'neo-btn-outline-info'; ?> view-attachments position-relative" data-lesson-id="<?php echo $lesson['id']; ?>" title="عرض المرفقات">
                                                        <i class="fas fa-paperclip"></i>
                                                        <?php if ($lesson['attachment_count'] > 0): ?>
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                            <?php echo $lesson['attachment_count']; ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </button>
                                                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $lesson['id']; ?>" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $lesson['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $lesson['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content neo-card">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $lesson['id']; ?>">تأكيد الحذف</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                هل أنت متأكد من حذف الدرس <strong><?php echo htmlspecialchars($lesson['title']); ?></strong>؟
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="/admin/courses/<?php echo $course['id']; ?>/lessons/<?php echo $lesson['id']; ?>/delete" method="POST">
                                                                    <button type="submit" class="neo-btn neo-btn-danger">حذف</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button id="save-order" class="neo-btn neo-btn-primary" onclick="saveOrder()">
                                <i class="fas fa-save"></i> حفظ الترتيب
                            </button>
                            <script>
                                function saveOrder() {
                                    console.log("Save order function called directly");
                                    
                                    var lessonOrder = [];
                                    var rows = document.querySelectorAll("#sortable-lessons .lesson-row");
                                    
                                    rows.forEach(function(row) {
                                        lessonOrder.push(row.getAttribute("data-lesson-id"));
                                    });
                                    
                                    console.log("Lesson order:", lessonOrder);
                                    
                                    // Get the course ID from the URL
                                    var urlParts = window.location.pathname.split('/');
                                    var courseId = urlParts[3];
                                    
                                    // Show loading state
                                    var saveButton = document.getElementById("save-order");
                                    var originalText = saveButton.innerHTML;
                                    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
                                    saveButton.disabled = true;
                                    saveButton.classList.add('neo-btn-loading');
                                    
                                    // Send AJAX request using vanilla JavaScript
                                    var xhr = new XMLHttpRequest();
                                    xhr.open("POST", "/admin/courses/" + courseId + "/lessons/reorder", true);
                                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                    
                                    xhr.onreadystatechange = function() {
                                        if (xhr.readyState === 4) {
                                            if (xhr.status === 200) {
                                                console.log("Response:", xhr.responseText);
                                                
                                                try {
                                                    var result = JSON.parse(xhr.responseText);
                                                    
                                                    if (result.success) {
                                                        // Show success message
                                                        saveButton.innerHTML = '<i class="fas fa-check"></i> تم الحفظ';
                                                        saveButton.classList.remove('neo-btn-loading');
                                                        saveButton.classList.add('neo-btn-success');
                                                        setTimeout(function() {
                                                            saveButton.innerHTML = originalText;
                                                            saveButton.disabled = false;
                                                            saveButton.classList.remove('neo-btn-success');
                                                        }, 2000);
                                                        
                                                        // Show alert
                                                        alert(result.message);
                                                    } else {
                                                        // Reset button
                                                        saveButton.innerHTML = originalText;
                                                        saveButton.disabled = false;
                                                        saveButton.classList.remove('neo-btn-loading');
                                                        
                                                        // Show error
                                                        alert("حدث خطأ: " + result.message);
                                                    }
                                                } catch (e) {
                                                    console.error("Error parsing response:", e);
                                                    
                                                    // Reset button
                                                    saveButton.innerHTML = originalText;
                                                    saveButton.disabled = false;
                                                    saveButton.classList.remove('neo-btn-loading');
                                                    
                                                    alert("حدث خطأ أثناء معالجة الاستجابة");
                                                }
                                            } else {
                                                console.error("AJAX error:", xhr.status);
                                                
                                                // Reset button
                                                saveButton.innerHTML = originalText;
                                                saveButton.disabled = false;
                                                saveButton.classList.remove('neo-btn-loading');
                                                
                                                alert("حدث خطأ أثناء حفظ الترتيب");
                                            }
                                        }
                                    };
                                    
                                    xhr.send("lesson_order=" + encodeURIComponent(JSON.stringify(lessonOrder)));
                                    
                                    return false;
                                }
                            </script>
                            <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> بعد الانتهاء من ترتيب الدروس، اضغط على زر "حفظ الترتيب" لتأكيد التغييرات</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Drag and drop styles -->
<style>
    /* These styles will be applied to the sortable table */
    #sortable-lessons .lesson-row {
        cursor: move;
        transition: all 0.2s ease;
    }
    #sortable-lessons .lesson-row:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    #sortable-lessons .handle {
        position: relative;
        cursor: grab;
        user-select: none;
        text-align: center;
        width: 40px;
        background-color: #f0f4f8;
        border-radius: 4px;
        font-weight: bold;
        color: #007bff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    #sortable-lessons .handle:hover {
        background-color: #e2e6ea;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f0f4f8;
        box-shadow: 0 0 10px rgba(0,123,255,0.2);
    }
    .sortable-chosen {
        background-color: #f0f4f8;
        box-shadow: 0 0 15px rgba(0,123,255,0.3);
    }
    .sortable-drag {
        opacity: 0.8;
        background-color: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        border-radius: 4px;
    }
    /* Style for table actions */
    .table-actions {
        display: flex;
        gap: 5px;
    }
</style>

<!-- Attachments Modal -->
<div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content neo-card">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="attachmentsModalLabel">
                    <i class="fas fa-paperclip me-2"></i> مرفقات الدرس: <span id="lesson-title-in-modal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="attachments-container">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-3">جاري تحميل المرفقات...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- Sortable initialization script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM loaded, initializing Sortable");
        
        var sortableList = document.getElementById('sortable-lessons');
        
        if (!sortableList) {
            console.error("Sortable list not found!");
            return;
        }
        
        console.log("Sortable list found:", sortableList);
        
        // Initialize Sortable
        try {
            var sortable = new Sortable(sortableList, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    console.log("Drag ended", evt);
                    // Update order numbers
                    updateOrderNumbers();
                }
            });
            
            console.log("Sortable initialized successfully");
        } catch (e) {
            console.error("Error initializing Sortable:", e);
        }
        
        // Function to update order numbers
        function updateOrderNumbers() {
            console.log("Updating order numbers");
            var rows = sortableList.querySelectorAll('tr');
            rows.forEach(function(row, index) {
                var handleCell = row.querySelector('.handle');
                if (handleCell) {
                    // Keep the hamburger icon
                    handleCell.innerHTML = '☰' + (index + 1);
                }
            });
        }
        
        // Save order button
        document.getElementById('save-order').addEventListener('click', function() {
            saveOrder();
        });
        
        // Handle view attachments button click
        document.querySelectorAll('.view-attachments').forEach(button => {
            button.addEventListener('click', function() {
                const lessonId = this.dataset.lessonId;
                const attachmentsContainer = document.getElementById('attachments-container');
                const lessonTitleElement = this.closest('tr').querySelector('td:nth-child(2)');
                const lessonTitle = lessonTitleElement ? lessonTitleElement.textContent.trim() : 'غير معروف';
                
                // Set lesson title in modal
                document.getElementById('lesson-title-in-modal').textContent = lessonTitle;
                
                // Show loading state
                attachmentsContainer.innerHTML = `
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-3">جاري تحميل المرفقات...</p>
                    </div>
                `;
                
                // Show the modal
                var attachmentsModal = new bootstrap.Modal(document.getElementById('attachmentsModal'));
                attachmentsModal.show();
                
                // Get the course ID from the URL
                var urlParts = window.location.pathname.split('/');
                var courseId = urlParts[3];
                
                // Fetch attachments data
                fetch(`/admin/courses/${courseId}/lessons/${lessonId}/attachments`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.attachments && data.attachments.length > 0) {
                            let html = `
                                <div class="table-responsive">
                                    <table class="neo-table neo-table-striped">
                                        <thead>
                                            <tr>
                                                <th width="40%">اسم الملف</th>
                                                <th width="20%">النوع</th>
                                                <th width="15%">الحجم</th>
                                                <th width="25%">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            data.attachments.forEach(attachment => {
                                let icon = 'fa-file';
                                let colorClass = 'text-secondary';
                                
                                if (attachment.file_type.includes('pdf')) {
                                    icon = 'fa-file-pdf';
                                    colorClass = 'text-danger';
                                } else if (attachment.file_type.includes('word') || attachment.file_type.includes('doc')) {
                                    icon = 'fa-file-word';
                                    colorClass = 'text-primary';
                                } else if (attachment.file_type.includes('excel') || attachment.file_type.includes('sheet') || attachment.file_type.includes('xls')) {
                                    icon = 'fa-file-excel';
                                    colorClass = 'text-success';
                                } else if (attachment.file_type.includes('powerpoint') || attachment.file_type.includes('presentation') || attachment.file_type.includes('ppt')) {
                                    icon = 'fa-file-powerpoint';
                                    colorClass = 'text-warning';
                                } else if (attachment.file_type.includes('zip') || attachment.file_type.includes('archive') || attachment.file_type.includes('rar')) {
                                    icon = 'fa-file-archive';
                                    colorClass = 'text-info';
                                } else if (attachment.file_type.includes('image') || attachment.file_type.includes('png') || attachment.file_type.includes('jpg') || attachment.file_type.includes('jpeg')) {
                                    icon = 'fa-file-image';
                                    colorClass = 'text-success';
                                } else if (attachment.file_type.includes('video')) {
                                    icon = 'fa-file-video';
                                    colorClass = 'text-danger';
                                } else if (attachment.file_type.includes('audio')) {
                                    icon = 'fa-file-audio';
                                    colorClass = 'text-info';
                                } else if (attachment.file_type.includes('text')) {
                                    icon = 'fa-file-alt';
                                    colorClass = 'text-secondary';
                                }
                                
                                // Format file size
                                let fileSize;
                                if (attachment.file_size > 1024 * 1024) {
                                    fileSize = (Math.round(attachment.file_size / (1024 * 1024) * 100) / 100) + ' MB';
                                } else {
                                    fileSize = (Math.round(attachment.file_size / 1024 * 100) / 100) + ' KB';
                                }
                                
                                // Format date
                                const date = new Date(attachment.created_at);
                                const formattedDate = date.toLocaleDateString('ar-SA', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                });
                                
                                html += `
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas ${icon} fa-2x ${colorClass}"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">${attachment.title}</div>
                                                    <small class="text-muted">${formattedDate}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${attachment.file_type}</td>
                                        <td>${fileSize}</td>
                                        <td>
                                            <a href="${attachment.file_path}" class="neo-btn neo-btn-sm neo-btn-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i> تحميل
                                            </a>
                                            <a href="${attachment.file_path}" class="neo-btn neo-btn-sm neo-btn-info" target="_blank">
                                                <i class="fas fa-eye me-1"></i> عرض
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                            
                            attachmentsContainer.innerHTML = html;
                        } else {
                            attachmentsContainer.innerHTML = `
                                <div class="alert alert-info text-center p-5">
                                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                                    <p class="mb-0">لا توجد مرفقات لهذا الدرس.</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching attachments:', error);
                        attachmentsContainer.innerHTML = `
                            <div class="alert alert-danger text-center p-5">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <p class="mb-0">حدث خطأ أثناء تحميل المرفقات. يرجى المحاولة مرة أخرى.</p>
                                <button class="neo-btn neo-btn-danger mt-3 retry-load-attachments">
                                    <i class="fas fa-sync-alt me-1"></i> إعادة المحاولة
                                </button>
                            </div>
                        `;
                        
                        // Add event listener to retry button
                        setTimeout(() => {
                            const retryButton = document.querySelector('.retry-load-attachments');
                            if (retryButton) {
                                retryButton.addEventListener('click', () => {
                                    this.click(); // Trigger the click event on the original button
                                });
                            }
                        }, 100);
                    });
            });
        });
        
        function saveOrder() {
            console.log("Save order function called");
            
            var lessonOrder = [];
            var rows = document.querySelectorAll("#sortable-lessons tr");
            
            rows.forEach(function(row) {
                lessonOrder.push(row.getAttribute("data-lesson-id"));
            });
            
            console.log("Lesson order:", lessonOrder);
            
            // Get the course ID from the URL
            var urlParts = window.location.pathname.split('/');
            var courseId = urlParts[3];
            
            // Show loading state
            var saveButton = document.getElementById("save-order");
            var originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
            saveButton.disabled = true;
            
            // Send AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/admin/courses/" + courseId + "/lessons/reorder", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log("Response:", xhr.responseText);
                        
                        try {
                            var result = JSON.parse(xhr.responseText);
                            
                            if (result.success) {
                                // Show success message
                                saveButton.innerHTML = '<i class="fas fa-check"></i> تم الحفظ';
                                setTimeout(function() {
                                    saveButton.innerHTML = originalText;
                                    saveButton.disabled = false;
                                }, 2000);
                                
                                // Show alert
                                alert(result.message);
                            } else {
                                // Reset button
                                saveButton.innerHTML = originalText;
                                saveButton.disabled = false;
                                
                                // Show error
                                alert("حدث خطأ: " + result.message);
                            }
                        } catch (e) {
                            console.error("Error parsing response:", e);
                            
                            // Reset button
                            saveButton.innerHTML = originalText;
                            saveButton.disabled = false;
                            
                            alert("حدث خطأ أثناء معالجة الاستجابة");
                        }
                    } else {
                        console.error("AJAX error:", xhr.status);
                        
                        // Reset button
                        saveButton.innerHTML = originalText;
                        saveButton.disabled = false;
                        
                        alert("حدث خطأ أثناء حفظ الترتيب");
                    }
                }
            };
            
            xhr.send("lesson_order=" + encodeURIComponent(JSON.stringify(lessonOrder)));
        }
    });
</script>