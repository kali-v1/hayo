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
    <!-- Statistics Cards -->
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
                        <h3 class="fw-bold"><?php echo number_format($totalQuestions); ?></h3>
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