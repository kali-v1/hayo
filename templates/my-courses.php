<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="fw-bold"><?php echo translate('my_courses'); ?></h1>
        <p class="lead"><?php echo translate('my_courses_subtitle'); ?></p>
    </div>
</div>

<?php if (empty($enrolledCourses)): ?>
<div class="brutalism-card bg-white p-5 text-center shadow">
    <h3 class="fw-bold"><?php echo translate('no_enrolled_courses'); ?></h3>
    <p><?php echo translate('no_enrolled_courses_message'); ?></p>
    <a href="/courses" class="btn btn-primary mt-3"><?php echo translate('browse_courses'); ?></a>
</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($enrolledCourses as $course): ?>
    <div class="col-md-4">
        <div class="brutalism-card bg-white p-0 shadow h-100">
            <div class="course-image">
                <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" alt="<?php echo $course['title']; ?>" class="img-fluid w-100">
                <?php if ($course['is_free']): ?>
                <span class="badge bg-success position-absolute top-0 end-0 m-2"><?php echo translate('free'); ?></span>
                <?php endif; ?>
                <?php if (isset($course['progress']) && $course['progress'] == 100): ?>
                <span class="badge bg-primary position-absolute top-0 start-0 m-2"><?php echo translate('completed'); ?></span>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <h3 class="fw-bold"><?php echo $course['title']; ?></h3>
                <p class="text-muted"><?php echo substr($course['description'], 0, 100); ?>...</p>
                
                <?php if (isset($course['progress'])): ?>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $course['progress']; ?>%;" 
                         aria-valuenow="<?php echo $course['progress']; ?>" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <p class="text-center mt-1"><?php echo $course['progress']; ?>% <?php echo translate('completed'); ?></p>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <?php if (!$course['is_free']): ?>
                        <span class="fw-bold text-primary"><?php echo formatCurrency($course['price']); ?></span>
                        <?php else: ?>
                        <span class="fw-bold text-success"><?php echo translate('free'); ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="/course/<?php echo $course['id']; ?>" class="btn btn-primary"><?php echo translate('continue_learning'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>