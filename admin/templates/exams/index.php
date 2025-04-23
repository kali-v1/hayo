<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة الاختبارات</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الاختبارات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة الاختبارات</h5>
            <a href="/admin/exams/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة اختبار جديد
            </a>
        </div>
        
        <!-- Search and Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="/admin/exams" method="get" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="exam-search" class="form-control" name="search" placeholder="بحث عن اختبار (بالعنوان فقط)..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" autocomplete="off">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="search-suggestions" class="position-absolute bg-white shadow rounded p-2 w-100 d-none" style="z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- الحالة --</option>
                            <option value="published" <?php echo (isset($_GET['status']) && $_GET['status'] === 'published') ? 'selected' : ''; ?>>منشور</option>
                            <option value="draft" <?php echo (isset($_GET['status']) && $_GET['status'] === 'draft') ? 'selected' : ''; ?>>مسودة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="is_free" class="form-select">
                            <option value="">-- النوع --</option>
                            <option value="1" <?php echo (isset($_GET['is_free']) && $_GET['is_free'] === '1') ? 'selected' : ''; ?>>مجاني</option>
                            <option value="0" <?php echo (isset($_GET['is_free']) && $_GET['is_free'] === '0') ? 'selected' : ''; ?>>مدفوع</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">تصفية</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table neo-table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>الدورات الموصى بها</th>
                            <th>المدة (دقيقة)</th>
                            <th>درجة النجاح</th>
                            <th>السعر</th>
                            <th>المحاولات</th>
                            <th>الحالة</th>
                            <th>مدخل البيانات</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exams)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="table-empty-state">
                                    <i class="fas fa-file-alt"></i>
                                    <h4>لا توجد اختبارات</h4>
                                    <p>لم يتم العثور على أي اختبارات في النظام. يمكنك إضافة اختبار جديد باستخدام زر "إضافة اختبار جديد".</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($exams as $index => $exam): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div>
                                        <?php echo htmlspecialchars($exam['title']); ?>
                                        <div class="text-muted small"><?php echo htmlspecialchars(substr($exam['description'], 0, 50) . (strlen($exam['description']) > 50 ? '...' : '')); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($exam['recommended_courses'])): ?>
                                        <?php 
                                            $courseNames = array_map(function($course) {
                                                return htmlspecialchars($course['title']);
                                            }, $exam['recommended_courses']);
                                            echo implode(', ', $courseNames);
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">لا توجد دورات موصى بها</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo isset($exam['duration']) ? $exam['duration'] : '30'; ?></td>
                                <td>
                                    <?php 
                                    if (isset($exam['pass_criteria_type']) && $exam['pass_criteria_type'] === 'percentage') {
                                        echo $exam['passing_score'] . '%';
                                    } else {
                                        echo $exam['passing_score'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (isset($exam['is_free']) && $exam['is_free']): ?>
                                    <span class="status-badge status-active">مجاني</span>
                                    <?php else: ?>
                                    <span class="fw-bold"><?php echo isset($exam['price']) ? number_format($exam['price'], 2) : '0.00'; ?> ر.س</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo isset($exam['attempts_count']) ? $exam['attempts_count'] : '0'; ?></span>
                                </td>
                                <td>
                                    <?php if (isset($exam['status']) && $exam['status'] === 'published'): ?>
                                    <span class="status-badge status-active">منشور</span>
                                    <?php else: ?>
                                    <span class="status-badge status-pending">مسودة</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($exam['creator_name'])): ?>
                                        <span title="<?php echo htmlspecialchars($exam['creator_username']); ?>"><?php echo htmlspecialchars($exam['creator_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">غير معروف</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($exam['created_at'])); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/admin/exams/view/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                                        <a href="/admin/exams/edit/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="/admin/questions?exam_id=<?php echo $exam['id']; ?>" class="neo-btn neo-btn-sm neo-btn-primary" title="الأسئلة">
                                            <i class="fas fa-question-circle"></i>
                                        </a>
                                        <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                                        <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $exam['id']; ?>" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Delete Modals -->
            <?php if (!empty($exams)): ?>
                <?php foreach ($exams as $exam): ?>
                    <!-- Delete Modal for Exam <?php echo $exam['id']; ?> -->
                    <div class="modal fade" id="deleteModal<?php echo $exam['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $exam['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content neo-card">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $exam['id']; ?>">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    هل أنت متأكد من حذف الاختبار <strong><?php echo htmlspecialchars($exam['title']); ?></strong>؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <a href="/admin/exams/delete/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if (isset($totalExams) && $totalExams > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <p class="mb-0">عرض <?php echo count($exams); ?> من <?php echo $totalExams; ?> اختبار</p>
                </div>
                <nav aria-label="Page navigation">
                    <?php
                    $totalPages = ceil($totalExams / $perPage);
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    
                    // Build the query string for pagination links
                    $queryParams = $_GET;
                    unset($queryParams['page']); // Remove the page parameter
                    $queryString = http_build_query($queryParams);
                    $queryString = !empty($queryString) ? '&' . $queryString : '';
                    ?>
                    
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($currentPage - 1) . $queryString; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php
                        // Show a limited number of page links
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        // Always show first page
                        if ($startPage > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1' . $queryString . '">1</a></li>';
                            if ($startPage > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        
                        // Show page links
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $activeClass = ($i === $currentPage) ? 'active' : '';
                            echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=' . $i . $queryString . '">' . $i . '</a></li>';
                        }
                        
                        // Always show last page
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . $queryString . '">' . $totalPages . '</a></li>';
                        }
                        ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($currentPage + 1) . $queryString; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Add jQuery UI for autocomplete if not already included -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    const searchInput = $('#exam-search');
    const suggestionsContainer = $('#search-suggestions');
    
    let typingTimer;
    const doneTypingInterval = 300; // milliseconds
    
    // On keyup, start the countdown
    searchInput.on('keyup', function() {
        clearTimeout(typingTimer);
        const searchTerm = $(this).val().trim();
        
        if (searchTerm.length < 2) {
            suggestionsContainer.addClass('d-none').html('');
            return;
        }
        
        typingTimer = setTimeout(function() {
            fetchSuggestions(searchTerm);
        }, doneTypingInterval);
    });
    
    // On keydown, clear the countdown
    searchInput.on('keydown', function() {
        clearTimeout(typingTimer);
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#exam-search, #search-suggestions').length) {
            suggestionsContainer.addClass('d-none');
        }
    });
    
    // Function to fetch suggestions
    function fetchSuggestions(term) {
        $.ajax({
            url: '/admin/exams/search-suggestions',
            method: 'GET',
            data: { term: term },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(item) {
                        const priceDisplay = item.is_free == 1 ? 
                            '<span class="badge bg-success">مجاني</span>' : 
                            `<span class="badge bg-info">${item.price} ر.س</span>`;
                            
                        const passScoreDisplay = item.pass_criteria_type === 'percentage' ?
                            `${item.passing_score}%` :
                            `${item.passing_score} درجة`;
                            
                        html += `<div class="suggestion-item p-2 border-bottom" data-value="${item.value}">
                                    <div class="fw-bold">${item.value}</div>
                                    <div class="small text-muted">معيار النجاح: ${passScoreDisplay}</div>
                                    <div class="small">${priceDisplay}</div>
                                </div>`;
                    });
                    suggestionsContainer.html(html).removeClass('d-none');
                    
                    // Handle suggestion click
                    $('.suggestion-item').on('click', function() {
                        searchInput.val($(this).data('value'));
                        suggestionsContainer.addClass('d-none');
                        searchInput.closest('form').submit();
                    });
                } else {
                    suggestionsContainer.addClass('d-none').html('');
                }
            },
            error: function() {
                suggestionsContainer.addClass('d-none').html('');
            }
        });
    }
});
</script>
