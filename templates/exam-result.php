<?php include 'partials/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><?php echo translate('exam_result'); ?>: <?php echo htmlspecialchars($exam['title']); ?></h2>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <?php if ($result['passed']): ?>
                        <div class="alert alert-success">
                            <h3 class="h5"><?php echo translate('congratulations'); ?>!</h3>
                            <p class="mb-0"><?php echo translate('you_passed_exam'); ?></p>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-danger">
                            <h3 class="h5"><?php echo translate('sorry'); ?></h3>
                            <p class="mb-0"><?php echo translate('you_failed_exam'); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo translate('your_score'); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="display-4 me-3"><?php echo $result['percentage']; ?>%</div>
                                        <div class="progress flex-grow-1" style="height: 10px;">
                                            <div class="progress-bar <?php echo $result['passed'] ? 'bg-success' : 'bg-danger'; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $result['percentage']; ?>%" 
                                                 aria-valuenow="<?php echo $result['percentage']; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2 mb-0">
                                        <?php echo translate('passing_score'); ?>: <?php echo $exam['passing_score']; ?>%
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo translate('exam_details'); ?></h5>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong><?php echo translate('time_taken'); ?>:</strong> <?php echo $result['time_taken']; ?> <?php echo translate('minutes'); ?></li>
                                        <li><strong><?php echo translate('questions'); ?>:</strong> <?php echo count($answers); ?></li>
                                        <li><strong><?php echo translate('correct_answers'); ?>:</strong> <?php echo array_reduce($answers, function($carry, $item) { return $carry + ($item['is_correct'] ? 1 : 0); }, 0); ?></li>
                                        <li><strong><?php echo translate('date'); ?>:</strong> <?php echo date('Y-m-d H:i', strtotime($result['completed_at'])); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($course): ?>
                    <div class="mb-4">
                        <h4><?php echo translate('related_course'); ?></h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" alt="<?php echo $course['title']; ?>" class="img-fluid rounded" style="width: 100px;">
                                    </div>
                                    <div>
                                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                        <p class="card-text"><?php echo substr(htmlspecialchars($course['description']), 0, 150); ?>...</p>
                                        <a href="/course/<?php echo $course['id']; ?>" class="btn btn-primary btn-sm"><?php echo translate('view_course'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($recommendedCourses)): ?>
                    <div class="mb-4">
                        <h4><?php echo translate('recommended_courses'); ?></h4>
                        <div class="row">
                            <?php foreach ($recommendedCourses as $recCourse): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($recCourse['title']); ?></h5>
                                        <?php if (!empty($recCourse['description'])): ?>
                                        <p class="card-text"><?php echo substr(htmlspecialchars($recCourse['description']), 0, 100); ?>...</p>
                                        <?php endif; ?>
                                        <a href="/course/<?php echo $recCourse['id']; ?>" class="btn btn-outline-primary btn-sm"><?php echo translate('view_course'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/exam/<?php echo $exam['id']; ?>" class="btn btn-outline-secondary"><?php echo translate('back_to_exam'); ?></a>
                        <a href="/exam/<?php echo $exam['id']; ?>/take" class="btn btn-primary"><?php echo translate('retake_exam'); ?></a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="h5 mb-0"><?php echo translate('question_review'); ?></h3>
                </div>
                <div class="card-body">
                    <?php foreach ($answers as $index => $answer): ?>
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge <?php echo $answer['is_correct'] ? 'bg-success' : 'bg-danger'; ?> me-2">
                                <?php echo $answer['is_correct'] ? translate('correct') : translate('incorrect'); ?>
                            </span>
                            <h4 class="h6 mb-0">
                                <?php echo translate('question'); ?> <?php echo $index + 1; ?>
                            </h4>
                        </div>
                        
                        <p class="mb-3"><?php echo htmlspecialchars($answer['question']); ?></p>
                        
                        <?php if ($answer['type'] === 'single_choice'): ?>
                            <div class="mb-3">
                                <strong><?php echo translate('your_answer'); ?>:</strong>
                                <?php echo isset($answer['options'][$answer['user_answer']]) ? htmlspecialchars($answer['options'][$answer['user_answer']]) : translate('no_answer'); ?>
                                <?php if (!$answer['is_correct']): ?>
                                <div class="text-success mt-1">
                                    <strong><?php echo translate('correct_answer'); ?>:</strong>
                                    <?php echo isset($answer['options'][$answer['correct_answer']]) ? htmlspecialchars($answer['options'][$answer['correct_answer']]) : ''; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($answer['type'] === 'multiple_choice'): ?>
                            <div class="mb-3">
                                <strong><?php echo translate('your_answers'); ?>:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($answer['user_answer'] as $option): ?>
                                    <li><?php echo isset($answer['options'][$option]) ? htmlspecialchars($answer['options'][$option]) : ''; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                
                                <?php if (!$answer['is_correct']): ?>
                                <div class="text-success mt-1">
                                    <strong><?php echo translate('correct_answers'); ?>:</strong>
                                    <ul class="mb-0">
                                        <?php foreach ($answer['correct_answer'] as $option): ?>
                                        <li><?php echo isset($answer['options'][$option]) ? htmlspecialchars($answer['options'][$option]) : ''; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($answer['type'] === 'drag_drop'): ?>
                            <div class="mb-3">
                                <strong><?php echo translate('your_matches'); ?>:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($answer['user_answer'] as $item => $zone): ?>
                                    <li><?php echo htmlspecialchars($item); ?> → <?php echo htmlspecialchars($zone); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                
                                <?php if (!$answer['is_correct']): ?>
                                <div class="text-success mt-1">
                                    <strong><?php echo translate('correct_matches'); ?>:</strong>
                                    <ul class="mb-0">
                                        <?php foreach ($answer['correct_answer'] as $item => $zone): ?>
                                        <li><?php echo htmlspecialchars($item); ?> → <?php echo htmlspecialchars($zone); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($answer['explanation'])): ?>
                        <div class="alert alert-info">
                            <strong><?php echo translate('explanation'); ?>:</strong>
                            <p class="mb-0"><?php echo htmlspecialchars($answer['explanation']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>