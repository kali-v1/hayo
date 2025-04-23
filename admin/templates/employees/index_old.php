<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة الموظفين</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الموظفين</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة الموظفين</h5>
            <a href="/admin/employees/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة موظف جديد
            </a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="7" class="text-center">لا يوجد موظفين</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($employees as $index => $employee): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['username']); ?></td>
                        <td><?php echo htmlspecialchars($employee['email']); ?></td>
                        <td>
                            <?php if ($employee['role'] === 'admin'): ?>
                            <span class="badge bg-primary">مدير</span>
                            <?php elseif ($employee['role'] === 'data_entry'): ?>
                            <span class="badge bg-info">مدخل بيانات</span>
                            <?php elseif ($employee['role'] === 'instructor'): ?>
                            <span class="badge bg-success">مدرب</span>
                            <?php else: ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($employee['role']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($employee['created_at'])); ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="/admin/employees/view/<?php echo $employee['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/employees/edit/<?php echo $employee['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($employee['id'] != $_SESSION['admin_id']): ?>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $employee['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $employee['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $employee['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $employee['id']; ?>">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من حذف الموظف <strong><?php echo htmlspecialchars($employee['name']); ?></strong>؟
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <a href="/admin/employees/delete/<?php echo $employee['id']; ?>" class="btn btn-danger">حذف</a>
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
    </div>
</div>
