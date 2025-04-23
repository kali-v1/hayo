<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-PfXw9JAY5VhCfJ3E8PsYT7ZUUWpMZa/F/eHERecCjw3yJ+1L1sXnbJb9OSaqnbZV" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Brutopia CSS -->
    <link href="https://cdn.jsdelivr.net/gh/rajnandan1/brutopia@latest/dist/assets/compiled/css/app.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Neo Brutalism CSS -->
    <link rel="stylesheet" href="/admin/assets/css/neo-brutalism.css">
    
    <style>
        body {
            background-color: #f0f0f0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <h1 class="neo-text-primary">لوحة التحكم</h1>
            <p class="neo-badge neo-badge-secondary">منصة الشهادات والاختبارات</p>
        </div>
        
        <?php
        // Display flash message if any
        $flashMessage = getAdminFlashMessage();
        if ($flashMessage) {
            $alertClass = 'neo-alert-info';
            
            switch ($flashMessage['type']) {
                case 'success':
                    $alertClass = 'neo-alert-success';
                    break;
                case 'error':
                    $alertClass = 'neo-alert-danger';
                    break;
                case 'warning':
                    $alertClass = 'neo-alert-warning';
                    break;
            }
        ?>
        <div class="neo-alert <?php echo $alertClass; ?>">
            <?php echo $flashMessage['message']; ?>
        </div>
        <?php } ?>
        
        <?php if (isset($error)): ?>
        <div class="neo-alert neo-alert-danger" style="background-color: #ffdddd; border: 2px solid #ff0000; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">
            <i class="fas fa-exclamation-triangle" style="color: #ff0000; margin-left: 5px; font-size: 18px;"></i>
            <strong style="color: #cc0000;"><?php echo $error; ?></strong>
        </div>
        <?php endif; ?>
        
        <div class="login-card neo-card">
            <h4 class="text-center mb-4 fw-bold">تسجيل الدخول</h4>
            
            <form action="/admin/auth" method="post">
                <div class="mb-3">
                    <label for="username" class="neo-label">اسم المستخدم</label>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2 bg-warning p-2 border border-dark border-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="text" class="neo-input" id="username" name="username" required autofocus>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="neo-label">كلمة المرور</label>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2 bg-warning p-2 border border-dark border-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" class="neo-input" id="password" name="password" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <input type="checkbox" id="remember" name="remember" class="me-2 border border-dark border-3">
                        <label for="remember" class="fw-bold">تذكرني</label>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="neo-btn neo-btn-primary">تسجيل الدخول</button>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-3">
            <a href="/" class="neo-btn">العودة إلى الموقع الرئيسي</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>