<?php
/**
 * Course View Template
 * 
 * This template displays the details of a course.
 */
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses">الدورات</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($course['title']); ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-end mb-3">
        <a href="/admin/courses/<?php echo $course['id']; ?>/lessons" class="neo-btn neo-btn-success me-2">
            <i class="fas fa-book"></i> إدارة الدروس
        </a>
        <a href="/admin/courses/edit/<?php echo $course['id']; ?>" class="neo-btn neo-btn-warning me-2">
            <i class="fas fa-edit"></i> تعديل
        </a>
        <a href="/admin/courses" class="neo-btn neo-btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة
        </a>
    </div>
    
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">تفاصيل الدورة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="neo-table neo-table-bordered">
                        <tr>
                            <th style="width: 150px;">العنوان</th>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                        </tr>
                        <tr>
                            <th>الرابط</th>
                            <td><?php echo htmlspecialchars($course['slug']); ?></td>
                        </tr>
                        <tr>
                            <th>رابط الشركة</th>
                            <td>
                                <?php if (!empty($course['company_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($course['company_url']); ?>" target="_blank" class="text-primary">
                                        <?php echo htmlspecialchars($course['company_url']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">غير محدد</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td><?php echo nl2br(htmlspecialchars($course['description'])); ?></td>
                        </tr>
                        <tr>
                            <th>السعر</th>
                            <td>
                                <?php if ($course['is_free']): ?>
                                    <span class="status-badge status-active">مجاني</span>
                                <?php else: ?>
                                    <span class="fw-bold"><?php echo htmlspecialchars($course['price']); ?> ر.س</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>المشتركين</th>
                            <td>
                                <span class="badge bg-info"><?php echo isset($course['subscribers_count']) ? $course['subscribers_count'] : '0'; ?></span>
                                مشترك
                            </td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                <?php if (isset($course['status']) && $course['status'] === 'published'): ?>
                                    <span class="status-badge status-active">منشور</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">مسودة</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>المدرب</th>
                            <td><?php echo htmlspecialchars($course['instructor_name']); ?> (<?php echo htmlspecialchars($course['instructor_username']); ?>)</td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="neo-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">صورة الدورة</h5>
                        </div>
                        <div class="text-center p-3">
                            <?php if (!empty($course['image'])): ?>
                                <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="neo-alert neo-alert-info">
                                    <i class="fas fa-info-circle"></i> لا توجد صورة للدورة
                                </div>
                            <?php endif; ?>
                        </div>
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
                    <div class="neo-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">مرفقات الدورة</h5>
                        </div>
                        <div class="p-3">
                            <ul class="list-group">
                                <?php foreach ($attachments as $attachment): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
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
                                    </span>
                                    <a href="<?php echo htmlspecialchars($attachment['file_path']); ?>" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lessons Section -->
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">دروس الدورة</h5>
            <a href="/admin/courses/<?php echo $course["id"]; ?>/lessons" class="neo-btn neo-btn-sm neo-btn-primary">
                <i class="fas fa-book"></i> إدارة الدروس
            </a>
        </div>
        <div>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-primary">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stats-info">
                            <h4><?php echo $lessonStats["total"]; ?></h4>
                            <p>إجمالي الدروس</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-info">
                            <h4><?php echo $lessonStats["published"]; ?></h4>
                            <p>الدروس المنشورة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-info">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="stats-info">
                            <h4><?php echo $lessonStats["free"]; ?></h4>
                            <p>الدروس المجانية</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-info">
                            <h4><?php echo $lessonStats["totalDuration"]; ?></h4>
                            <p>إجمالي الدقائق</p>
                        </div>
                    </div>
                </div>
            </div>
                            
            <?php if (empty($lessons)): ?>
                <div class="neo-alert neo-alert-info">
                    <i class="fas fa-info-circle"></i> لا توجد دروس لهذه الدورة. <a href="/admin/courses/<?php echo $course["id"]; ?>/lessons/create" class="alert-link">أضف درسًا جديدًا</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="neo-table neo-table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان الدرس</th>
                                <th>المدة</th>
                                <th>مجاني</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lessons as $index => $lesson): ?>
                                <tr>
                                    <td><?php echo $lesson["order_number"]; ?></td>
                                    <td><?php echo htmlspecialchars($lesson["title"]); ?></td>
                                    <td><?php echo $lesson["duration"]; ?> دقيقة</td>
                                    <td>
                                        <?php if ($lesson["is_free"]): ?>
                                            <span class="status-badge status-active">نعم</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($lesson["status"] === "published"): ?>
                                            <span class="status-badge status-active">منشور</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">مسودة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="/admin/courses/<?php echo $course["id"]; ?>/lessons/<?php echo $lesson["id"]; ?>/edit" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>