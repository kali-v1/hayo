<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">الملف الشخصي</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الملف الشخصي</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">معلومات الملف الشخصي</h5>
                <div>
                    <?php
                    // Display errors if any
                    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                        echo '<div class="alert alert-danger">';
                        echo '<ul class="mb-0">';
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<li>' . $error . '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        
                        // Clear errors
                        unset($_SESSION['errors']);
                    }
                    
                    // Get form data if any
                    $form_data = $_SESSION['form_data'] ?? $admin;
                    unset($_SESSION['form_data']);
                    ?>
                    
                    <form action="/admin/profile/update" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                            <div class="form-text">لا يمكن تغيير اسم المستخدم.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">الدور</label>
                            <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars(getRoleName($admin['role'])); ?>" disabled>
                            <div class="form-text">لا يمكن تغيير الدور.</div>
                        </div>
                        
                        <button type="submit" class="neo-btn neo-btn-primary">حفظ التغييرات</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="neo-card">
                <h5 class="fw-bold mb-3">تغيير كلمة المرور</h5>
                <div>
                    <?php
                    // Display password errors if any
                    if (isset($_SESSION['password_errors']) && !empty($_SESSION['password_errors'])) {
                        echo '<div class="alert alert-danger">';
                        echo '<ul class="mb-0">';
                        foreach ($_SESSION['password_errors'] as $error) {
                            echo '<li>' . $error . '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        
                        // Clear errors
                        unset($_SESSION['password_errors']);
                    }
                    ?>
                    
                    <form action="/admin/profile/password" method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">كلمة المرور الحالية <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <button type="button" class="neo-btn password-toggle" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <button type="button" class="neo-btn password-toggle" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">كلمة المرور يجب أن تكون على الأقل 8 أحرف.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="neo-btn password-toggle" data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="neo-btn neo-btn-primary">تغيير كلمة المرور</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle
        const passwordToggles = document.querySelectorAll('.password-toggle');
        
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.querySelector('i').classList.remove('fa-eye');
                    this.querySelector('i').classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.querySelector('i').classList.remove('fa-eye-slash');
                    this.querySelector('i').classList.add('fa-eye');
                }
            });
        });
    });
</script>

<?php
/**
 * Get role name in Arabic
 * 
 * @param string $role Role key
 * @return string Role name in Arabic
 */
function getRoleName($role) {
    $roles = [
        'admin' => 'مدير',
        'instructor' => 'مدرب',
        'data_entry' => 'مدخل بيانات'
    ];
    
    return $roles[$role] ?? $role;
}
?>