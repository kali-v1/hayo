<div class="row">
    <div class="col-lg-8">
        <div class="brutalism-card bg-white p-4 shadow mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold mb-0"><?php echo $exam['title']; ?></h1>
                <div class="exam-timer bg-primary text-white p-2 rounded">
                    <i class="fas fa-clock me-2"></i>
                    <span id="timer" data-duration="<?php echo $exam['duration'] * 60; ?>">
                        <?php echo $exam['duration']; ?>:00
                    </span>
                </div>
            </div>
            
            <div class="progress mb-4">
                <div id="progress-bar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            
            <form id="exam-form" action="/exam/<?php echo $exam['id']; ?>/submit" method="post">
                <input type="hidden" name="started_at" value="<?php echo date('Y-m-d H:i:s'); ?>">
                
                <?php foreach ($questions as $index => $question): ?>
                <div class="question-container" id="question-<?php echo $index + 1; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                    <div class="question-header d-flex justify-content-between mb-3">
                        <h4 class="fw-bold">
                            <?php echo translate('question'); ?> <?php echo $index + 1; ?> <?php echo translate('of'); ?> <?php echo count($questions); ?>
                        </h4>
                        <div>
                            <span class="badge bg-info"><?php echo $question['points']; ?> <?php echo translate('points'); ?></span>
                            <?php 
                            $questionType = '';
                            switch ($question['question_type']) {
                                case 'single_choice':
                                    $questionType = translate('single_choice');
                                    break;
                                case 'multiple_choice':
                                    $questionType = translate('multiple_choice');
                                    break;
                                case 'drag_drop':
                                    $questionType = translate('drag_drop');
                                    break;
                            }
                            ?>
                            <span class="badge bg-secondary"><?php echo $questionType; ?></span>
                        </div>
                    </div>
                    
                    <div class="question-text mb-4">
                        <p class="lead"><?php echo $question['question_text']; ?></p>
                    </div>
                    
                    <div class="question-options mb-4">
                        <?php 
                        $options = json_decode($question['options'], true);
                        
                        switch ($question['question_type']) {
                            case 'single_choice':
                                foreach ($options as $optionIndex => $option): 
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" id="option-<?php echo $question['id']; ?>-<?php echo $optionIndex; ?>" value="<?php echo $option; ?>">
                            <label class="form-check-label" for="option-<?php echo $question['id']; ?>-<?php echo $optionIndex; ?>">
                                <?php echo $option; ?>
                            </label>
                        </div>
                        <?php 
                                endforeach;
                                break;
                                
                            case 'multiple_choice':
                                foreach ($options as $optionIndex => $option): 
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="answers[<?php echo $question['id']; ?>][]" id="option-<?php echo $question['id']; ?>-<?php echo $optionIndex; ?>" value="<?php echo $option; ?>">
                            <label class="form-check-label" for="option-<?php echo $question['id']; ?>-<?php echo $optionIndex; ?>">
                                <?php echo $option; ?>
                            </label>
                        </div>
                        <?php 
                                endforeach;
                                break;
                                
                            case 'drag_drop':
                                if (is_array($options) && !isset($options[0])): // Associative array (key-value pairs)
                        ?>
                        <div class="drag-drop-container">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="fw-bold"><?php echo translate('items'); ?></h5>
                                    <div class="drag-items">
                                        <?php foreach (array_keys($options) as $key): ?>
                                        <div class="drag-item brutalism-card bg-light p-2 mb-2" data-key="<?php echo $key; ?>">
                                            <?php echo $key; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="fw-bold"><?php echo translate('drop_zones'); ?></h5>
                                    <div class="drop-zones">
                                        <?php foreach (array_keys($options) as $key): ?>
                                        <div class="drop-zone brutalism-card bg-white p-2 mb-2" data-key="<?php echo $key; ?>">
                                            <span class="drop-placeholder"><?php echo translate('drop_here'); ?></span>
                                            <input type="hidden" name="answers[<?php echo $question['id']; ?>][<?php echo $key; ?>]" value="">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                                else: // Simple array (ordered list)
                        ?>
                        <div class="drag-drop-container">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="fw-bold"><?php echo translate('items'); ?></h5>
                                    <div class="drag-items">
                                        <?php 
                                        $shuffledOptions = $options;
                                        shuffle($shuffledOptions);
                                        foreach ($shuffledOptions as $optionIndex => $option): 
                                        ?>
                                        <div class="drag-item brutalism-card bg-light p-2 mb-2" data-index="<?php echo $optionIndex; ?>">
                                            <?php echo $option; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="fw-bold"><?php echo translate('drop_zones'); ?></h5>
                                    <div class="drop-zones">
                                        <?php for ($i = 0; $i < count($options); $i++): ?>
                                        <div class="drop-zone brutalism-card bg-white p-2 mb-2" data-position="<?php echo $i; ?>">
                                            <span class="drop-placeholder"><?php echo translate('drop_here'); ?></span>
                                            <input type="hidden" name="answers[<?php echo $question['id']; ?>][]" value="">
                                        </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                                endif;
                                break;
                        }
                        ?>
                    </div>
                    
                    <div class="question-navigation d-flex justify-content-between">
                        <?php if ($index > 0): ?>
                        <button type="button" class="btn btn-outline-primary prev-question" data-current="<?php echo $index + 1; ?>" data-prev="<?php echo $index; ?>">
                            <i class="fas fa-arrow-left me-2"></i> <?php echo translate('previous'); ?>
                        </button>
                        <?php else: ?>
                        <div></div>
                        <?php endif; ?>
                        
                        <?php if ($index < count($questions) - 1): ?>
                        <button type="button" class="btn btn-primary next-question" data-current="<?php echo $index + 1; ?>" data-next="<?php echo $index + 2; ?>">
                            <?php echo translate('next'); ?> <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-success" id="finish-exam">
                            <?php echo translate('finish_exam'); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Confirmation Modal -->
                <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmSubmitModalLabel"><?php echo translate('confirm_submission'); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><?php echo translate('confirm_submit_message'); ?></p>
                                <div id="unanswered-questions-alert" class="alert alert-warning d-none">
                                    <p class="mb-0"><?php echo translate('unanswered_questions_warning'); ?></p>
                                    <ul id="unanswered-questions-list"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('continue_exam'); ?></button>
                                <button type="button" id="confirm-submit-btn" class="btn btn-primary"><?php echo translate('submit_exam'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="brutalism-card bg-white p-4 shadow sticky-top" style="top: 20px;">
            <h4 class="fw-bold mb-3"><?php echo translate('question_navigator'); ?></h4>
            
            <div class="question-navigator mb-4">
                <div class="row g-2">
                    <?php foreach ($questions as $index => $question): ?>
                    <div class="col-2">
                        <button type="button" class="btn btn-outline-primary w-100 question-nav-btn" data-question="<?php echo $index + 1; ?>">
                            <?php echo $index + 1; ?>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="exam-info">
                <h4 class="fw-bold mb-3"><?php echo translate('exam_information'); ?></h4>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-question-circle me-2"></i> <?php echo translate('total_questions'); ?>: <?php echo count($questions); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle me-2"></i> <?php echo translate('passing_score'); ?>: <?php echo $exam['passing_score']; ?>%
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock me-2"></i> <?php echo translate('time_limit'); ?>: <?php echo $exam['duration']; ?> <?php echo translate('minutes'); ?>
                    </li>
                </ul>
            </div>
            
            <div class="exam-legend mt-4 pt-4 border-top">
                <h4 class="fw-bold mb-3"><?php echo translate('legend'); ?></h4>
                <div class="d-flex flex-wrap">
                    <div class="me-3 mb-2">
                        <span class="btn btn-outline-primary btn-sm"><?php echo translate('not_answered'); ?></span>
                    </div>
                    <div class="me-3 mb-2">
                        <span class="btn btn-primary btn-sm"><?php echo translate('answered'); ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="btn btn-warning btn-sm"><?php echo translate('current_question'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="button" class="btn btn-success" id="submit-exam-btn">
                    <?php echo translate('submit_exam'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Timer functionality
    const timerElement = document.getElementById('timer');
    let duration = parseInt(timerElement.dataset.duration);
    let timer = duration;
    
    const timerInterval = setInterval(function() {
        const minutes = Math.floor(timer / 60);
        const seconds = timer % 60;
        
        timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        if (--timer < 0) {
            clearInterval(timerInterval);
            document.getElementById('exam-form').submit();
        }
    }, 1000);
    
    // Question navigation
    const questionNavBtns = document.querySelectorAll('.question-nav-btn');
    const nextButtons = document.querySelectorAll('.next-question');
    const prevButtons = document.querySelectorAll('.prev-question');
    const finishButton = document.getElementById('finish-exam');
    const submitButton = document.getElementById('submit-exam-btn');
    
    // Mark current question
    function updateQuestionNav(currentQuestion) {
        questionNavBtns.forEach(btn => {
            btn.classList.remove('btn-warning');
            if (parseInt(btn.dataset.question) === currentQuestion) {
                btn.classList.add('btn-warning');
            }
        });
        
        // Update progress bar
        const progressBar = document.getElementById('progress-bar');
        const progress = (currentQuestion / <?php echo count($questions); ?>) * 100;
        progressBar.style.width = progress + '%';
        progressBar.textContent = Math.round(progress) + '%';
        progressBar.setAttribute('aria-valuenow', progress);
    }
    
    // Initialize with first question
    updateQuestionNav(1);
    
    // Question navigation buttons
    questionNavBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const questionNumber = parseInt(this.dataset.question);
            showQuestion(questionNumber);
            updateQuestionNav(questionNumber);
        });
    });
    
    // Next question buttons
    nextButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const currentQuestion = parseInt(this.dataset.current);
            const nextQuestion = parseInt(this.dataset.next);
            
            // Mark current question as answered if it has a value
            const questionContainer = document.getElementById('question-' + currentQuestion);
            const inputs = questionContainer.querySelectorAll('input:checked, input[type="hidden"][value!=""]');
            
            if (inputs.length > 0) {
                questionNavBtns[currentQuestion - 1].classList.remove('btn-outline-primary');
                questionNavBtns[currentQuestion - 1].classList.add('btn-primary');
            }
            
            showQuestion(nextQuestion);
            updateQuestionNav(nextQuestion);
        });
    });
    
    // Previous question buttons
    prevButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const currentQuestion = parseInt(this.dataset.current);
            const prevQuestion = parseInt(this.dataset.prev);
            
            showQuestion(prevQuestion);
            updateQuestionNav(prevQuestion);
        });
    });
    
    // Show specific question
    function showQuestion(questionNumber) {
        const questionContainers = document.querySelectorAll('.question-container');
        questionContainers.forEach(container => {
            container.style.display = 'none';
        });
        
        document.getElementById('question-' + questionNumber).style.display = 'block';
    }
    
    // Finish exam button
    if (finishButton) {
        finishButton.addEventListener('click', function(e) {
            e.preventDefault();
            showSubmitConfirmation();
        });
    }
    
    // Submit exam button
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            showSubmitConfirmation();
        });
    }
    
    // Show submit confirmation
    function showSubmitConfirmation() {
        // Check for unanswered questions
        const unansweredQuestions = [];
        const questionContainers = document.querySelectorAll('.question-container');
        
        questionContainers.forEach((container, index) => {
            const questionNumber = index + 1;
            const inputs = container.querySelectorAll('input:checked, input[type="hidden"][value!=""]');
            
            if (inputs.length === 0) {
                unansweredQuestions.push(questionNumber);
            }
        });
        
        // Show warning for unanswered questions
        const unansweredAlert = document.getElementById('unanswered-questions-alert');
        const unansweredList = document.getElementById('unanswered-questions-list');
        
        if (unansweredQuestions.length > 0) {
            unansweredAlert.classList.remove('d-none');
            unansweredList.innerHTML = '';
            
            unansweredQuestions.forEach(questionNumber => {
                const listItem = document.createElement('li');
                listItem.textContent = '<?php echo translate('question'); ?> ' + questionNumber;
                unansweredList.appendChild(listItem);
            });
        } else {
            unansweredAlert.classList.add('d-none');
        }
        
        // Show confirmation modal
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
        confirmModal.show();
    }
    
    // Add event listener to the confirm submit button
    document.getElementById('confirm-submit-btn').addEventListener('click', function() {
        document.getElementById('exam-form').submit();
    });
    
    // Drag and drop functionality
    const dragItems = document.querySelectorAll('.drag-item');
    const dropZones = document.querySelectorAll('.drop-zone');
    
    dragItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.innerHTML);
            e.dataTransfer.setData('key', this.dataset.key || '');
            e.dataTransfer.setData('index', this.dataset.index || '');
            this.classList.add('dragging');
        });
        
        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
        });
        
        // Make items draggable
        item.setAttribute('draggable', 'true');
    });
    
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        zone.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const data = e.dataTransfer.getData('text/plain');
            const key = e.dataTransfer.getData('key');
            const index = e.dataTransfer.getData('index');
            
            // Clear existing content
            const placeholder = this.querySelector('.drop-placeholder');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Remove any previous dropped item
            const previousItem = this.querySelector('.dropped-item');
            if (previousItem) {
                previousItem.remove();
            }
            
            // Create new dropped item
            const droppedItem = document.createElement('div');
            droppedItem.className = 'dropped-item';
            droppedItem.innerHTML = data;
            this.appendChild(droppedItem);
            
            // Update hidden input value
            const input = this.querySelector('input[type="hidden"]');
            if (input) {
                input.value = data;
                
                // Mark question as answered
                const questionContainer = this.closest('.question-container');
                const questionNumber = parseInt(questionContainer.id.replace('question-', ''));
                questionNavBtns[questionNumber - 1].classList.remove('btn-outline-primary');
                questionNavBtns[questionNumber - 1].classList.add('btn-primary');
            }
            
            // Add remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-1';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                droppedItem.remove();
                removeBtn.remove();
                
                // Reset input value
                if (input) {
                    input.value = '';
                }
                
                // Add placeholder back
                const newPlaceholder = document.createElement('span');
                newPlaceholder.className = 'drop-placeholder';
                newPlaceholder.textContent = '<?php echo translate('drop_here'); ?>';
                zone.appendChild(newPlaceholder);
            });
            
            this.appendChild(removeBtn);
        });
    });
    
    // Form inputs change event to mark questions as answered
    document.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(input => {
        input.addEventListener('change', function() {
            const questionContainer = this.closest('.question-container');
            const questionNumber = parseInt(questionContainer.id.replace('question-', ''));
            
            questionNavBtns[questionNumber - 1].classList.remove('btn-outline-primary');
            questionNavBtns[questionNumber - 1].classList.add('btn-primary');
        });
    });
});
</script>