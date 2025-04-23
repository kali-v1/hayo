<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <div class="neo-icon-box me-3">
                        <i class="fas fa-question-circle fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="m-0 fw-bold">إدارة الأسئلة</h1>
                        <?php if (isset($exam) && $exam): ?>
                        <div class="mt-2 d-flex align-items-center flex-wrap">
                            <div class="neo-badge me-2">
                                <i class="fas fa-file-alt me-1"></i>
                                <?php echo htmlspecialchars($exam['title']); ?>
                            </div>
                            <?php if (isset($exam['course_title']) && !empty($exam['course_title'])): ?>
                            <div class="neo-badge neo-badge-info me-2">
                                <i class="fas fa-book me-1"></i>
                                <?php echo htmlspecialchars($exam['course_title']); ?>
                            </div>
                            <?php endif; ?>
                            <?php if (isset($exam['is_published']) && $exam['is_published'] == 1): ?>
                            <div class="neo-badge neo-badge-success">
                                <i class="fas fa-check-circle me-1"></i> منشور
                            </div>
                            <?php else: ?>
                            <div class="neo-badge neo-badge-secondary">
                                <i class="fas fa-file-alt me-1"></i> مسودة
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="d-flex justify-content-end align-items-center h-100">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-end mb-0">
                            <li class="breadcrumb-item">
                                <a href="/admin" class="text-decoration-none">
                                    <i class="fas fa-home me-1"></i>الرئيسية
                                </a>
                            </li>
                            <?php if (isset($exam) && $exam): ?>
                            <li class="breadcrumb-item">
                                <a href="/admin/questions" class="text-decoration-none">
                                    <i class="fas fa-file-alt me-1"></i>الاختبارات
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                <i class="fas fa-question-circle me-1"></i>
                                أسئلة <?php echo htmlspecialchars($exam['title']); ?>
                            </li>
                            <?php else: ?>
                            <li class="breadcrumb-item active">
                                <i class="fas fa-question-circle me-1"></i>الأسئلة
                            </li>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?php if (isset($exam) && $exam): ?>
    <!-- عرض الأسئلة لاختبار محدد -->
    <div class="row mb-4">
        <!-- بطاقة معلومات الاختبار -->
        <div class="col-md-4 mb-4">
            <div class="neo-card h-100">
                <div class="neo-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الاختبار
                        </h5>
                        <?php if (isset($exam['is_published']) && $exam['is_published'] == 1): ?>
                            <span class="neo-badge neo-badge-success">
                                <i class="fas fa-check-circle me-1"></i> منشور
                            </span>
                        <?php else: ?>
                            <span class="neo-badge neo-badge-secondary">
                                <i class="fas fa-file-alt me-1"></i> مسودة
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="neo-card-body">
                    <div class="p-3">
                        <div class="neo-card-inner mb-4">
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($exam['title']); ?></h5>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($exam['description']); ?></p>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="neo-card-inner h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="neo-icon-box-sm me-3">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">المدة</small>
                                            <strong class="fs-5"><?php echo isset($exam['duration']) ? $exam['duration'] : 30; ?></strong>
                                            <small>دقيقة</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="neo-card-inner h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="neo-icon-box-sm neo-icon-success me-3">
                                            <i class="fas fa-award"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">درجة النجاح</small>
                                            <strong class="fs-5"><?php echo $exam['passing_score']; ?></strong>
                                            <small>%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="neo-card-inner h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="neo-icon-box-sm neo-icon-info me-3">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">عدد الأسئلة</small>
                                            <strong class="fs-5"><?php echo isset($questions) ? count($questions) : 0; ?></strong>
                                            <small>سؤال</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="neo-card-inner h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="neo-icon-box-sm neo-icon-warning me-3">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">تاريخ الإنشاء</small>
                                            <strong><?php echo date('Y-m-d', strtotime($exam['created_at'])); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="neo-card-footer">
                    <div class="d-flex">
                        <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                        <a href="/admin/exams/edit/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-warning flex-grow-1 me-2">
                            <i class="fas fa-edit me-1"></i> تعديل الاختبار
                        </a>
                        <?php endif; ?>
                        <a href="/admin/questions" class="neo-btn neo-btn-secondary <?php echo $_SESSION['admin_role'] === 'admin' ? '' : 'flex-grow-1'; ?>">
                            <i class="fas fa-arrow-right me-1"></i> العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قائمة الأسئلة -->
        <div class="col-md-8">
            <div class="neo-card">
                <div class="neo-card-header neo-card-header-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-question-circle me-2 fa-lg"></i>
                            <h5 class="fw-bold mb-0">أسئلة الاختبار</h5>
                            <span class="neo-badge ms-2"><?php echo isset($questions) ? count($questions) : 0; ?> سؤال</span>
                        </div>
                        
                        <a href="/admin/questions/create?exam_id=<?php echo $exam['id']; ?>" class="neo-btn neo-btn-light">
                            <i class="fas fa-plus me-1"></i> إضافة سؤال جديد
                        </a>
                    </div>
                </div>
                
                <div class="neo-card-body">
                    <?php if (empty($questions)): ?>
                    <div class="p-5 text-center">
                        <div class="py-5">
                            <div class="mb-4">
                                <div class="neo-icon-box-lg mx-auto">
                                    <i class="fas fa-question-circle fa-2x"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-muted">لا توجد أسئلة</h4>
                            <p class="text-muted mb-4">لم يتم العثور على أي أسئلة في هذا الاختبار. يمكنك إضافة سؤال جديد باستخدام زر "إضافة سؤال جديد".</p>
                            <a href="/admin/questions/create?exam_id=<?php echo $exam['id']; ?>" class="neo-btn">
                                <i class="fas fa-plus me-1"></i> إضافة سؤال جديد
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4">
                        <div class="neo-search-box mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="questionSearch" class="form-control" placeholder="ابحث في الأسئلة...">
                                <button class="neo-btn" type="button">بحث</button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table neo-table">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="45%">السؤال</th>
                                        <th width="20%" class="text-center">النوع</th>
                                        <th width="10%" class="text-center">النقاط</th>
                                        <th width="20%" class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($questions as $index => $question): ?>
                                    <tr class="question-row">
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <div class="neo-number-badge">
                                                    <?php echo $index + 1; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="neo-text-block">
                                                <?php echo htmlspecialchars(substr($question['question_text'], 0, 100) . (strlen($question['question_text']) > 100 ? '...' : '')); ?>
                                                <?php if (!empty($question['creator_name'])): ?>
                                                <div class="mt-1 small text-muted">
                                                    <i class="fas fa-user-edit me-1"></i>
                                                    <?php echo htmlspecialchars($question['creator_name']); ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($question['question_type'] === 'single_choice'): ?>
                                            <span class="neo-badge">
                                                <i class="fas fa-dot-circle me-1"></i> اختيار واحد
                                            </span>
                                            <?php elseif ($question['question_type'] === 'multiple_choice'): ?>
                                            <span class="neo-badge neo-badge-success">
                                                <i class="fas fa-check-square me-1"></i> اختيار متعدد
                                            </span>
                                            <?php elseif ($question['question_type'] === 'drag_drop'): ?>
                                            <span class="neo-badge neo-badge-info">
                                                <i class="fas fa-arrows-alt me-1"></i> سحب وإفلات
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="neo-badge neo-badge-warning">
                                                <?php echo $question['points']; ?> نقطة
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/admin/questions/view/<?php echo $question['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                                                <a href="/admin/questions/edit/<?php echo $question['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $question['id']; ?>" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $question['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $question['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="neo-modal">
                                                        <div class="neo-modal-header neo-modal-header-danger">
                                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $question['id']; ?>">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                تأكيد الحذف
                                                            </h5>
                                                            <button type="button" class="neo-btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                        <div class="neo-modal-body">
                                                            <div class="neo-alert neo-alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                هل أنت متأكد من حذف هذا السؤال؟ لا يمكن التراجع عن هذه العملية.
                                                            </div>
                                                            <div class="neo-card-inner mt-3">
                                                                <strong class="d-block mb-2">السؤال:</strong>
                                                                <p class="mb-0"><?php echo htmlspecialchars(substr($question['question_text'], 0, 100) . (strlen($question['question_text']) > 100 ? '...' : '')); ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="neo-modal-footer">
                                                            <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">
                                                                <i class="fas fa-times me-1"></i> إلغاء
                                                            </button>
                                                            <a href="/admin/questions/delete/<?php echo $question['id']; ?>" class="neo-btn neo-btn-danger">
                                                                <i class="fas fa-trash me-1"></i> حذف
                                                            </a>
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
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- عرض بطاقات الاختبارات -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="neo-card">
                <div class="neo-card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-8 p-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="neo-icon-box me-3">
                                    <i class="fas fa-file-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1">اختر اختباراً لإدارة أسئلته</h4>
                                    <p class="text-muted mb-0">يمكنك إدارة أسئلة الاختبارات من خلال اختيار الاختبار المناسب من القائمة أدناه</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 neo-card-highlight p-4 text-center d-flex align-items-center justify-content-center">
                            <a href="/admin/exams/create" class="neo-btn neo-btn-light neo-btn-lg">
                                <i class="fas fa-plus me-2"></i> إضافة اختبار جديد
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- فلتر الاختبارات -->
        <div class="col-12 mb-4">
            <div class="neo-card">
                <div class="neo-card-header neo-card-header-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-filter me-2"></i> تصفية الاختبارات
                        </h5>
                    </div>
                </div>
                <div class="neo-card-body p-3">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="neo-search-box mb-0">
                                <div class="input-group flex-row-reverse">
                                    <button class="neo-btn" type="button">بحث</button>
                                    <input type="text" id="examSearch" class="form-control" placeholder="ابحث عن اختبار...">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="btn-group flex-grow-1">
                                    <button type="button" class="neo-btn neo-btn-primary active filter-btn" data-filter="all">
                                        <i class="fas fa-list-ul me-1"></i> الكل
                                    </button>
                                    <button type="button" class="neo-btn neo-btn-success filter-btn" data-filter="published">
                                        <i class="fas fa-check-circle me-1"></i> المنشورة
                                    </button>
                                    <button type="button" class="neo-btn neo-btn-secondary filter-btn" data-filter="draft">
                                        <i class="fas fa-file-alt me-1"></i> المسودات
                                    </button>
                                </div>
                                <button type="button" class="neo-btn neo-btn-info ms-2" id="sortExams">
                                    <i class="fas fa-sort-alpha-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (empty($exams)): ?>
        <div class="col-12">
            <div class="neo-card">
                <div class="neo-card-body p-5 text-center">
                    <div class="py-5">
                        <div class="mb-4">
                            <div class="neo-icon-box-lg mx-auto">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-muted">لا توجد اختبارات</h4>
                        <p class="text-muted mb-4">لم يتم العثور على أي اختبارات في النظام. يمكنك إضافة اختبار جديد باستخدام زر "إضافة اختبار جديد".</p>
                        <a href="/admin/exams/create" class="neo-btn neo-btn-primary">
                            <i class="fas fa-plus me-1"></i> إضافة اختبار جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="col-12">
            <div class="row neo-cards g-4">
                <?php foreach ($exams as $exam): ?>
                <div class="col-md-6 col-lg-4" 
                     data-title="<?php echo htmlspecialchars($exam['title']); ?>"
                     data-status="<?php echo (isset($exam['is_published']) && $exam['is_published'] == 1) ? 'published' : 'draft'; ?>">
                    <div class="neo-card h-100">
                        <?php if (isset($exam['is_published']) && $exam['is_published'] == 1): ?>
                        <div class="neo-card-header neo-card-header-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-check-circle me-2"></i> 
                                    <span class="neo-badge neo-badge-success px-3 py-2 fs-6">منشور</span>
                                </h5>
                                <span class="neo-badge neo-badge-light">
                                    <i class="fas fa-question-circle me-1"></i>
                                    <?php echo isset($exam['question_count']) ? $exam['question_count'] : 0; ?> سؤال
                                </span>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="neo-card-header neo-card-header-secondary">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-file-alt me-2"></i> 
                                    <span class="neo-badge neo-badge-secondary px-3 py-2 fs-6">مسودة</span>
                                </h5>
                                <span class="neo-badge neo-badge-light">
                                    <i class="fas fa-question-circle me-1"></i>
                                    <?php echo isset($exam['question_count']) ? $exam['question_count'] : 0; ?> سؤال
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="neo-card-body p-4">
                            <h5 class="fw-bold mb-3 border-bottom border-2 pb-2"><?php echo htmlspecialchars($exam['title']); ?></h5>
                            <div class="neo-card-inner mb-4 p-3 border border-2 border-dark">
                                <p class="mb-0"><?php echo htmlspecialchars(substr($exam['description'], 0, 100) . (strlen($exam['description']) > 100 ? '...' : '')); ?></p>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="neo-card-inner h-100 border border-2 border-dark p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="neo-icon-box-sm me-3">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">المدة</small>
                                                <strong class="fs-5"><?php echo isset($exam['duration']) ? $exam['duration'] : 30; ?></strong>
                                                <small>دقيقة</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="neo-card-inner h-100 border border-2 border-dark p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="neo-icon-box-sm neo-icon-success me-3">
                                                <i class="fas fa-award"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">درجة النجاح</small>
                                                <strong class="fs-5"><?php echo $exam['passing_score']; ?></strong>
                                                <small>%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="neo-badge neo-badge-dark d-inline-block mt-2 mb-3 px-3 py-2 border border-2 border-dark">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <strong><?php echo date('Y-m-d', strtotime($exam['created_at'])); ?></strong>
                            </div>
                        </div>
                        
                        <div class="neo-card-footer">
                            <div class="d-flex">
                                <a href="/admin/questions?exam_id=<?php echo $exam['id']; ?>" class="neo-btn neo-btn-primary flex-grow-1 me-2">
                                    <i class="fas fa-question-circle me-1"></i> إدارة الأسئلة
                                </a>
                                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                                <a href="/admin/exams/edit/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-warning" title="تعديل الاختبار">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- إضافة أنماط CSS للبطاقات -->
    <style>
    /* تأثيرات بطاقات الاختبارات */
    .neo-card {
        transition: all 0.3s ease;
        transform: translateY(0);
        border: 3px solid #000 !important;
        background-color: #fff !important;
    }
    
    .neo-card:hover {
        transform: translateY(-5px);
        box-shadow: 8px 8px 0px 0px rgba(0, 0, 0, 1) !important;
    }
    
    .neo-card:hover .neo-card-header {
        padding-top: 15px !important;
    }
    
    .neo-card-header .neo-badge {
        transition: all 0.3s ease;
    }
    
    .neo-card:hover .neo-card-header .neo-badge {
        transform: scale(1.1);
    }
    
    .neo-card-inner {
        background-color: #f8f9fa;
        border-radius: 0 !important;
        transition: all 0.3s ease;
    }
    
    .neo-card-inner:hover {
        transform: translateY(-2px);
        box-shadow: 4px 4px 0px 0px rgba(0, 0, 0, 0.8) !important;
    }
    

    
    /* تأثيرات للأزرار */
    .btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn:hover {
        transform: translateY(-3px);
    }
    
    .btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }
    
    .btn:hover::after {
        animation: ripple 1s ease-out;
    }
    
    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }
    
    /* تأثيرات للأزرار في فلتر البحث */
    .filter-btn {
        transition: all 0.3s ease;
    }
    
    .filter-btn.active {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        font-weight: bold;
    }
    
    /* تنسيقات جدول الأسئلة */
    .question-row {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .question-row:hover {
        background-color: rgba(13, 110, 253, 0.03) !important;
        transform: translateX(-5px);
    }
    
    .question-number {
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .question-row:hover .question-number {
        transform: scale(1.2);
    }
    
    .question-text {
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    .question-row:hover .question-text {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
    
    .action-btn {
        transition: all 0.3s ease;
        display: flex !important;
        align-items: center;
        justify-content: center;
        opacity: 0.8;
    }
    
    .question-row:hover .action-btn {
        opacity: 1;
    }
    
    .action-btn:hover {
        transform: translateY(-3px) scale(1.1);
    }
    
    /* تأثيرات للبطاقات الداخلية */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* تأثيرات للشارات */
    .badge {
        transition: all 0.3s ease;
    }
    
    .badge:hover {
        transform: scale(1.05);
    }
    
    /* تأثيرات للنقاط */
    .points-badge {
        transition: all 0.3s ease;
    }
    
    .question-row:hover .points-badge {
        background-color: rgba(13, 110, 253, 0.2) !important;
        transform: scale(1.05);
    }
    
    /* تأثيرات للمدخلات */
    .form-control, .input-group {
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.2) !important;
        border-color: #0d6efd !important;
    }
    
    /* تأثيرات للأيقونات الدائرية */
    .rounded-circle {
        transition: all 0.3s ease;
    }
    
    .card:hover .rounded-circle {
        transform: scale(1.1);
    }
    </style>
    
    <!-- إضافة سكريبت البحث في الأسئلة -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // البحث في الأسئلة
        const questionSearch = document.getElementById('questionSearch');
        if (questionSearch) {
            questionSearch.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const questionRows = document.querySelectorAll('.question-row');
                
                questionRows.forEach(row => {
                    const questionText = row.querySelector('.question-text').textContent.toLowerCase();
                    if (questionText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
    </script>
    
    <!-- إضافة سكريبت البحث والفلترة -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // البحث في الاختبارات
        const searchInput = document.getElementById('examSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                filterExams();
            });
        }
        
        // فلترة الاختبارات حسب الحالة
        const filterButtons = document.querySelectorAll('.filter-btn');
        if (filterButtons.length > 0) {
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // إزالة الكلاس النشط من جميع الأزرار
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // إضافة الكلاس النشط للزر المضغوط
                    this.classList.add('active');
                    filterExams();
                });
            });
        }
        
        // ترتيب الاختبارات
        const sortButton = document.getElementById('sortExams');
        if (sortButton) {
            let ascending = true;
            sortButton.addEventListener('click', function() {
                ascending = !ascending;
                this.innerHTML = ascending ? '<i class="fas fa-sort-alpha-down"></i>' : '<i class="fas fa-sort-alpha-up"></i>';
                sortExamCards(ascending);
            });
        }
        
        // دالة فلترة الاختبارات
        function filterExams() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            
            const examCards = document.querySelectorAll('.col-md-6.col-lg-4');
            examCards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const status = card.getAttribute('data-status');
                
                const matchesSearch = title.includes(searchTerm);
                const matchesFilter = activeFilter === 'all' || status === activeFilter;
                
                if (matchesSearch && matchesFilter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // دالة ترتيب بطاقات الاختبارات
        function sortExamCards(ascending) {
            const examCardsContainer = document.querySelector('.neo-cards');
            const examCards = Array.from(document.querySelectorAll('.col-md-6.col-lg-4'));
            
            examCards.sort((a, b) => {
                const titleA = a.getAttribute('data-title').toLowerCase();
                const titleB = b.getAttribute('data-title').toLowerCase();
                
                if (ascending) {
                    return titleA.localeCompare(titleB);
                } else {
                    return titleB.localeCompare(titleA);
                }
            });
            
            // إعادة ترتيب البطاقات في DOM
            examCards.forEach(card => {
                examCardsContainer.appendChild(card);
            });
        }
    });
    </script>
    <?php endif; ?>
</div>