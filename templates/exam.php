<div class="row">
    <div class="col-lg-8">
        <div class="brutalism-card bg-white p-4 shadow mb-4">
            <h1 class="fw-bold"><?php echo $exam['title']; ?></h1>
            
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <span class="badge bg-primary"><?php echo $exam['is_free'] ? translate('free') : translate('paid'); ?></span>
                </div>
                <div class="me-3">
                    <i class="fas fa-clock"></i>
                    <span><?php echo $exam['duration_minutes'] ?? $exam['duration'] ?? 60; ?> <?php echo translate('minutes'); ?></span>
                </div>
                <div>
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo translate('passing_score'); ?>: <?php echo $exam['passing_score']; ?>%</span>
                </div>
            </div>
            
            <div class="exam-description mb-4">
                <h3 class="fw-bold"><?php echo translate('about_this_exam'); ?></h3>
                <p><?php echo $exam['description']; ?></p>
            </div>
            
            <div class="exam-details mb-4">
                <h3 class="fw-bold"><?php echo translate('exam_details'); ?></h3>
                <ul>
                    <li><?php echo translate('total_questions'); ?>: <?php echo $questionCount ?? 0; ?></li>
                    <li><?php echo translate('question_types'); ?>: 
                        <?php 
                        $types = [];
                        $singleChoiceCount = $singleChoiceCount ?? 0;
                        $multipleChoiceCount = $multipleChoiceCount ?? 0;
                        $dragDropCount = $dragDropCount ?? 0;
                        if ($singleChoiceCount > 0) $types[] = translate('single_choice') . ' (' . $singleChoiceCount . ')';
                        if ($multipleChoiceCount > 0) $types[] = translate('multiple_choice') . ' (' . $multipleChoiceCount . ')';
                        if ($dragDropCount > 0) $types[] = translate('drag_drop') . ' (' . $dragDropCount . ')';
                        echo implode(', ', $types);
                        ?>
                    </li>
                    <li><?php echo translate('total_points'); ?>: <?php echo $totalPoints ?? 0; ?></li>
                    <li><?php echo translate('time_limit'); ?>: <?php echo $exam['duration_minutes'] ?? $exam['duration'] ?? 60; ?> <?php echo translate('minutes'); ?></li>
                </ul>
            </div>
            
            <?php if ($course): ?>
            <div class="exam-course mb-4">
                <h3 class="fw-bold"><?php echo translate('related_course'); ?></h3>
                <div class="d-flex align-items-center">
                    <div class="course-image me-3">
                        <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" alt="<?php echo $course['title']; ?>" class="img-fluid rounded" style="width: 100px;">
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0"><?php echo $course['title']; ?></h5>
                        <p class="text-muted mb-0"><?php echo substr($course['description'], 0, 100); ?>...</p>
                        <a href="/course/<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary mt-2"><?php echo translate('view_course'); ?></a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="exam-course mb-4">
                <h3 class="fw-bold"><?php echo translate('standalone_exam'); ?></h3>
                <div class="alert alert-info">
                    <p class="mb-0"><?php echo translate('standalone_exam_description'); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($recommendedCourses)): ?>
            <div class="recommended-courses mb-4">
                <h3 class="fw-bold"><?php echo translate('recommended_courses'); ?></h3>
                <div class="row">
                    <?php foreach ($recommendedCourses as $recCourse): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $recCourse['title']; ?></h5>
                                <?php if (!empty($recCourse['description'])): ?>
                                <p class="card-text"><?php echo substr($recCourse['description'], 0, 100); ?>...</p>
                                <?php endif; ?>
                                <a href="/course/<?php echo $recCourse['id']; ?>" class="btn btn-sm btn-outline-primary"><?php echo translate('view_course'); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($hasAccess) && $hasAccess): ?>
            <div class="d-grid gap-2">
                <a href="/exam/<?php echo $exam['id']; ?>/take" class="btn btn-primary btn-lg"><?php echo translate('start_exam'); ?></a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($userAttempts)): ?>
        <div class="brutalism-card bg-white p-4 shadow">
            <h3 class="fw-bold mb-3"><?php echo translate('your_attempts'); ?></h3>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo translate('date'); ?></th>
                            <th><?php echo translate('score'); ?></th>
                            <th><?php echo translate('result'); ?></th>
                            <th><?php echo translate('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userAttempts as $index => $attempt): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo formatDate($attempt['completed_at'], 'M d, Y H:i'); ?></td>
                            <td><?php echo $attempt['score']; ?>%</td>
                            <td>
                                <?php if ($attempt['score'] >= $exam['passing_score']): ?>
                                <span class="badge bg-success"><?php echo translate('passed'); ?></span>
                                <?php else: ?>
                                <span class="badge bg-danger"><?php echo translate('failed'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/exam/<?php echo $exam['id']; ?>/result/<?php echo $attempt['id']; ?>" class="btn btn-sm btn-primary"><?php echo translate('view_result'); ?></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="brutalism-card bg-white p-4 shadow mb-4 sticky-top" style="top: 20px;">
            <?php if (isset($hasAccess) && $hasAccess): ?>
            <div class="d-grid gap-2 mb-3">
                <a href="/exam/<?php echo $exam['id']; ?>/take" class="btn btn-primary btn-lg"><?php echo translate('start_exam'); ?></a>
            </div>
            <?php else: ?>
                <?php if ($exam['is_free']): ?>
                <div class="d-grid gap-2 mb-3">
                    <a href="/exam/<?php echo $exam['id']; ?>/enroll" class="btn btn-success btn-lg"><?php echo translate('enroll_for_free'); ?></a>
                </div>
                <?php else: ?>
                    <?php if ($course): ?>
                    <div class="alert alert-warning">
                        <p class="mb-0"><?php echo translate('exam_requires_course_purchase'); ?></p>
                    </div>
                    <div class="d-grid gap-2 mb-3">
                        <a href="/course/<?php echo $course['id']; ?>" class="btn btn-primary btn-lg"><?php echo translate('view_course'); ?></a>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <p class="mb-0"><?php echo translate('exam_requires_login'); ?></p>
                    </div>
                    <div class="d-grid gap-2 mb-3">
                        <a href="/login" class="btn btn-primary btn-lg"><?php echo translate('login'); ?></a>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="exam-stats">
                <h4 class="fw-bold mb-3"><?php echo translate('exam_statistics'); ?></h4>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-users me-2"></i> <?php echo $attemptCount; ?> <?php echo translate('total_attempts'); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-chart-line me-2"></i> <?php echo $averageScore; ?>% <?php echo translate('average_score'); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $passRate; ?>% <?php echo translate('pass_rate'); ?>
                    </li>
                </ul>
            </div>
            
            <div class="exam-tips mt-4 pt-4 border-top">
                <h4 class="fw-bold mb-3"><?php echo translate('exam_tips'); ?></h4>
                <ul>
                    <li><?php echo translate('exam_tip_1'); ?></li>
                    <li><?php echo translate('exam_tip_2'); ?></li>
                    <li><?php echo translate('exam_tip_3'); ?></li>
                    <li><?php echo translate('exam_tip_4'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>