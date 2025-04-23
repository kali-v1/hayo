<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="fw-bold"><?php echo translate('profile'); ?></h1>
        <p class="lead"><?php echo translate('profile_subtitle'); ?></p>
    </div>
</div>

<div class="row">
    <!-- User Profile Card -->
    <div class="col-md-4 mb-4">
        <div class="brutalism-card bg-white p-4 shadow">
            <div class="text-center mb-4">
                <?php if ($user->getProfileImage()): ?>
                    <img src="/assets/images/users/<?php echo $user->getProfileImage(); ?>" alt="<?php echo $user->getFullName(); ?>" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 4rem;">
                        <?php echo strtoupper(substr($user->getFirstName() ?? '', 0, 1) . substr($user->getLastName() ?? '', 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <h3 class="mt-3 mb-1"><?php echo $user->getFullName(); ?></h3>
                <p class="text-muted"><?php echo $user->getUsername(); ?></p>
            </div>
            
            <div class="mb-4">
                <?php if ($user->getBio()): ?>
                    <p><?php echo $user->getBio(); ?></p>
                <?php else: ?>
                    <p class="text-muted"><?php echo translate('no_bio'); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <h5 class="fw-bold"><?php echo translate('contact_info'); ?></h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i> <?php echo $user->getEmail(); ?>
                    </li>
                    <?php if ($user->getPhone()): ?>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i> <?php echo $user->getPhone(); ?>
                    </li>
                    <?php endif; ?>
                    <?php if ($user->getAddress()): ?>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i> 
                        <?php echo $user->getAddress(); ?>
                        <?php if ($user->getCity()): ?>, <?php echo $user->getCity(); ?><?php endif; ?>
                        <?php if ($user->getCountry()): ?>, <?php echo $user->getCountry(); ?><?php endif; ?>
                        <?php if ($user->getPostalCode()): ?> <?php echo $user->getPostalCode(); ?><?php endif; ?>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <?php if ($user->getWebsite() || $user->getFacebook() || $user->getTwitter() || $user->getLinkedin() || $user->getInstagram()): ?>
            <div class="mb-4">
                <h5 class="fw-bold"><?php echo translate('social_profiles'); ?></h5>
                <div class="d-flex flex-wrap">
                    <?php if ($user->getWebsite()): ?>
                    <a href="<?php echo $user->getWebsite(); ?>" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                        <i class="fas fa-globe"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($user->getFacebook()): ?>
                    <a href="https://facebook.com/<?php echo $user->getFacebook(); ?>" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($user->getTwitter()): ?>
                    <a href="https://twitter.com/<?php echo $user->getTwitter(); ?>" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($user->getLinkedin()): ?>
                    <a href="https://linkedin.com/in/<?php echo $user->getLinkedin(); ?>" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($user->getInstagram()): ?>
                    <a href="https://instagram.com/<?php echo $user->getInstagram(); ?>" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2">
                <a href="/profile/edit" class="btn btn-primary"><?php echo translate('edit_profile'); ?></a>
                <a href="/profile/password" class="btn btn-outline-primary"><?php echo translate('change_password'); ?></a>
            </div>
        </div>
    </div>
    
    <!-- User Stats and Activity -->
    <div class="col-md-8">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="brutalism-card bg-primary text-white p-3 text-center">
                    <h4><?php echo count($enrolledCourses); ?></h4>
                    <p class="mb-0"><?php echo translate('enrolled_courses'); ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="brutalism-card bg-success text-white p-3 text-center">
                    <?php 
                    $completedCourses = 0;
                    foreach ($enrolledCourses as $course) {
                        if (isset($course['progress']) && $course['progress'] >= 100) {
                            $completedCourses++;
                        }
                    }
                    ?>
                    <h4><?php echo $completedCourses; ?></h4>
                    <p class="mb-0"><?php echo translate('completed_courses'); ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="brutalism-card bg-info text-white p-3 text-center">
                    <h4><?php echo count($certificates); ?></h4>
                    <p class="mb-0"><?php echo translate('certificates'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Enrolled Courses -->
        <div class="brutalism-card bg-white p-4 shadow mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold"><?php echo translate('enrolled_courses'); ?></h3>
                <a href="/my-courses" class="btn btn-sm btn-primary"><?php echo translate('view_all'); ?></a>
            </div>
            
            <?php if (empty($enrolledCourses)): ?>
                <p class="text-muted"><?php echo translate('no_enrolled_courses_message'); ?></p>
            <?php else: ?>
                <div class="row">
                    <?php 
                    // Display only the first 3 courses
                    $displayCourses = array_slice($enrolledCourses, 0, 3);
                    foreach ($displayCourses as $course): 
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" class="card-img-top" alt="<?php echo $course['title']; ?>" style="height: 140px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $course['title']; ?></h5>
                                <div class="progress mb-2">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $course['progress'] ?? 0; ?>%" aria-valuenow="<?php echo $course['progress'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="small text-muted"><?php echo $course['progress'] ?? 0; ?>% <?php echo translate('completed'); ?></p>
                                <a href="/course/<?php echo $course['id']; ?>" class="btn btn-sm btn-primary"><?php echo translate('continue_learning'); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Exam Attempts -->
        <div class="brutalism-card bg-white p-4 shadow mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold"><?php echo translate('recent_exams'); ?></h3>
                <a href="/my-exams" class="btn btn-sm btn-primary"><?php echo translate('view_all'); ?></a>
            </div>
            
            <?php if (empty($examAttempts)): ?>
                <p class="text-muted"><?php echo translate('no_exam_attempts_message'); ?></p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo translate('exam'); ?></th>
                                <th><?php echo translate('date'); ?></th>
                                <th><?php echo translate('score'); ?></th>
                                <th><?php echo translate('result'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Display only the first 3 exam attempts
                            $displayAttempts = array_slice($examAttempts, 0, 3);
                            foreach ($displayAttempts as $attempt): 
                                // Determine if passed based on score and passing_score
                                $isPassed = isset($attempt['passed']) ? $attempt['passed'] : 
                                           (isset($attempt['score']) && isset($attempt['passing_score']) ? 
                                           $attempt['score'] >= $attempt['passing_score'] : 
                                           ($attempt['score'] >= 70)); // Default passing score is 70%
                            ?>
                            <tr>
                                <td><?php echo $attempt['exam_title']; ?></td>
                                <td><?php echo isset($attempt['attempt_date']) ? formatDate($attempt['attempt_date']) : formatDate($attempt['started_at'] ?? date('Y-m-d H:i:s')); ?></td>
                                <td><?php echo $attempt['score']; ?>%</td>
                                <td>
                                    <?php if ($isPassed): ?>
                                    <span class="badge bg-success"><?php echo translate('passed'); ?></span>
                                    <?php else: ?>
                                    <span class="badge bg-danger"><?php echo translate('failed'); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Certificates -->
        <div class="brutalism-card bg-white p-4 shadow">
            <h3 class="fw-bold mb-3"><?php echo translate('certificates'); ?></h3>
            
            <?php if (empty($certificates)): ?>
                <p class="text-muted"><?php echo translate('no_certificates_message'); ?></p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($certificates as $certificate): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $certificate['course_title']; ?> <?php echo translate('certificate'); ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?php echo translate('issued_on'); ?>: <?php echo formatDate($certificate['issue_date']); ?>
                                    </small>
                                </p>
                                <a href="/certificate/<?php echo $certificate['id']; ?>" class="btn btn-sm btn-primary"><?php echo translate('view_certificate'); ?></a>
                                <a href="/certificate/<?php echo $certificate['id']; ?>/download" class="btn btn-sm btn-outline-primary"><?php echo translate('download'); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>