<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">سجل نشاطات المشرفين</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">سجل النشاطات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="mb-3">
            <h5 class="fw-bold mb-0">سجل نشاطات المشرفين</h5>
        </div>
        
        <!-- Search and Filter Form -->
        <div class="mb-4">
            <form action="/admin/activity-logs" method="GET">
                <div class="neo-card p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">اسم المستخدم</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="admin_username" class="form-control" placeholder="بحث..." value="<?php echo htmlspecialchars($filters['admin_username'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">نوع النشاط</label>
                            <select name="action" class="form-select">
                                <option value="">الكل</option>
                                <?php 
                                $actionLabels = [
                                    'login' => 'تسجيل دخول',
                                    'logout' => 'تسجيل خروج',
                                    'add' => 'إضافة',
                                    'update' => 'تعديل',
                                    'delete' => 'حذف'
                                ];
                                
                                foreach ($actions as $action): 
                                    $actionLabel = isset($actionLabels[$action]) ? $actionLabels[$action] : $action;
                                ?>
                                    <option value="<?php echo htmlspecialchars($action); ?>" <?php echo ($filters['action'] === $action) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($actionLabel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">القسم</label>
                            <select name="section" class="form-select">
                                <option value="">الكل</option>
                                <?php 
                                $sectionLabels = [
                                    'auth' => 'المصادقة',
                                    'users' => 'المستخدمين',
                                    'courses' => 'الدورات',
                                    'exams' => 'الاختبارات',
                                    'questions' => 'الأسئلة'
                                ];
                                
                                foreach ($sections as $section): 
                                    $sectionLabel = isset($sectionLabels[$section]) ? $sectionLabels[$section] : $section;
                                ?>
                                    <option value="<?php echo htmlspecialchars($section); ?>" <?php echo ($filters['section'] === $section) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sectionLabel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">الفترة الزمنية</label>
                            <div class="d-flex">
                                <div class="input-group me-2">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="date_from" class="form-control" placeholder="من" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="date_to" class="form-control" placeholder="إلى" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end">
                            <a href="/admin/activity-logs" class="neo-btn neo-btn-secondary me-2">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </a>
                            <button type="submit" class="neo-btn neo-btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>النشاط</th>
                        <th>التفاصيل</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5">
                            <div class="table-empty-state">
                                <i class="fas fa-history"></i>
                                <h4>لا توجد سجلات نشاط</h4>
                                <p>لم يتم العثور على أي سجلات نشاط تطابق معايير البحث.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo $log['id']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($log['admin_username']); ?></div>
                                <div class="text-muted small">#<?php echo $log['admin_id']; ?></div>
                            </td>
                            <td>
                                <?php 
                                // تحديد نوع الإجراء الأساسي (إضافة، تعديل، حذف)
                                $actionName = $log['action']; // الإجراء الأصلي من قاعدة البيانات
                                
                                // تعيين الإجراء الأساسي والألوان
                                if (strpos($actionName, 'create') !== false || strpos($actionName, 'add') !== false) {
                                    $actionLabel = '<span class="status-badge status-success">إضافة</span>';
                                } 
                                elseif (strpos($actionName, 'update') !== false || strpos($actionName, 'edit') !== false) {
                                    $actionLabel = '<span class="status-badge status-warning">تعديل</span>';
                                } 
                                elseif (strpos($actionName, 'delete') !== false || strpos($actionName, 'remove') !== false) {
                                    $actionLabel = '<span class="status-badge status-danger">حذف</span>';
                                }
                                elseif ($actionName == 'login') {
                                    $actionLabel = '<span class="status-badge status-info">تسجيل دخول</span>';
                                }
                                elseif ($actionName == 'logout') {
                                    $actionLabel = '<span class="status-badge status-secondary">تسجيل خروج</span>';
                                }
                                else {
                                    // إذا لم نتمكن من تحديد الإجراء، نستخدم الإجراء الأصلي
                                    $actionLabel = '<span class="status-badge status-primary">' . htmlspecialchars($actionName) . '</span>';
                                }
                                
                                // تعيين القسم
                                $sectionLabels = [
                                    'auth' => 'المصادقة',
                                    'users' => 'المستخدمين',
                                    'courses' => 'الدورات',
                                    'exams' => 'الاختبارات',
                                    'questions' => 'الأسئلة'
                                ];
                                
                                $section = isset($sectionLabels[$log['section']]) ? $sectionLabels[$log['section']] : htmlspecialchars($log['section']);
                                
                                echo $actionLabel . ' <small class="text-muted">(' . $section . ')</small>';
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($log['details'])): ?>
                                    <button class="neo-btn neo-btn-sm neo-btn-info" data-bs-toggle="modal" data-bs-target="#dataModal<?php echo $log['id']; ?>">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </button>
                                    
                                    <!-- Data Modal -->
                                    <div class="modal fade" id="dataModal<?php echo $log['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content neo-card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تفاصيل النشاط</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold">التفاصيل:</h6>
                                                        <pre class="bg-light p-3 rounded"><code><?php 
                                                        // محاولة تنسيق التفاصيل إذا كانت بتنسيق JSON
                                                        $details = $log['details'];
                                                        try {
                                                            $jsonData = json_decode($details, true);
                                                            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                                                                // تحويل المفاتيح الإنجليزية إلى عربي
                                                                $arabicLabels = [
                                                                    'user_id' => 'معرف المستخدم',
                                                                    'username' => 'اسم المستخدم',
                                                                    'email' => 'البريد الإلكتروني',
                                                                    'first_name' => 'الاسم الأول',
                                                                    'last_name' => 'الاسم الأخير',
                                                                    'status' => 'الحالة',
                                                                    'exam_id' => 'معرف الاختبار',
                                                                    'title' => 'العنوان',
                                                                    'course_id' => 'معرف الدورة',
                                                                    'active' => 'نشط',
                                                                    'inactive' => 'غير نشط',
                                                                    'password_changed' => 'تم تغيير كلمة المرور'
                                                                ];
                                                                
                                                                $formattedDetails = [];
                                                                foreach ($jsonData as $key => $value) {
                                                                    $arabicKey = isset($arabicLabels[$key]) ? $arabicLabels[$key] : $key;
                                                                    if (is_bool($value)) {
                                                                        $value = $value ? 'نعم' : 'لا';
                                                                    } elseif ($key === 'status' && isset($arabicLabels[$value])) {
                                                                        $value = $arabicLabels[$value];
                                                                    }
                                                                    $formattedDetails[] = $arabicKey . ': ' . $value;
                                                                }
                                                                echo htmlspecialchars(implode("\n", $formattedDetails));
                                                            } else {
                                                                echo htmlspecialchars($details);
                                                            }
                                                        } catch (Exception $e) {
                                                            echo htmlspecialchars($details);
                                                        }
                                                        ?></code></pre>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold">عنوان IP:</h6>
                                                            <p class="bg-light p-2 rounded"><?php echo htmlspecialchars($log['ip_address']); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-bold">تاريخ النشاط:</h6>
                                                            <p class="bg-light p-2 rounded"><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></p>
                                                        </div>
                                                    </div>
                                                    <?php if (!empty($log['user_agent'])): ?>
                                                    <div class="mt-3">
                                                        <h6 class="fw-bold">معلومات المتصفح:</h6>
                                                        <p class="bg-light p-2 rounded small"><?php echo htmlspecialchars($log['user_agent']); ?></p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">لا توجد تفاصيل</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination and Results Summary -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <!-- Results Summary -->
            <div class="mb-3 mb-md-0">
                <?php if (isset($pagination['total'])): ?>
                <div class="text-muted">
                    <?php 
                    $start = (($pagination['current_page'] - 1) * $pagination['per_page']) + 1;
                    $end = min($start + count($logs) - 1, $pagination['total']);
                    ?>
                    عرض <strong><?php echo $start; ?> - <?php echo $end; ?></strong> من إجمالي <strong><?php echo $pagination['total']; ?></strong> سجل
                    
                    <?php if (!empty($filters['admin_username']) || !empty($filters['action']) || !empty($filters['section']) || !empty($filters['date_from']) || !empty($filters['date_to'])): ?>
                    <span class="ms-2">
                        <a href="/admin/activity-logs" class="text-decoration-none">
                            <i class="fas fa-times-circle text-danger"></i> إزالة عوامل التصفية
                        </a>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pagination['total_pages']) && $pagination['total_pages'] > 1): ?>
            <nav aria-label="صفحات سجل النشاطات">
                <ul class="pagination mb-0">
                    <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/activity-logs?page=1<?php 
                            echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                            echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                            echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                            echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                            echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        ?>" aria-label="الصفحة الأولى">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="/admin/activity-logs?page=<?php echo $pagination['current_page'] - 1; ?><?php 
                            echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                            echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                            echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                            echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                            echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        ?>" aria-label="السابق">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php
                    // Show a limited number of page links
                    $startPage = max(1, $pagination['current_page'] - 2);
                    $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                    
                    // Always show first page link if not in range
                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="/admin/activity-logs?page=1';
                        echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                        echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                        echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                        echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                        echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        echo '">1</a></li>';
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Page links
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        echo '<li class="page-item ' . ($i == $pagination['current_page'] ? 'active' : '') . '">';
                        echo '<a class="page-link" href="/admin/activity-logs?page=' . $i;
                        echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                        echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                        echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                        echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                        echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        echo '">' . $i . '</a>';
                        echo '</li>';
                    }
                    
                    // Always show last page link if not in range
                    if ($endPage < $pagination['total_pages']) {
                        if ($endPage < $pagination['total_pages'] - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="/admin/activity-logs?page=' . $pagination['total_pages'];
                        echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                        echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                        echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                        echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                        echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        echo '">' . $pagination['total_pages'] . '</a></li>';
                    }
                    ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/activity-logs?page=<?php echo $pagination['current_page'] + 1; ?><?php 
                            echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                            echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                            echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                            echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                            echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        ?>" aria-label="التالي">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="/admin/activity-logs?page=<?php echo $pagination['total_pages']; ?><?php 
                            echo !empty($filters['admin_username']) ? '&admin_username='.urlencode($filters['admin_username']) : ''; 
                            echo !empty($filters['action']) ? '&action='.urlencode($filters['action']) : '';
                            echo !empty($filters['section']) ? '&section='.urlencode($filters['section']) : '';
                            echo !empty($filters['date_from']) ? '&date_from='.urlencode($filters['date_from']) : '';
                            echo !empty($filters['date_to']) ? '&date_to='.urlencode($filters['date_to']) : '';
                        ?>" aria-label="الصفحة الأخيرة">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Format JSON data in modals
    $('.modal').on('shown.bs.modal', function() {
        const preElement = $(this).find('pre code');
        const jsonText = preElement.text();
        
        try {
            const jsonObj = JSON.parse(jsonText);
            const formattedJson = JSON.stringify(jsonObj, null, 2);
            preElement.text(formattedJson);
        } catch (e) {
            // Not valid JSON, leave as is
        }
    });
});
</script>