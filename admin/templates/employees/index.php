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
        
        <div class="table-responsive">
            <table class="neo-table">
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
                                <td colspan="7">
                                    <div class="table-empty-state">
                                        <i class="fas fa-user-tie"></i>
                                        <h4>لا يوجد موظفين</h4>
                                        <p>لم يتم العثور على أي موظفين في النظام. يمكنك إضافة موظف جديد باستخدام زر "إضافة موظف جديد".</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['id']; ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?php echo htmlspecialchars($employee['name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($employee['username']); ?></td>
                                <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $employee['role'] === 'admin' ? 'danger' : ($employee['role'] === 'instructor' ? 'success' : 'info'); ?>">
                                        <?php echo htmlspecialchars($employee['role_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($employee['created_at'])); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/admin/employees/view/<?php echo $employee['id']; ?>" class="text-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/employees/edit/<?php echo $employee['id']; ?>" class="text-primary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="text-danger" title="حذف" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $employee['id']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $employee['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $employee['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content neo-card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $employee['id']; ?>">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف الموظف <strong><?php echo htmlspecialchars($employee['name']); ?></strong>؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <a href="/admin/employees/delete/<?php echo $employee['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
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