<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/employees">الموظفين</a></li>
                    <li class="breadcrumb-item active">عرض الموظف</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">بيانات الموظف</h5>
            <div>
                <a href="/admin/employees/edit/<?php echo $employee['id']; ?>" class="neo-btn neo-btn-primary btn-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="/admin/employees" class="neo-btn neo-btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="neo-table">
                        <tr>
                            <th style="width: 150px;">الاسم</th>
                            <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        </tr>
                        <tr>
                            <th>اسم المستخدم</th>
                            <td><?php echo htmlspecialchars($employee['username']); ?></td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td><?php echo htmlspecialchars($employee['email']); ?></td>
                        </tr>
                        <tr>
                            <th>الدور</th>
                            <td>
                                <span class="badge bg-<?php echo $employee['role'] === 'admin' ? 'danger' : ($employee['role'] === 'instructor' ? 'success' : 'info'); ?>">
                                    <?php echo htmlspecialchars($employee['role_name']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td><?php echo htmlspecialchars($employee['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>آخر تحديث</th>
                            <td><?php echo htmlspecialchars($employee['updated_at']); ?></td>
                        </tr>
                    </table>
        </div>
    </div>
</div>