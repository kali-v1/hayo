<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة المستخدمين</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">المستخدمين</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة المستخدمين</h5>
            <a href="/admin/users/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة مستخدم جديد
            </a>
        </div>
        
        <!-- Search and Filter Form -->
        <div class="mb-4">
            <form action="/admin/users" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="user-search" name="search" class="form-control" placeholder="بحث عن مستخدم (اسم المستخدم، البريد الإلكتروني، الاسم)..." value="<?php echo htmlspecialchars($search ?? ''); ?>" autocomplete="off">
                        <button type="submit" class="neo-btn neo-btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                    <div id="search-suggestions" class="position-absolute bg-white shadow rounded p-2 w-100 d-none" style="z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">جميع الحالات</option>
                        <option value="active" <?php echo ($statusFilter === 'active') ? 'selected' : ''; ?>>نشط</option>
                        <option value="inactive" <?php echo ($statusFilter === 'inactive') ? 'selected' : ''; ?>>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="/admin/users" class="neo-btn neo-btn-secondary w-100">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Results Summary -->
        <div class="mb-3">
            <p class="text-muted">
                <?php if ($totalUsers > 0): ?>
                    عرض <?php echo count($users); ?> من إجمالي <?php echo $totalUsers; ?> مستخدم
                <?php else: ?>
                    لا توجد نتائج
                <?php endif; ?>
                <?php if (!empty($search)): ?>
                    للبحث: <strong><?php echo htmlspecialchars($search); ?></strong>
                <?php endif; ?>
                <?php if ($statusFilter !== ''): ?>
                    | الحالة: <strong><?php echo ($statusFilter === 'active') ? 'نشط' : 'غير نشط'; ?></strong>
                <?php endif; ?>
            </p>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الاسم الكامل</th>
                        <th>الدور</th>
                        <th>الدورات</th>
                        <th>الاختبارات</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="table-empty-state">
                                <i class="fas fa-users"></i>
                                <h4>لا يوجد مستخدمين</h4>
                                <p>لم يتم العثور على أي مستخدمين في النظام. يمكنك إضافة مستخدم جديد باستخدام زر "إضافة مستخدم جديد".</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        // Create a copy of the users array to avoid reference issues
                        $usersDisplay = $users;
                        foreach ($usersDisplay as $index => $user): 
                        ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td>
                                <?php 
                                $roleLabels = [
                                    'admin' => '<span class="neo-badge neo-badge-danger"><i class="fas fa-user-shield me-1"></i> مدير</span>',
                                    'instructor' => '<span class="neo-badge neo-badge-primary"><i class="fas fa-chalkboard-teacher me-1"></i> مدرب</span>',
                                    'data_entry' => '<span class="neo-badge neo-badge-warning"><i class="fas fa-keyboard me-1"></i> مدخل بيانات</span>',
                                    'user' => '<span class="neo-badge neo-badge-info"><i class="fas fa-user me-1"></i> مستخدم</span>'
                                ];
                                echo $roleLabels[$user['role']] ?? '<span class="neo-badge neo-badge-secondary"><i class="fas fa-user me-1"></i> ' . $user['role'] . '</span>';
                                ?>
                            </td>
                            <td>
                                <span class="neo-badge neo-badge-primary">
                                    <i class="fas fa-book me-1"></i> <?php echo isset($user['course_count']) ? $user['course_count'] : 0; ?>
                                </span>
                            </td>
                            <td>
                                <span class="neo-badge neo-badge-warning">
                                    <i class="fas fa-file-alt me-1"></i> <?php echo isset($user['exam_count']) ? $user['exam_count'] : 0; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $statusLabels = [
                                    'active' => '<span class="status-badge status-active">نشط</span>',
                                    'inactive' => '<span class="status-badge status-pending">غير نشط</span>',
                                    'banned' => '<span class="status-badge status-inactive">محظور</span>'
                                ];
                                echo $statusLabels[$user['status']] ?? '<span class="status-badge">' . $user['status'] . '</span>';
                                ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="/admin/users/view/<?php echo $user['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/users/edit/<?php echo $user['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id']; ?>" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                        // Unset the current user to avoid reference issues
                        unset($user);
                        endforeach; 
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Delete Modals -->
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <!-- Delete Modal for User <?php echo $user['id']; ?> -->
                <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content neo-card">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel<?php echo $user['id']; ?>">تأكيد الحذف</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                هل أنت متأكد من حذف المستخدم <strong><?php echo htmlspecialchars($user['username']); ?></strong>؟
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <a href="/admin/users/delete/<?php echo $user['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="صفحات المستخدمين">
                <ul class="pagination">
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/users?page=1<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status='.$statusFilter : ''; ?>" aria-label="الصفحة الأولى">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="/admin/users?page=<?php echo $currentPage - 1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status='.$statusFilter : ''; ?>" aria-label="السابق">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php
                    // Show a limited number of page links
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    // Always show first page link if not in range
                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="/admin/users?page=1' . 
                             (!empty($search) ? '&search='.urlencode($search) : '') . 
                             ($statusFilter !== '' ? '&status='.$statusFilter : '') . 
                             '">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Page links
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        echo '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '">';
                        echo '<a class="page-link" href="/admin/users?page=' . $i . 
                             (!empty($search) ? '&search='.urlencode($search) : '') . 
                             ($statusFilter !== '' ? '&status='.$statusFilter : '') . 
                             '">' . $i . '</a>';
                        echo '</li>';
                    }
                    
                    // Always show last page link if not in range
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="/admin/users?page=' . $totalPages . 
                             (!empty($search) ? '&search='.urlencode($search) : '') . 
                             ($statusFilter !== '' ? '&status='.$statusFilter : '') . 
                             '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/users?page=<?php echo $currentPage + 1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status='.$statusFilter : ''; ?>" aria-label="التالي">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="/admin/users?page=<?php echo $totalPages; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo $statusFilter !== '' ? '&status='.$statusFilter : ''; ?>" aria-label="الصفحة الأخيرة">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- Add jQuery UI for autocomplete if not already included -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    const searchInput = $('#user-search');
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
        if (!$(e.target).closest('#user-search, #search-suggestions').length) {
            suggestionsContainer.addClass('d-none');
        }
    });
    
    // Function to fetch suggestions
    function fetchSuggestions(term) {
        $.ajax({
            url: '/admin/users/search-suggestions',
            method: 'GET',
            data: { term: term },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(item) {
                        html += `<div class="suggestion-item p-2 border-bottom" data-value="${item.value}">
                                    <div class="fw-bold">${item.value}</div>
                                    <div class="small text-muted">${item.email}</div>
                                    <div class="small">${item.full_name}</div>
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
