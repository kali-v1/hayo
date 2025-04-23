<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="fw-bold"><?php echo translate('my_exams'); ?></h1>
        <p class="lead"><?php echo translate('my_exams_subtitle'); ?></p>
    </div>
</div>

<?php if (empty($examAttempts)): ?>
<div class="brutalism-card bg-white p-5 text-center shadow">
    <h3 class="fw-bold"><?php echo translate('no_exam_attempts'); ?></h3>
    <p><?php echo translate('no_exam_attempts_message'); ?></p>
    <a href="/exams" class="btn btn-primary mt-3"><?php echo translate('browse_exams'); ?></a>
</div>
<?php else: ?>
<div class="brutalism-card bg-white p-4 shadow mb-4">
    <h3 class="fw-bold mb-3"><?php echo translate('exam_history'); ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo translate('exam'); ?></th>
                    <th><?php echo translate('date'); ?></th>
                    <th><?php echo translate('score'); ?></th>
                    <th><?php echo translate('result'); ?></th>
                    <th><?php echo translate('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($examAttempts as $attempt): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="/assets/images/exams/<?php echo $attempt['exam_image'] ?? 'default-exam.jpg'; ?>" alt="<?php echo $attempt['exam_title']; ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <strong><?php echo $attempt['exam_title']; ?></strong>
                                <?php if (isset($attempt['course_title'])): ?>
                                <div class="small text-muted"><?php echo $attempt['course_title']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?php echo isset($attempt['attempt_date']) ? formatDate($attempt['attempt_date']) : formatDate($attempt['started_at'] ?? date('Y-m-d H:i:s')); ?></td>
                    <td><?php echo $attempt['score']; ?>%</td>
                    <td>
                        <?php 
                        // Determine if passed based on score and passing_score
                        $isPassed = isset($attempt['passed']) ? $attempt['passed'] : 
                                   (isset($attempt['score']) && isset($attempt['passing_score']) ? 
                                   $attempt['score'] >= $attempt['passing_score'] : 
                                   ($attempt['score'] >= 70)); // Default passing score is 70%
                        ?>
                        <?php if ($isPassed): ?>
                        <span class="badge bg-success"><?php echo translate('passed'); ?></span>
                        <?php else: ?>
                        <span class="badge bg-danger"><?php echo translate('failed'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/exam-result/<?php echo $attempt['id'] ?? $attempt['attempt_id'] ?? 0; ?>" class="btn btn-sm btn-primary"><?php echo translate('view_results'); ?></a>
                        <?php if (!$isPassed): ?>
                        <a href="/exam/<?php echo $attempt['exam_id']; ?>" class="btn btn-sm btn-outline-primary"><?php echo translate('retake_exam'); ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="brutalism-card bg-white p-4 shadow">
    <h3 class="fw-bold mb-3"><?php echo translate('exam_statistics'); ?></h3>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="brutalism-card bg-primary text-white p-3 text-center">
                <h4><?php echo count($examAttempts); ?></h4>
                <p class="mb-0"><?php echo translate('total_attempts'); ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="brutalism-card bg-success text-white p-3 text-center">
                <?php
                $passedCount = 0;
                foreach ($examAttempts as $attempt) {
                    // Determine if passed based on score and passing_score
                    $isPassed = isset($attempt['passed']) ? $attempt['passed'] : 
                               (isset($attempt['score']) && isset($attempt['passing_score']) ? 
                               $attempt['score'] >= $attempt['passing_score'] : 
                               ($attempt['score'] >= 70)); // Default passing score is 70%
                    if ($isPassed) {
                        $passedCount++;
                    }
                }
                ?>
                <h4><?php echo $passedCount; ?></h4>
                <p class="mb-0"><?php echo translate('exams_passed'); ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="brutalism-card bg-danger text-white p-3 text-center">
                <h4><?php echo count($examAttempts) - $passedCount; ?></h4>
                <p class="mb-0"><?php echo translate('exams_failed'); ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="brutalism-card bg-info text-white p-3 text-center">
                <?php 
                $totalScore = 0;
                foreach ($examAttempts as $attempt) {
                    $totalScore += $attempt['score'];
                }
                $averageScore = count($examAttempts) > 0 ? round($totalScore / count($examAttempts)) : 0;
                ?>
                <h4><?php echo $averageScore; ?>%</h4>
                <p class="mb-0"><?php echo translate('average_score'); ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>