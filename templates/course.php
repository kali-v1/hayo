<div class="row">
    <div class="col-lg-8">
        <div class="brutalism-card bg-white p-4 shadow mb-4">
            <h1 class="fw-bold"><?php echo $course['title']; ?></h1>
            
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <span class="badge bg-primary"><?php echo $course['is_free'] ? translate('free') : translate('paid'); ?></span>
                </div>
                <div class="me-3">
                    <i class="fas fa-star text-warning"></i>
                    <span><?php echo number_format($courseRating, 1); ?> (<?php echo $ratingCount; ?> <?php echo translate('reviews'); ?>)</span>
                </div>
                <div>
                    <i class="fas fa-users"></i>
                    <span><?php echo $enrollmentCount; ?> <?php echo translate('students'); ?></span>
                </div>
            </div>
            
            <div class="course-image mb-4">
                <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" alt="<?php echo $course['title']; ?>" class="img-fluid w-100 rounded">
            </div>
            
            <div class="course-description mb-4">
                <h3 class="fw-bold"><?php echo translate('about_this_course'); ?></h3>
                <p><?php echo $course['description']; ?></p>
            </div>
            
            <div class="course-instructor mb-4">
                <h3 class="fw-bold"><?php echo translate('instructor'); ?></h3>
                <div class="d-flex align-items-center">
                    <div class="instructor-avatar me-3">
                        <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <?php echo substr($instructor['name'], 0, 1); ?>
                        </div>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0"><?php echo $instructor['name']; ?></h5>
                        <p class="text-muted mb-0"><?php echo translate('instructor'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Exams Section -->
        <div class="brutalism-card bg-white p-4 shadow mb-4">
            <h3 class="fw-bold mb-3"><?php echo translate('course_exams'); ?></h3>
            
            <?php if (empty($exams)): ?>
            <p><?php echo translate('no_exams_available'); ?></p>
            <?php else: ?>
            <div class="list-group">
                <?php foreach ($exams as $exam): ?>
                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><?php echo $exam['title']; ?></h5>
                        <p class="mb-1 text-muted"><?php echo substr($exam['description'], 0, 100); ?>...</p>
                        <small>
                            <i class="fas fa-clock"></i> <?php echo $exam['duration_minutes']; ?> <?php echo translate('minutes'); ?>
                            <i class="fas fa-check-circle ms-3"></i> <?php echo translate('passing_score'); ?>: <?php echo $exam['passing_score']; ?>%
                        </small>
                    </div>
                    <div>
                        <?php if ($exam['is_free'] || $hasAccess): ?>
                        <a href="/exam/<?php echo $exam['id']; ?>" class="btn btn-primary"><?php echo translate('view_exam'); ?></a>
                        <?php else: ?>
                        <span class="badge bg-warning"><?php echo translate('requires_purchase'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Reviews Section -->
        <div class="brutalism-card bg-white p-4 shadow">
            <h3 class="fw-bold mb-3"><?php echo translate('student_reviews'); ?></h3>
            
            <?php if (empty($reviews)): ?>
            <p><?php echo translate('no_reviews_yet'); ?></p>
            <?php else: ?>
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-3">
                        <span class="display-4 fw-bold"><?php echo number_format($courseRating, 1); ?></span>
                    </div>
                    <div>
                        <div class="mb-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= round($courseRating) ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="mb-0"><?php echo $ratingCount; ?> <?php echo translate('reviews'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="reviews-list">
                <?php foreach ($reviews as $review): ?>
                <div class="review-item mb-4 pb-4 border-bottom">
                    <div class="d-flex align-items-center mb-2">
                        <div class="review-avatar me-3">
                            <?php if ($review['profile_image']): ?>
                            <img src="/assets/images/users/<?php echo $review['profile_image']; ?>" alt="<?php echo $review['username']; ?>" class="rounded-circle" width="50" height="50">
                            <?php else: ?>
                            <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <?php echo substr($review['username'], 0, 1); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo $review['username']; ?></h5>
                            <p class="text-muted mb-0"><?php echo formatDate($review['created_at'], 'M d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p><?php echo $review['review']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($totalReviews > count($reviews)): ?>
            <div class="text-center">
                <a href="/course/<?php echo $course['id']; ?>/reviews" class="btn btn-outline-primary"><?php echo translate('view_all_reviews'); ?></a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php $auth = new Auth(); if ($auth->isLoggedIn() && $hasAccess && !$hasReviewed): ?>
            <div class="mt-4 pt-4 border-top">
                <h4 class="fw-bold mb-3"><?php echo translate('write_a_review'); ?></h4>
                <form action="/course/<?php echo $course['id']; ?>/review" method="post">
                    <div class="mb-3">
                        <label class="form-label"><?php echo translate('your_rating'); ?></label>
                        <div class="rating-stars mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating1" value="1" required>
                                <label class="form-check-label" for="rating1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                                <label class="form-check-label" for="rating2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating3" value="3">
                                <label class="form-check-label" for="rating3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                                <label class="form-check-label" for="rating4">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                                <label class="form-check-label" for="rating5">5</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="review" class="form-label"><?php echo translate('your_review'); ?></label>
                        <textarea class="form-control" id="review" name="review" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo translate('submit_review'); ?></button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="brutalism-card bg-white p-4 shadow mb-4 sticky-top" style="top: 20px;">
            <?php if (!$course['is_free']): ?>
            <div class="price-tag mb-3">
                <h2 class="fw-bold text-primary"><?php echo formatCurrency($course['price']); ?></h2>
            </div>
            <?php else: ?>
            <div class="price-tag mb-3">
                <h2 class="fw-bold text-success"><?php echo translate('free'); ?></h2>
            </div>
            <?php endif; ?>
            
            <?php if ($auth->isLoggedIn()): ?>
                <?php if ($hasAccess): ?>
                <div class="d-grid gap-2 mb-3">
                    <a href="/course/<?php echo $course['id']; ?>/start" class="btn btn-success btn-lg"><?php echo translate('start_learning'); ?></a>
                </div>
                <?php else: ?>
                <div class="d-grid gap-2 mb-3">
                    <a href="/course/<?php echo $course['id']; ?>/enroll" class="btn btn-primary btn-lg"><?php echo $course['is_free'] ? translate('enroll_for_free') : translate('buy_now'); ?></a>
                </div>
                <?php endif; ?>
            <?php else: ?>
            <div class="d-grid gap-2 mb-3">
                <a href="/login" class="btn btn-primary btn-lg"><?php echo translate('login_to_enroll'); ?></a>
                <a href="/register" class="btn btn-outline-primary"><?php echo translate('register'); ?></a>
            </div>
            <?php endif; ?>
            
            <div class="course-info">
                <h4 class="fw-bold mb-3"><?php echo translate('course_includes'); ?></h4>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-file-alt me-2"></i> <?php echo $examCount; ?> <?php echo translate('practice_exams'); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-question-circle me-2"></i> <?php echo $questionCount; ?> <?php echo translate('practice_questions'); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-infinity me-2"></i> <?php echo translate('full_lifetime_access'); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-certificate me-2"></i> <?php echo translate('certificate_of_completion'); ?>
                    </li>
                </ul>
            </div>
            
            <div class="share-course mt-4 pt-4 border-top">
                <h4 class="fw-bold mb-3"><?php echo translate('share_this_course'); ?></h4>
                <div class="d-flex">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(getCurrentUrl()); ?>" target="_blank" class="btn btn-outline-primary me-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(getCurrentUrl()); ?>&text=<?php echo urlencode($course['title']); ?>" target="_blank" class="btn btn-outline-primary me-2">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(getCurrentUrl()); ?>&title=<?php echo urlencode($course['title']); ?>" target="_blank" class="btn btn-outline-primary me-2">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="mailto:?subject=<?php echo urlencode($course['title']); ?>&body=<?php echo urlencode('Check out this course: ' . getCurrentUrl()); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>