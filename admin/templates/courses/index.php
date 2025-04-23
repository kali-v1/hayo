<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة الدورات</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الدورات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة الدورات</h5>
            <a href="/admin/courses/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة دورة جديدة
            </a>
        </div>
        
        <!-- Search and Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="/admin/courses" method="get" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="course-search" class="form-control" name="search" placeholder="بحث عن دورة (اسم الدورة، اسم المدرب)..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" autocomplete="off">
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
                            <th>رابط الشركة</th>
                            <th>المسؤول</th>
                            <th>السعر</th>
                            <th>المشتركين</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="table-empty-state">
                                    <i class="fas fa-book"></i>
                                    <h4>لا توجد دورات</h4>
                                    <p>لم يتم العثور على أي دورات في النظام. يمكنك إضافة دورة جديدة باستخدام زر "إضافة دورة جديدة".</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $index => $course): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($course['image'])): ?>
                                        <div class="me-2">
                                            <img src="<?php echo $course['image']; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                            <div class="text-muted small"><?php echo htmlspecialchars($course['slug']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($course['company_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($course['company_url']); ?>" target="_blank" class="text-primary">
                                            <?php echo htmlspecialchars($course['company_url']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">غير محدد</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'غير محدد'); ?></td>
                                <td>
                                    <?php if ($course['is_free']): ?>
                                    <span class="status-badge status-active">مجاني</span>
                                    <?php else: ?>
                                    <span class="fw-bold"><?php echo number_format($course['price'], 2); ?> ر.س</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo isset($course['subscribers_count']) ? $course['subscribers_count'] : '0'; ?></span>
                                </td>
                                <td>
                                    <?php if (isset($course['status']) && $course['status'] === 'published'): ?>
                                    <span class="status-badge status-active">منشور</span>
                                    <?php else: ?>
                                    <span class="status-badge status-pending">مسودة</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($course['created_at'])); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/admin/courses/view/<?php echo $course['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/courses/edit/<?php echo $course['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $course['id']; ?>" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $course['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $course['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content neo-card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $course['id']; ?>">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف الدورة <strong><?php echo htmlspecialchars($course['title']); ?></strong>؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <a href="/admin/courses/delete/<?php echo $course['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($totalCourses) && $totalCourses > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <p class="mb-0">عرض <?php echo count($courses); ?> من <?php echo $totalCourses; ?> دورة</p>
                </div>
                <nav aria-label="Page navigation">
                    <?php
                    $totalPages = ceil($totalCourses / $perPage);
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
    const searchInput = $('#course-search');
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
        if (!$(e.target).closest('#course-search, #search-suggestions').length) {
            suggestionsContainer.addClass('d-none');
        }
    });
    
    // Function to fetch suggestions
    function fetchSuggestions(term) {
        $.ajax({
            url: '/admin/courses/search-suggestions',
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
                            
                        html += `<div class="suggestion-item p-2 border-bottom" data-value="${item.value}">
                                    <div class="fw-bold">${item.value}</div>
                                    <div class="small text-muted">المدرب: ${item.instructor || 'غير معروف'}</div>
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
