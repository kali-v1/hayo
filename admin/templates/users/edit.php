<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">تعديل المستخدم</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/users">المستخدمين</a></li>
                    <li class="breadcrumb-item active">تعديل المستخدم</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">معلومات المستخدم</h5>
        </div>
        <div class="card-body">
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
            $form_data = $_SESSION['form_data'] ?? $user;
            unset($_SESSION['form_data']);
            ?>
            
            <form action="/admin/users/update/<?php echo $user['id']; ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>
                            <div class="form-text">اسم المستخدم يجب أن يكون فريدًا ويحتوي على أحرف وأرقام فقط.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور <small class="text-muted">(اتركها فارغة إذا لم ترغب في تغييرها)</small></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <button type="button" class="btn btn-outline-secondary password-toggle" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">كلمة المرور يجب أن تكون على الأقل 8 أحرف.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                <button type="button" class="btn btn-outline-secondary password-toggle" data-target="confirm_password">
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
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mobile_number" class="form-label">رقم الجوال (مع رمز الدولة)</label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                <input type="text" class="form-control" id="mobile_number" name="mobile_number" 
                                    placeholder="966501234567" 
                                    value="<?php 
                                        $mobile = $form_data['mobile_number'] ?? '';
                                        // Remove the + if it exists
                                        echo htmlspecialchars(ltrim($mobile, '+'));
                                    ?>">
                            </div>
                            <div class="form-text">أدخل رقم الجوال مع رمز الدولة بدون الرمز + (مثال: 966501234567)</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?php echo (isset($form_data['status']) && $form_data['status'] === 'active') ? 'selected' : ''; ?>>نشط</option>
                                <option value="inactive" <?php echo (isset($form_data['status']) && $form_data['status'] === 'inactive') ? 'selected' : ''; ?>>غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="role" value="<?php echo htmlspecialchars($form_data['role'] ?? 'user'); ?>">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/users" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>