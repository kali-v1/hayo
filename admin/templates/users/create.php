<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إضافة مستخدم جديد</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/users">المستخدمين</a></li>
                    <li class="breadcrumb-item active">إضافة مستخدم</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <h5 class="fw-bold mb-3">معلومات المستخدم</h5>
        
        <?php
        // Display errors if any
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            echo '<div class="neo-alert neo-alert-danger mb-3">';
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
        $form_data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        ?>
            
            <form action="/admin/users/store" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control neo-input" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>
                            <div class="form-text">اسم المستخدم يجب أن يكون فريدًا ويحتوي على أحرف وأرقام فقط.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control neo-input" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control neo-input" id="password" name="password" required>
                                <button type="button" class="neo-btn neo-btn-outline-secondary password-toggle" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">كلمة المرور يجب أن تكون على الأقل 8 أحرف.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control neo-input" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="neo-btn neo-btn-outline-secondary password-toggle" data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" class="form-control neo-input" id="first_name" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" class="form-control neo-input" id="last_name" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control neo-input" id="phone" name="phone" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mobile_number" class="form-label">رقم الجوال</label>
                            <input type="text" class="form-control neo-input" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($form_data['mobile_number'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select neo-select" id="status" name="status">
                                <option value="active" <?php echo (isset($form_data['status']) && $form_data['status'] === 'active') ? 'selected' : ''; ?>>نشط</option>
                                <option value="inactive" <?php echo (isset($form_data['status']) && $form_data['status'] === 'inactive') ? 'selected' : ''; ?>>غير نشط</option>
                            </select>
                        </div>
                    </div>
                    
                    <input type="hidden" name="role" value="user">
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/users" class="neo-btn neo-btn-secondary">إلغاء</a>
                    <button type="submit" class="neo-btn neo-btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
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