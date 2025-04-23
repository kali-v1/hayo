<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>" dir="<?php echo getDirection(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Certification Platform</title>
    
    <!-- Neo Brutalism CSS -->
    <link href="https://cdn.jsdelivr.net/gh/rajnandan1/brutopia@latest/dist/assets/compiled/css/app.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <?php if (isRtl()): ?>
    <!-- RTL Support -->
    <link rel="stylesheet" href="/assets/css/rtl.css">
    <?php endif; ?>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    <span class="text-warning">CERT</span><span class="text-light">PLATFORM</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/"><?php echo translate('home'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/courses"><?php echo translate('courses'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/exams"><?php echo translate('exams'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/leaderboard"><?php echo translate('leaderboard'); ?></a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php 
                        $auth = new Auth();
                        if ($auth->isLoggedIn()): 
                            $currentUser = $auth->getCurrentUser();
                        ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo htmlspecialchars($currentUser['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="/profile"><?php echo translate('profile'); ?></a></li>
                                    <li><a class="dropdown-item" href="/my-courses"><?php echo translate('my_courses'); ?></a></li>
                                    <li><a class="dropdown-item" href="/my-exams"><?php echo translate('my_exams'); ?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/logout"><?php echo translate('logout'); ?></a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/login"><?php echo translate('login'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/register"><?php echo translate('register'); ?></a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo getCurrentLanguage() === 'ar' ? 'العربية' : 'English'; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                <li><a class="dropdown-item" href="?lang=en">English</a></li>
                                <li><a class="dropdown-item" href="?lang=ar">العربية</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container py-4">
        <?php
        // Display flash message if any
        $flashMessage = getFlashMessage();
        if ($flashMessage) {
            $alertClass = 'alert-info';
            
            switch ($flashMessage['type']) {
                case 'success':
                    $alertClass = 'alert-success';
                    break;
                case 'error':
                    $alertClass = 'alert-danger';
                    break;
                case 'warning':
                    $alertClass = 'alert-warning';
                    break;
            }
        ?>
        <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
            <?php echo $flashMessage['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php } ?>
        
        <?php include $contentTemplate; ?>
    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="text-warning"><?php echo translate('about_us'); ?></h5>
                    <p><?php echo translate('footer_about'); ?></p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-warning"><?php echo translate('quick_links'); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-light"><?php echo translate('home'); ?></a></li>
                        <li><a href="/courses" class="text-light"><?php echo translate('courses'); ?></a></li>
                        <li><a href="/exams" class="text-light"><?php echo translate('exams'); ?></a></li>
                        <li><a href="/contact" class="text-light"><?php echo translate('contact'); ?></a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-warning"><?php echo translate('contact_us'); ?></h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt"></i> 123 Certification St, Tech City</p>
                        <p><i class="fas fa-envelope"></i> info@certplatform.com</p>
                        <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    </address>
                </div>
            </div>
            <hr class="bg-light">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> Certification Platform. <?php echo translate('all_rights_reserved'); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/privacy-policy" class="text-light me-3"><?php echo translate('privacy_policy'); ?></a>
                    <a href="/terms-of-service" class="text-light"><?php echo translate('terms_of_service'); ?></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="/assets/js/script.js"></script>
</body>
</html>