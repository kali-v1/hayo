<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - لوحة التحكم' : 'لوحة التحكم'; ?></title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-PfXw9JAY5VhCfJ3E8PsYT7ZUUWpMZa/F/eHERecCjw3yJ+1L1sXnbJb9OSaqnbZV" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Brutopia CSS -->
    <link href="https://cdn.jsdelivr.net/gh/rajnandan1/brutopia@latest/dist/assets/compiled/css/app.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Neo Brutalism CSS -->
    <link rel="stylesheet" href="/admin/assets/css/neo-brutalism.css">
    
    <!-- CKEditor 4 with color support - updated to secure LTS version -->
    <script src="https://cdn.ckeditor.com/4.25.1-lts/full-all/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/full-all/lang/ar.js"></script>
    
    <style>
        /* Fix CKEditor height and make it responsive */
        .ck-editor__editable {
            min-height: 300px;
            max-height: 600px;
            direction: rtl;
        }
        
        /* Fix RTL issues in CKEditor */
        .ck.ck-editor__editable:not(.ck-editor__nested-editable) {
            text-align: right;
        }
        
        /* Add some spacing to color buttons */
        .ck.ck-color-ui-dropdown .ck-color-grid {
            grid-gap: 4px;
        }
        
        /* Make color swatches larger */
        .ck.ck-color-grid__tile {
            width: 24px;
            height: 24px;
        }
    </style>
    
    <?php if (isset($extraStyles)): ?>
    <?php echo $extraStyles; ?>
    <?php endif; ?>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-header">
                <img src="/assets/images/logo.png" alt="Logo" class="logo">
                <h3>لوحة التحكم</h3>
                <button id="sidebarCollapseBtn" class="d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-user">
                <div class="d-flex align-items-center">
                    <img src="/assets/images/avatar.png" alt="User Avatar" class="user-avatar">
                    <div class="user-info ms-3">
                        <h5><?php echo $_SESSION['admin_name'] ?? 'المدير'; ?></h5>
                        <span><?php echo getAdminRoleName($_SESSION['admin_role'] ?? 'admin'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <?php
                $menu = getAdminSidebarMenu();
                $currentUrl = $_SERVER['REQUEST_URI'];
                
                foreach ($menu as $item):
                    // Fix for dashboard active state
                    if ($item['url'] == '/admin') {
                        $isActive = ($currentUrl == '/admin' || $currentUrl == '/admin/');
                    } else {
                        $isActive = ($currentUrl == $item['url'] || strpos($currentUrl, $item['url'] . '/') === 0);
                    }
                ?>
                <div class="sidebar-item <?php echo $isActive ? 'active' : ''; ?>">
                    <a href="<?php echo $item['url']; ?>" class="sidebar-link">
                        <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                        <span><?php echo $item['title']; ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
                
                <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
                <!-- Employees section (admin only) -->
                <div class="sidebar-item <?php echo (strpos($currentUrl, '/admin/employees') === 0) ? 'active' : ''; ?>">
                    <a href="/admin/employees" class="sidebar-link">
                        <i class="fas fa-user-tie"></i>
                        <span>الموظفين</span>
                    </a>
                </div>
                
                <!-- Activity Logs section (admin only) -->
                <div class="sidebar-item <?php echo (strpos($currentUrl, '/admin/activity-logs') === 0) ? 'active' : ''; ?>">
                    <a href="/admin/activity-logs" class="sidebar-link">
                        <i class="fas fa-history"></i>
                        <span>سجل النشاطات</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="sidebar-footer">
                <a href="/admin/logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar">
                <div>
                    <button id="sidebarToggle" class="navbar-toggler">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end neo-card" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">إشعار 1</a></li>
                            <li><a class="dropdown-item" href="#">إشعار 2</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">عرض الكل</a></li>
                        </ul>
                    </div>
                    
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/assets/images/avatar.png" alt="User Avatar" class="user-avatar" style="width: 30px; height: 30px;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end neo-card" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/admin/profile">الملف الشخصي</a></li>
                            <li><a class="dropdown-item" href="/admin/settings">الإعدادات</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Content Wrapper -->
            <div class="content-wrapper">
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
                
                <!-- Main Content -->
                <?php echo $contentView; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- Admin JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            
            function toggleSidebar() {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('expanded');
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    content.classList.toggle('active');
                });
            }
            
            // Responsive sidebar
            function checkWidth() {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('collapsed');
                    content.classList.add('expanded');
                } else {
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('expanded');
                }
            }
            
            // Initial check
            checkWidth();
            
            // Check on resize
            window.addEventListener('resize', checkWidth);
        });
    </script>
    
    <?php if (isset($extraScripts)): ?>
    <?php echo $extraScripts; ?>
    <?php endif; ?>
</body>
</html>