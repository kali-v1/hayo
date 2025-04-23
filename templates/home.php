<!-- Hero Section -->
<section class="hero-section py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="brutalism-card bg-primary text-white p-4 p-md-5 shadow-lg">
                    <h1 class="display-4 fw-bold"><?php echo translate('hero_title'); ?></h1>
                    <p class="lead"><?php echo translate('hero_subtitle'); ?></p>
                    <div class="mt-4">
                        <a href="/courses" class="btn btn-warning btn-lg me-2"><?php echo translate('explore_courses'); ?></a>
                        <a href="/register" class="btn btn-outline-light btn-lg"><?php echo translate('get_started'); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="brutalism-card bg-warning p-4 shadow-lg">
                    <img src="/assets/images/hero-image.svg" alt="Certification Platform" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold"><?php echo translate('why_choose_us'); ?></h2>
            <p class="lead"><?php echo translate('features_subtitle'); ?></p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="brutalism-card bg-success text-white p-4 h-100 shadow">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-certificate fa-3x"></i>
                    </div>
                    <h3 class="fw-bold"><?php echo translate('feature_1_title'); ?></h3>
                    <p><?php echo translate('feature_1_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="brutalism-card bg-info text-white p-4 h-100 shadow">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-laptop-code fa-3x"></i>
                    </div>
                    <h3 class="fw-bold"><?php echo translate('feature_2_title'); ?></h3>
                    <p><?php echo translate('feature_2_desc'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="brutalism-card bg-danger text-white p-4 h-100 shadow">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-chart-line fa-3x"></i>
                    </div>
                    <h3 class="fw-bold"><?php echo translate('feature_3_title'); ?></h3>
                    <p><?php echo translate('feature_3_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Courses Section -->
<section class="courses-section py-5 mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><?php echo translate('popular_courses'); ?></h2>
            <a href="/courses" class="btn btn-primary"><?php echo translate('view_all_courses'); ?></a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($popularCourses as $course): ?>
            <div class="col-md-4">
                <div class="brutalism-card bg-white p-0 shadow h-100">
                    <div class="course-image">
                        <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" alt="<?php echo $course['title']; ?>" class="img-fluid w-100">
                        <?php if ($course['is_free']): ?>
                        <span class="badge bg-success position-absolute top-0 end-0 m-2"><?php echo translate('free'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="fw-bold"><?php echo $course['title']; ?></h3>
                        <p class="text-muted"><?php echo substr($course['description'], 0, 100); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <?php if (!$course['is_free']): ?>
                                <span class="fw-bold text-primary"><?php echo formatCurrency($course['price']); ?></span>
                                <?php else: ?>
                                <span class="fw-bold text-success"><?php echo translate('free'); ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="/course/<?php echo $course['id']; ?>" class="btn btn-primary"><?php echo translate('view_course'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5 mb-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold"><?php echo translate('what_our_students_say'); ?></h2>
            <p class="lead"><?php echo translate('testimonials_subtitle'); ?></p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="col-md-4">
                <div class="brutalism-card bg-white p-4 h-100 shadow">
                    <div class="d-flex align-items-center mb-3">
                        <div class="testimonial-avatar me-3">
                            <?php if ($testimonial['profile_image']): ?>
                            <img src="/assets/images/users/<?php echo $testimonial['profile_image']; ?>" alt="<?php echo $testimonial['name']; ?>" class="rounded-circle" width="60" height="60">
                            <?php else: ?>
                            <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <?php echo substr($testimonial['name'], 0, 1); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo $testimonial['name']; ?></h5>
                            <p class="text-muted mb-0"><?php echo $testimonial['course']; ?></p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text"><?php echo $testimonial['review']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 mb-5">
    <div class="container">
        <div class="brutalism-card bg-primary text-white p-5 text-center shadow-lg">
            <h2 class="fw-bold mb-3"><?php echo translate('ready_to_start'); ?></h2>
            <p class="lead mb-4"><?php echo translate('cta_subtitle'); ?></p>
            <div>
                <a href="/register" class="btn btn-warning btn-lg me-2"><?php echo translate('sign_up_now'); ?></a>
                <a href="/courses" class="btn btn-outline-light btn-lg"><?php echo translate('browse_courses'); ?></a>
            </div>
        </div>
    </div>
</section>