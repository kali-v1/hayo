<!DOCTYPE html>
<html lang="<?php echo $lang ?? 'en'; ?>" dir="<?php echo $direction ?? 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Neo Brutalism CSS -->
    <link href="https://cdn.jsdelivr.net/gh/rajnandan1/brutopia@latest/dist/assets/compiled/css/app.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <?php if (($direction ?? 'ltr') === 'rtl'): ?>
    <!-- RTL CSS - Only for direction changes -->
    <link href="/assets/css/rtl.css" rel="stylesheet">
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-PJsj/BTMqILvmcej7ulplguok8ag4xFTPryRq8xevL7eBYSmpXKcbNVuy+P0RMgq" crossorigin="anonymous">
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="header position-relative">
        <div class="container h-100">
            <nav class="navbar navbar-expand-lg h-100" style="height: 70px !important;">
                <div class="container-fluid p-0">
                    <a class="navbar-brand" href="/">
                        <span><?php echo APP_NAME; ?></span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('/') ? 'active' : ''; ?>" href="/">
                                    <i class="fas fa-home <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i> <?php echo translate('home'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('/courses') ? 'active' : ''; ?>" href="/courses">
                                    <i class="fas fa-graduation-cap <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i> <?php echo translate('courses'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('/exams') ? 'active' : ''; ?>" href="/exams">
                                    <i class="fas fa-clipboard-check <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i> <?php echo translate('exams'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('/leaderboard') ? 'active' : ''; ?>" href="/leaderboard">
                                    <i class="fas fa-trophy <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i> <?php echo translate('leaderboard'); ?>
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-globe <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i>
                                    <?php echo ($lang ?? 'en') === 'en' ? 'English' : 'العربية'; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                                    <li><a class="dropdown-item" href="<?php echo addLanguageToUrl('en'); ?>"><i class="fas fa-flag <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-2' : 'me-2'; ?>"></i> English</a></li>
                                    <li><a class="dropdown-item" href="<?php echo addLanguageToUrl('ar'); ?>"><i class="fas fa-flag <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-2' : 'me-2'; ?>"></i> العربية</a></li>
                                </ul>
                            </li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i>
                                    <?php echo $_SESSION['username']; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="/profile"><i class="fas fa-id-card <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-2' : 'me-2'; ?>"></i> <?php echo translate('profile'); ?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-2' : 'me-2'; ?>"></i> <?php echo translate('logout'); ?></a></li>
                                </ul>
                            </li>
                            <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('/login') ? 'active' : ''; ?>" href="/login">
                                    <i class="fas fa-sign-in-alt <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i> <?php echo translate('login'); ?>
                                </a>
                            </li>
                            <li class="nav-item <?php echo ($direction ?? 'ltr') === 'rtl' ? 'me-2' : 'ms-2'; ?>">
                                <a class="nav-link <?php echo isCurrentPage('/register') ? 'active' : ''; ?>" href="/register">
                                    <i class="fas fa-user-plus <?php echo ($direction ?? 'ltr') === 'rtl' ? 'ms-1' : 'me-1'; ?>"></i> <?php echo translate('register'); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">