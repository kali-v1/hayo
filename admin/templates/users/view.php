<?php
/**
 * User View Template
 * 
 * This template displays the details of a user.
 */
?>

<div class="container mt-4">
    <div class="card neo-brutalism-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo htmlspecialchars($pageTitle); ?></h5>
            <div>
                <a href="/admin/users/edit/<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="/admin/users" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">الاسم</th>
                            <td><?php 
                                if (isset($user['first_name']) && isset($user['last_name'])) {
                                    echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                } elseif (isset($user['name'])) {
                                    echo htmlspecialchars($user['name']);
                                } else {
                                    echo htmlspecialchars($user['username'] ?? '');
                                }
                            ?></td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th>اسم المستخدم</th>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                <?php 
                                $isActive = false;
                                if (isset($user['status']) && $user['status'] === 'active') {
                                    $isActive = true;
                                } elseif (isset($user['is_active']) && $user['is_active'] == 1) {
                                    $isActive = true;
                                }
                                
                                if ($isActive): 
                                ?>
                                    <span class="badge bg-success">نشط</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">غير نشط</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>رقم الجوال</th>
                            <td><?php echo !empty($user['mobile_number']) ? htmlspecialchars($user['mobile_number']) : 'غير متوفر'; ?></td>
                        </tr>
                        <tr>
                            <th>تاريخ التسجيل</th>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>الدورات المسجلة</th>
                            <td>
                                <span class="badge bg-primary">
                                    <i class="fas fa-book me-1"></i> <?php echo isset($user['course_count']) ? $user['course_count'] : count($enrollments ?? []); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>الاختبارات المسجلة</th>
                            <td>
                                <span class="badge bg-warning">
                                    <i class="fas fa-file-alt me-1"></i> <?php echo isset($user['exam_count']) ? $user['exam_count'] : 0; ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card neo-brutalism-card">
                        <div class="card-header">
                            <h6 class="mb-0">الصورة الشخصية</h6>
                        </div>
                        <div class="card-body text-center">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="<?php 
                                    if (isset($user['first_name']) && isset($user['last_name'])) {
                                        echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                    } elseif (isset($user['name'])) {
                                        echo htmlspecialchars($user['name']);
                                    } else {
                                        echo htmlspecialchars($user['username'] ?? '');
                                    }
                                ?>" class="img-fluid rounded-circle" style="max-height: 150px;">
                            <?php else: ?>
                                <div class="alert alert-info">
                                    لا توجد صورة شخصية
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card neo-brutalism-card">
                        <div class="card-header">
                            <h6 class="mb-0">الدورات المسجلة</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($enrollments)): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>الدورة</th>
                                            <th style="width: 150px;">تاريخ التسجيل</th>
                                            <th style="width: 100px;">الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrollments as $index => $enrollment): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <a href="/admin/courses/view/<?php echo $enrollment['course_id']; ?>">
                                                        <?php echo htmlspecialchars($enrollment['course_title']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($enrollment['created_at']); ?></td>
                                                <td>
                                                    <?php if (isset($enrollment['status']) && $enrollment['status'] === 'active'): ?>
                                                        <span class="badge bg-success">نشط</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    لا توجد دورات مسجلة لهذا المستخدم
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>