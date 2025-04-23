<?php
/**
 * Admin Header Template
 * 
 * This template displays the header for the admin panel.
 */

// Get the current admin
$adminAuth = new AdminAuth();
$currentAdmin = $adminAuth->getCurrentAdmin();
$isAdmin = $currentAdmin['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'لوحة التحكم'; ?> - لوحة التحكم</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-PfXw9JAY5VhCfJ3E8PsYT7ZUUWpMZa/F/eHERecCjw3yJ+1L1sXnbJb9OSaqnbZV" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Brutopia CSS -->
    <link href="https://cdn.jsdelivr.net/gh/rajnandan1/brutopia@latest/dist/assets/compiled/css/app.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Neo Brutalism CSS -->
    <link rel="stylesheet" href="/admin/assets/css/neo-brutalism.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- jQuery UI Touch Punch - enables touch support for jQuery UI -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="/assets/images/logo.png" alt="Logo" class="logo">
                <h3>لوحة التحكم</h3>
            </div>
            
            <div class="user-info">
                <div class="d-flex align-items-center">
                    <img src="/assets/images/avatar.png" alt="User Avatar" class="user-avatar">
                    <div>
                        <h5 class="mb-0"><?php echo strtoupper($currentAdmin['username']); ?></h5>
                        <small><?php echo $isAdmin ? 'مدير' : 'مدرب'; ?></small>
                    </div>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="/admin" class="<?php echo isCurrentPage('/admin') && !isCurrentPage('/admin/') ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                </li>
                <li>
                    <a href="/admin/courses" class="<?php echo isCurrentPage('/admin/courses') ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i> الدورات
                    </a>
                </li>
                <li>
                    <a href="/admin/profile" class="<?php echo isCurrentPage('/admin/profile') ? 'active' : ''; ?>">
                        <i class="fas fa-user-circle"></i> الملف الشخصي
                    </a>
                </li>
                <!-- Logout button removed from here to avoid duplication -->
            </ul>
            
            <div class="sidebar-footer">
                <a href="/admin/logout">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" id="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex ms-auto">
                        <div class="dropdown me-3">
                            <button class="btn btn-link dropdown-toggle position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                <li><a class="dropdown-item" href="#">تم تسجيل طالب جديد</a></li>
                                <li><a class="dropdown-item" href="#">تم إضافة تقييم جديد</a></li>
                                <li><a class="dropdown-item" href="#">تم تحديث النظام</a></li>
                            </ul>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="/assets/images/avatar.png" alt="User Avatar" class="user-avatar-sm me-2">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="/admin/profile">الملف الشخصي</a></li>
                                <li><a class="dropdown-item" href="/admin/settings">الإعدادات</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content -->
            <div class="container-fluid py-4">