<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">لوحة التحكم</h1>
            </div>
            <div class="col-sm-6">
                <div class="d-flex justify-content-end align-items-center">
                    <button id="printDashboard" class="neo-btn me-3" onclick="window.print();">
                        <i class="fas fa-print me-1"></i> طباعة التقرير
                    </button>
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item active">الرئيسية</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?php
    // Get the current admin
    $adminAuth = new AdminAuth();
    $currentAdmin = $adminAuth->getCurrentAdmin();
    $adminRole = $currentAdmin['role'];
    $isAdmin = $adminRole === 'admin';
    $isDataEntry = $adminRole === 'data_entry';
    ?>
    
    <?php if ($isAdmin): ?>
    <!-- Statistics Cards - Admin -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--primary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($totalUsers); ?></h3>
                        <p class="mb-0">إجمالي المستخدمين</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--primary-light);">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 12% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--secondary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($totalCourses); ?></h3>
                        <p class="mb-0">إجمالي الدورات</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--secondary-light);">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 5% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--warning-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($totalExams); ?></h3>
                        <p class="mb-0">إجمالي الاختبارات</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--warning-light);">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 8% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--info-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format(17); ?></h3>
                        <p class="mb-0">إجمالي الأسئلة</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--info-light);">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 15% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--success-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($totalEnrollments); ?></h3>
                        <p class="mb-0">إجمالي التسجيلات</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--success-light);">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 20% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--danger-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($totalExamAttempts); ?></h3>
                        <p class="mb-0">محاولات الاختبار</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--danger-light);">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 18% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--primary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($totalRevenue, 2); ?> ر.س</h3>
                        <p class="mb-0">إجمالي الإيرادات</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--primary-light);">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <small><i class="fas fa-arrow-up text-success"></i> زيادة بنسبة 25% عن الشهر الماضي</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">تحليل الإيرادات الشهرية</h5>
                <div class="chart-container" style="position: relative; height: 300px;">
                    <div class="chart-loading" id="revenueChartLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2">جاري تحميل البيانات...</p>
                    </div>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">معدل نجاح الاختبارات</h5>
                <div class="chart-container" style="position: relative; height: 300px;">
                    <div class="chart-loading" id="examCompletionChartLoading">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2">جاري تحميل البيانات...</p>
                    </div>
                    <canvas id="examCompletionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">تسجيلات المستخدمين الجدد</h5>
                <div class="chart-container" style="position: relative; height: 250px;">
                    <div class="chart-loading" id="userRegistrationChartLoading">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2">جاري تحميل البيانات...</p>
                    </div>
                    <canvas id="userRegistrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">أحدث المستخدمين</h5>
                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="/admin/users" class="neo-btn">عرض جميع المستخدمين</a>
                </div>
            </div>
        </div>
        
        <!-- Recent Courses -->
        <div class="col-lg-6 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">أحدث الدورات</h5>
                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>النوع</th>
                                <th>المدرب</th>
                                <th>تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCourses as $course): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                <td>
                                    <?php if ($course['is_free']): ?>
                                    <span class="neo-badge neo-badge-success">مجاني</span>
                                    <?php else: ?>
                                    <span class="neo-badge neo-badge-primary">مدفوع</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($course['name']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($course['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="/admin/courses" class="neo-btn">عرض جميع الدورات</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Question Statistics Section -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">إحصائيات الأسئلة</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="fw-bold">توزيع الأسئلة حسب النوع</h6>
                            <div class="chart-container" style="position: relative; height: 250px;">
                                <canvas id="questionTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="fw-bold">توزيع الأسئلة حسب الصعوبة</h6>
                            <div class="chart-container" style="position: relative; height: 250px;">
                                <canvas id="questionDifficultyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="neo-card stat-card mb-3" style="border-top: 5px solid var(--warning-color);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-bold"><?php echo number_format($questionsWithoutAnswers); ?></h3>
                                    <p class="mb-0">أسئلة بدون إجابات</p>
                                </div>
                                <div class="stat-icon" style="background-color: var(--warning-light);">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                            <div class="stat-footer">
                                <small><i class="fas fa-info-circle text-info"></i> تحتاج إلى مراجعة وإضافة إجابات</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="neo-card stat-card mb-3" style="border-top: 5px solid var(--info-color);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-bold"><?php echo number_format($questionsWithImages); ?></h3>
                                    <p class="mb-0">أسئلة تحتوي على صور</p>
                                </div>
                                <div class="stat-icon" style="background-color: var(--info-light);">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                            </div>
                            <div class="stat-footer">
                                <small><i class="fas fa-percentage text-success"></i> <?php echo round(($questionsWithImages / $totalQuestions) * 100); ?>% من إجمالي الأسئلة</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($isAdmin): ?>
    <!-- Admin Activity Section -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="neo-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">آخر النشاطات</h5>
                    <div>
                        <a href="<?php echo BASE_URL; ?>admin/activity-logs" class="neo-btn neo-btn-sm neo-btn-primary">
                            <i class="fas fa-list me-1"></i> عرض الكل
                        </a>
                    </div>
                </div>
                
                <!-- Search and Filter Form -->
                <div class="mb-4">
                    <form action="/admin" method="GET" id="activity-filter-form">
                        <div class="neo-card p-3 mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">اسم المستخدم</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="admin_username" class="form-control" placeholder="بحث..." value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">نوع النشاط</label>
                                    <select name="action" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="login">تسجيل دخول</option>
                                        <option value="logout">تسجيل خروج</option>
                                        <option value="add">إضافة</option>
                                        <option value="update">تعديل</option>
                                        <option value="delete">حذف</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">القسم</label>
                                    <select name="section" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="auth">المصادقة</option>
                                        <option value="users">المستخدمين</option>
                                        <option value="courses">الدورات</option>
                                        <option value="exams">الاختبارات</option>
                                        <option value="questions">الأسئلة</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">الفترة الزمنية</label>
                                    <div class="d-flex">
                                        <div class="input-group me-2">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="date_from" class="form-control" placeholder="من" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="date_to" class="form-control" placeholder="إلى" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="reset" class="neo-btn neo-btn-secondary me-2">
                                        <i class="fas fa-redo"></i> إعادة تعيين
                                    </button>
                                    <button type="submit" class="neo-btn neo-btn-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="activity-log-container">
                    <?php if (isset($adminActivityError)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>خطأ في قاعدة البيانات:</strong> <?php echo $adminActivityError; ?>
                    </div>
                    <?php elseif (empty($adminActivities)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        لا توجد نشاطات لعرضها.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="neo-table" id="admin-activity-table">
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
                                <?php foreach ($adminActivities as $index => $activity): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo $activity['username']; ?></div>
                                        <div class="text-muted small"><?php echo $activity['role']; ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                        // تحديد نوع الإجراء الأساسي (إضافة، تعديل، حذف)
                                        $actionName = $activity['action']; // الإجراء الأصلي من قاعدة البيانات
                                        
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
                                            'questions' => 'الأسئلة',
                                            'settings' => 'الإعدادات',
                                            'تسجيل الدخول' => 'المصادقة'
                                        ];
                                        
                                        $section = isset($sectionLabels[$activity['section']]) ? $sectionLabels[$activity['section']] : htmlspecialchars($activity['section']);
                                        
                                        echo $actionLabel . ' <small class="text-muted">(' . $section . ')</small>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($activity['details'])): ?>
                                            <?php
                                            // محاولة تحويل التفاصيل إلى ملخص مختصر بالعربية
                                            $details = $activity['details'];
                                            $summary = '';
                                            
                                            // محاولة تحليل JSON إذا كانت التفاصيل بهذا التنسيق
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
                                                    'name' => 'الاسم',
                                                    'description' => 'الوصف',
                                                    'active' => 'نشط',
                                                    'inactive' => 'غير نشط',
                                                    'password_changed' => 'تم تغيير كلمة المرور'
                                                ];
                                                
                                                // إنشاء ملخص بناءً على نوع النشاط
                                                if (isset($jsonData['username'])) {
                                                    $summary = 'المستخدم: ' . $jsonData['username'];
                                                } elseif (isset($jsonData['title'])) {
                                                    $summary = $jsonData['title'];
                                                } elseif (isset($jsonData['name'])) {
                                                    $summary = $jsonData['name'];
                                                } elseif (isset($jsonData['email'])) {
                                                    $summary = $jsonData['email'];
                                                } else {
                                                    // إذا لم نتمكن من تحديد ملخص محدد، نأخذ أول قيمة
                                                    foreach ($jsonData as $key => $value) {
                                                        if (!is_array($value) && !is_object($value)) {
                                                            $arabicKey = isset($arabicLabels[$key]) ? $arabicLabels[$key] : $key;
                                                            $summary = $arabicKey . ': ' . $value;
                                                            break;
                                                        }
                                                    }
                                                }
                                            } else {
                                                // إذا لم تكن JSON، نستخدم النص كما هو مع اقتطاع
                                                $summary = $details;
                                            }
                                            
                                            // اقتطاع الملخص إذا كان طويلاً
                                            $summary = substr($summary, 0, 50) . (strlen($summary) > 50 ? '...' : '');
                                            ?>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo htmlspecialchars($details); ?>">
                                                <?php echo htmlspecialchars($summary); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $activity['date']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            عرض <strong>1 - <?php echo count($adminActivities); ?></strong> من إجمالي <strong><?php echo count($adminActivities); ?></strong> سجل
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <script>
                    // JavaScript لتفعيل نموذج البحث والتصفية في قسم "آخر النشاطات"
                    document.addEventListener('DOMContentLoaded', function() {
                        // نموذج البحث والتصفية
                        const activityFilterForm = document.getElementById('activity-filter-form');
                        if (activityFilterForm) {
                            // زر إعادة التعيين
                            const resetButton = activityFilterForm.querySelector('button[type="reset"]');
                            if (resetButton) {
                                resetButton.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    // إعادة تعيين جميع الحقول
                                    const inputs = activityFilterForm.querySelectorAll('input, select');
                                    inputs.forEach(input => {
                                        if (input.type === 'text' || input.type === 'date') {
                                            input.value = '';
                                        } else if (input.tagName === 'SELECT') {
                                            input.selectedIndex = 0;
                                        }
                                    });
                                    
                                    // إعادة تحميل الصفحة بدون معلمات البحث
                                    window.location.href = window.location.pathname;
                                });
                            }
                            
                            // تفعيل البحث
                            activityFilterForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                
                                // جمع معلمات البحث
                                const formData = new FormData(activityFilterForm);
                                const searchParams = new URLSearchParams();
                                
                                // إضافة المعلمات غير الفارغة فقط
                                for (const [key, value] of formData.entries()) {
                                    if (value.trim() !== '') {
                                        searchParams.append(key, value);
                                    }
                                }
                                
                                // إعادة تحميل الصفحة مع معلمات البحث
                                const queryString = searchParams.toString();
                                window.location.href = window.location.pathname + (queryString ? '?' + queryString : '');
                            });
                        }
                    });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Recent Exams and Attempts -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">أحدث الاختبارات</h5>
                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>الدورة</th>
                                <th>المدة (دقيقة)</th>
                                <th>تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentExams as $exam): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                <td><?php echo htmlspecialchars($exam['course_title']); ?></td>
                                <td><?php echo $exam['duration_minutes']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($exam['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="/admin/exams" class="neo-btn">عرض جميع الاختبارات</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">أحدث محاولات الاختبار</h5>
                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>الاختبار</th>
                                <th>النتيجة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAttempts as $attempt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($attempt['first_name'] . ' ' . $attempt['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($attempt['exam_title']); ?></td>
                                <td><?php echo $attempt['percentage']; ?>%</td>
                                <td>
                                    <?php if (isset($attempt['passed']) && $attempt['passed']): ?>
                                    <span class="neo-badge neo-badge-success">ناجح</span>
                                    <?php else: ?>
                                    <span class="neo-badge neo-badge-danger">راسب</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    overflow: hidden;
}
.avatar-sm img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($isAdmin): ?>
    // Initialize admin charts
    initAdminCharts();
    <?php else: ?>
    // Initialize instructor charts
    initInstructorCharts();
    <?php endif; ?>
});

function refreshActivityLog() {
    // Add spinning animation to refresh button
    const refreshBtn = document.querySelector('.refresh-btn i');
    refreshBtn.classList.add('fa-spin');
    
    // Get the activity log container
    const activityLogContainer = document.querySelector('.activity-log-container');
    
    // Fetch updated activity log via AJAX - use relative path to avoid CORS issues
    fetch('admin/api/activity-log.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                // Show error message
                activityLogContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>خطأ في قاعدة البيانات:</strong> ${data.error || 'حدث خطأ غير معروف'}
                </div>`;
                return;
            }
            
            if (!data.activities || data.activities.length === 0) {
                // Show no activities message
                activityLogContainer.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد نشاطات لعرضها.
                </div>`;
                return;
            }
            
            // Update the activity log table
            let html = `
            <div class="table-responsive">
                <table class="neo-table" id="admin-activity-table">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>النشاط</th>
                            <th>القسم</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
            data.activities.forEach(activity => {
                html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-2">
                                <img src="${activity.avatar || '/assets/images/avatar.png'}" alt="Avatar" class="rounded-circle">
                            </div>
                            <div>
                                <div class="fw-bold">${activity.username}</div>
                                <small class="text-muted">${activity.role}</small>
                            </div>
                        </div>
                    </td>
                    <td>${activity.action}</td>
                    <td>${activity.section}</td>
                    <td>${activity.date}</td>
                    <td>
                        <span class="badge bg-${activity.status_color}">${activity.status}</span>
                    </td>
                </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
            </div>`;
            
            activityLogContainer.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching activity log:', error);
            // Show error message
            activityLogContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>خطأ في الاتصال:</strong> ${error.message}
            </div>`;
        })
        .finally(() => {
            // Remove spinning animation after 1 second
            setTimeout(() => {
                refreshBtn.classList.remove('fa-spin');
            }, 1000);
        });
}

function initAdminCharts() {
    // Initialize revenue and user registration charts (existing charts)
    
    // Question Type Chart
    const questionTypeCtx = document.getElementById('questionTypeChart').getContext('2d');
    const questionTypeData = {
        labels: [
            <?php 
            // Types are already mapped in the controller
            foreach ($questionsByType as $type => $count) {
                echo "'" . $type . "', ";
            }
            ?>
        ],
        datasets: [{
            label: 'عدد الأسئلة',
            data: [<?php echo implode(', ', $questionsByType); ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    new Chart(questionTypeCtx, {
        type: 'pie',
        data: questionTypeData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: 'Tajawal, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Question Difficulty Chart
    const questionDifficultyCtx = document.getElementById('questionDifficultyChart').getContext('2d');
    const questionDifficultyData = {
        labels: [
            <?php 
            $difficultyLabels = [
                'easy' => 'سهل',
                'medium' => 'متوسط',
                'hard' => 'صعب'
            ];
            foreach ($questionsByDifficulty as $difficulty => $count) {
                echo "'" . ($difficultyLabels[$difficulty] ?? $difficulty) . "', ";
            }
            ?>
        ],
        datasets: [{
            label: 'عدد الأسئلة',
            data: [<?php echo implode(', ', $questionsByDifficulty); ?>],
            backgroundColor: [
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(255, 99, 132, 0.7)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    new Chart(questionDifficultyCtx, {
        type: 'doughnut',
        data: questionDifficultyData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: 'Tajawal, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function initInstructorCharts() {
    // Student Distribution Chart
    const enrollmentChartCtx = document.getElementById('instructorEnrollmentChart').getContext('2d');
    const enrollmentData = {
        labels: [<?php 
            $courseLabels = [];
            foreach ($instructorCourseStats as $course) {
                $courseLabels[] = "'" . addslashes($course['title']) . "'";
            }
            echo implode(', ', $courseLabels);
        ?>],
        datasets: [{
            label: 'عدد الطلاب',
            data: [<?php 
                $enrollmentCounts = [];
                foreach ($instructorCourseStats as $course) {
                    $enrollmentCounts[] = $course['enrollment_count'];
                }
                echo implode(', ', $enrollmentCounts);
            ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderWidth: 1
        }]
    };
    
    new Chart(enrollmentChartCtx, {
        type: 'pie',
        data: enrollmentData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
    
    document.getElementById('instructorEnrollmentChartLoading').style.display = 'none';
    
    // Revenue Chart
    const revenueChartCtx = document.getElementById('instructorRevenueChart').getContext('2d');
    const revenueData = {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'الإيرادات (ر.س)',
            data: [12000, 19000, 15000, 25000, 22000, 30000],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            tension: 0.4
        }]
    };
    
    new Chart(revenueChartCtx, {
        type: 'line',
        data: revenueData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    document.getElementById('instructorRevenueChartLoading').style.display = 'none';
}
</script>

<?php if (!$isAdmin): ?>
<!-- Statistics Cards - Instructor -->
<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="neo-card stat-card" style="border-top: 5px solid var(--primary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold"><?php echo number_format($totalInstructorCourses ?? 0); ?></h3>
                    <p class="mb-0">دوراتي التدريبية</p>
                </div>
                <div class="stat-icon" style="background-color: var(--primary-light);">
                    <i class="fas fa-book fa-2x"></i>
                </div>
            </div>
            <div class="stat-footer">
                <small><i class="fas fa-eye"></i> <a href="/admin/courses">عرض جميع الدورات</a></small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="neo-card stat-card" style="border-top: 5px solid var(--success-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold"><?php echo number_format($totalInstructorEnrollments ?? 0); ?></h3>
                    <p class="mb-0">تسجيلات الطلاب</p>
                </div>
                <div class="stat-icon" style="background-color: var(--success-light);">
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
            </div>
            <div class="stat-footer">
                <small><i class="fas fa-info-circle"></i> متوسط <?php echo number_format(($totalInstructorEnrollments ?? 0) / (($totalInstructorCourses ?? 1) ?: 1), 0); ?> طالب لكل دورة</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12 mb-4">
        <div class="neo-card stat-card" style="border-top: 5px solid var(--primary-color);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold"><?php echo number_format($instructorEarnings ?? 0, 2); ?> ر.س</h3>
                    <p class="mb-0">أرباحي من الدورات</p>
                </div>
                <div class="stat-icon" style="background-color: var(--primary-light);">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
            <div class="stat-footer">
                <?php if (isset($earningSettings) && is_array($earningSettings) && isset($earningSettings['earning_type']) && $earningSettings['earning_type'] === 'percentage'): ?>
                <small><i class="fas fa-info-circle"></i> <?php echo number_format($earningSettings['earning_value'] ?? 0, 0); ?>% من إجمالي الإيرادات</small>
                <?php else: ?>
                <small><i class="fas fa-info-circle"></i> <?php echo number_format(($instructorEarnings ?? 0) / (($totalInstructorCourses ?? 1) ?: 1), 2); ?> ر.س لكل دورة</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Course Performance Analysis -->
<div class="row mb-4">
    <div class="col-12">
        <div class="neo-card">
            <h5 class="fw-bold mb-3">تحليل أداء الدورات</h5>
            <div class="table-responsive">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>اسم الدورة</th>
                            <th>عدد الطلاب</th>
                            <th>الإيرادات</th>
                            <th>أرباحي</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructorCourseStats as $course): ?>
                        <tr>
                            <td>
                                <a href="/admin/courses/view/<?php echo $course['id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </a>
                            </td>
                            <td><?php echo number_format($course['enrollment_count']); ?></td>
                            <td><?php echo number_format($course['revenue'], 2); ?> ر.س</td>
                            <td><?php echo number_format($course['instructor_earnings'], 2); ?> ر.س</td>
                            <td>
                                <?php if ($course['is_active']): ?>
                                <span class="neo-badge neo-badge-success">نشط</span>
                                <?php else: ?>
                                <span class="neo-badge neo-badge-secondary">غير نشط</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="/admin/courses" class="neo-btn">إدارة الدورات</a>
            </div>
        </div>
    </div>
</div>

<!-- Student Distribution Chart -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="neo-card">
            <h5 class="fw-bold mb-3">توزيع الطلاب حسب الدورات</h5>
            <div class="chart-container" style="position: relative; height: 250px;">
                <div class="chart-loading" id="instructorEnrollmentChartLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل البيانات...</p>
                </div>
                <canvas id="instructorEnrollmentChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="neo-card">
            <h5 class="fw-bold mb-3">تحليل الإيرادات الشهرية</h5>
            <div class="chart-container" style="position: relative; height: 250px;">
                <div class="chart-loading" id="instructorRevenueChartLoading">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل البيانات...</p>
                </div>
                <canvas id="instructorRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Enrollments -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="neo-card">
            <h5 class="fw-bold mb-3">أحدث الطلاب المسجلين في دوراتي</h5>
            <div class="table-responsive">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEnrollments as $enrollment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($enrollment['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="neo-card">
            <h5 class="fw-bold mb-3">دوراتي التدريبية</h5>
            <div class="table-responsive">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>عدد الطلاب</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructorCourses as $course): ?>
                        <tr>
                            <td>
                                <a href="/admin/courses/view/<?php echo $course['id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($course['is_free']): ?>
                                <span class="neo-badge neo-badge-success">مجاني</span>
                                <?php else: ?>
                                <span class="neo-badge neo-badge-primary">مدفوع</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($course['enrollment_count']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($course['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="/admin/courses" class="neo-btn">إدارة الدورات</a>
                <a href="/admin/courses/create" class="neo-btn neo-btn-success me-2">إضافة دورة جديدة</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>