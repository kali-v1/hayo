<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">لوحة التحكم</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end mb-0">
                    <li class="breadcrumb-item active">الرئيسية</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Statistics Cards - Data Entry -->
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--info-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($dataEntryTotalQuestions); ?></h3>
                        <p class="mb-0">عدد الأسئلة التي قمت بإضافتها</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--info-light);">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--warning-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($dataEntryExamsWithQuestions); ?></h3>
                        <p class="mb-0">عدد الاختبارات التي أضفت لها أسئلة</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--warning-light);">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="neo-card stat-card" style="border-top: 5px solid var(--primary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold"><?php echo number_format($dataEntryTotalExams); ?></h3>
                        <p class="mb-0">عدد الاختبارات التي قمت بإنشائها</p>
                    </div>
                    <div class="stat-icon" style="background-color: var(--primary-light);">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">روابط سريعة</h5>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/admin/questions/create" class="neo-btn neo-btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> إضافة سؤال جديد
                    </a>
                    <a href="/admin/exams/create" class="neo-btn neo-btn-success">
                        <i class="fas fa-plus-circle me-2"></i> إنشاء اختبار جديد
                    </a>
                    <a href="/admin/questions" class="neo-btn neo-btn-info">
                        <i class="fas fa-list me-2"></i> عرض جميع الأسئلة
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">آخر نشاطاتك</h5>
                <div class="table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>النشاط</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($recentActivities) && count($recentActivities) > 0): ?>
                                <?php foreach ($recentActivities as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">لا توجد أنشطة حديثة</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>