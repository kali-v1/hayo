<?php
/**
 * Question View Template
 * 
 * This template displays the details of a question.
 */

// Helper function to ensure correct_answer is an array
function ensureArray($value) {
    if (!is_array($value)) {
        return [$value];
    }
    return $value;
}

// Helper function to check if an option is correct
function isCorrectOption($option, $correctAnswer, $questionType) {
    if (!isset($correctAnswer)) {
        return false;
    }
    
    if ($questionType === 'single_choice') {
        if (is_array($correctAnswer)) {
            return in_array($option, $correctAnswer);
        } else {
            return $option === $correctAnswer;
        }
    } else if ($questionType === 'multiple_choice') {
        $correctAnswers = is_array($correctAnswer) ? $correctAnswer : [$correctAnswer];
        return in_array($option, $correctAnswers);
    } else if ($questionType === 'drag_drop') {
        // For drag_drop, the index in options array should match the index in correct_answer array
        if (is_array($correctAnswer) && isset($question['options']) && is_array($question['options'])) {
            $index = array_search($option, $question['options']);
            return $index !== false && isset($correctAnswer[$index]);
        }
    }
    
    return false;
}

// Ensure correct_answer is an array
if (isset($question['correct_answer'])) {
    $question['correct_answer'] = ensureArray($question['correct_answer']);
}
?>

<div class="container mt-4">
    <div class="card neo-brutalism-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo htmlspecialchars($pageTitle); ?></h5>
            <div>
                <a href="/admin/questions/edit/<?php echo $question['id']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="/admin/questions?exam_id=<?php echo $question['exam_id']; ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">الاختبار</th>
                            <td>
                                <a href="/admin/exams/view/<?php echo $question['exam_id']; ?>">
                                    <?php echo htmlspecialchars($question['exam_title'] ?? ''); ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>الدورة</th>
                            <td><?php echo htmlspecialchars($question['course_title'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th>نص السؤال</th>
                            <td><?php echo nl2br(htmlspecialchars($question['question_text'] ?? '')); ?></td>
                        </tr>
                        <tr>
                            <th>نوع السؤال</th>
                            <td>
                                <?php if (isset($question['question_type']) && $question['question_type'] === 'single_choice'): ?>
                                    <span class="badge bg-primary">اختيار واحد</span>
                                <?php elseif (isset($question['question_type']) && $question['question_type'] === 'multiple_choice'): ?>
                                    <span class="badge bg-info">اختيارات متعددة</span>
                                <?php elseif (isset($question['question_type']) && $question['question_type'] === 'drag_drop'): ?>
                                    <span class="badge bg-warning">سحب وإفلات</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>النقاط</th>
                            <td><?php echo htmlspecialchars($question['points'] ?? 0); ?></td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td><?php echo htmlspecialchars($question['created_at'] ?? ''); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card neo-brutalism-card">
                        <div class="card-header">
                            <h6 class="mb-0">الخيارات والإجابات الصحيحة</h6>
                        </div>
                        <div class="card-body">
                            <?php if (isset($question['question_type']) && $question['question_type'] === 'single_choice'): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>الخيار</th>
                                            <th style="width: 100px;">الإجابة الصحيحة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($question['options']) && is_array($question['options'])): ?>
                                            <?php foreach ($question['options'] as $index => $option): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($option); ?></td>
                                                    <td class="text-center">
                                                        <?php if (isset($question['correct_answer']) && is_array($question['correct_answer']) && in_array($option, $question['correct_answer'])): ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php elseif (isset($question['question_type']) && $question['question_type'] === 'multiple_choice'): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>الخيار</th>
                                            <th style="width: 100px;">الإجابة الصحيحة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($question['options']) && is_array($question['options'])): ?>
                                            <?php foreach ($question['options'] as $index => $option): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($option); ?></td>
                                                    <td class="text-center">
                                                        <?php if (isset($question['correct_answer']) && is_array($question['correct_answer']) && in_array($option, $question['correct_answer'])): ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php elseif (isset($question['question_type']) && $question['question_type'] === 'drag_drop'): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>عنصر السحب</th>
                                            <th>منطقة الإفلات (الإجابة الصحيحة)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($question['options']) && is_array($question['options'])): ?>
                                            <?php foreach ($question['options'] as $index => $dragItem): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($dragItem); ?></td>
                                                    <td>
                                                        <?php echo isset($question['correct_answer']) && is_array($question['correct_answer']) && isset($question['correct_answer'][$index]) ? htmlspecialchars($question['correct_answer'][$index]) : ''; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>