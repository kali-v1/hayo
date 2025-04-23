<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">تعديل السؤال</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/questions">الأسئلة</a></li>
                    <li class="breadcrumb-item active">تعديل السؤال</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card">
        <div class="card-header">
            <h5 class="card-title fw-bold">معلومات السؤال</h5>
        </div>
        <div class="card-body">
            <?php
            // Display errors if any
            if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                echo '<div class="alert alert-danger">';
                echo '<ul class="mb-0">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</div>';
                
                // Clear errors
                unset($_SESSION['errors']);
            }
            
            // Get form data if any
            $form_data = $_SESSION['form_data'] ?? $question;
            unset($_SESSION['form_data']);
            ?>
            
            <form action="/admin/questions/update/<?php echo $question['id']; ?>" method="post" id="questionForm" onsubmit="return validateQuestionForm(event)" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exam_id" class="form-label fw-bold">الاختبار <span class="text-danger">*</span></label>
                            <select class="form-select neo-select" id="exam_id" name="exam_id" required>
                                <option value="">اختر الاختبار</option>
                                <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['id']; ?>" <?php echo ($form_data['exam_id'] == $exam['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($exam['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="question_type" class="form-label fw-bold">نوع السؤال <span class="text-danger">*</span></label>
                            <select class="form-select neo-select" id="question_type" name="question_type" required>
                                <option value="single_choice" <?php echo ($form_data['question_type'] === 'single_choice') ? 'selected' : ''; ?>>
                                    <i class="fas fa-dot-circle"></i> اختيار واحد
                                </option>
                                <option value="multiple_choice" <?php echo ($form_data['question_type'] === 'multiple_choice') ? 'selected' : ''; ?>>
                                    <i class="fas fa-check-square"></i> اختيار متعدد
                                </option>
                                <option value="drag_drop" <?php echo ($form_data['question_type'] === 'drag_drop') ? 'selected' : ''; ?>>
                                    <i class="fas fa-arrows-alt"></i> سحب وإفلات
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="points" class="form-label fw-bold">النقاط <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-input" id="points" name="points" min="1" value="<?php echo htmlspecialchars($form_data['points']); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="question_text" class="form-label fw-bold">نص السؤال <span class="text-danger">*</span></label>
                    <textarea class="form-control neo-textarea" id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($form_data['question_text']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="question_image" class="form-label fw-bold">صورة السؤال (اختياري)</label>
                    <?php if (!empty($form_data['image_path'])): ?>
                    <div class="mb-2">
                        <img src="<?php echo htmlspecialchars($form_data['image_path']); ?>" alt="صورة السؤال الحالية" class="img-fluid" style="max-width: 300px; max-height: 200px;">
                        <p class="text-muted mt-1">الصورة الحالية</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="question_image" name="question_image" accept="image/*">
                    <div class="form-text text-muted">يمكنك إضافة صورة توضيحية للسؤال (الصيغ المدعومة: JPG, PNG, GIF)</div>
                </div>
                
                <!-- Single Choice Options -->
                <div id="single_choice_options" class="question-options" <?php echo ($form_data['question_type'] !== 'single_choice') ? 'style="display: none;"' : ''; ?>>
                    <div class="neo-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-dot-circle me-2"></i>خيارات الإجابة</h6>
                        </div>
                        <div class="card-body">
                            <div class="options-container">
                                <?php
                                $single_options = $form_data['options'] ?? [];
                                $correct_answer = $form_data['correct_answer'] ?? [];
                                
                                // Ensure correct_answer is an array
                                if (!is_array($correct_answer)) {
                                    $correct_answer = [$correct_answer];
                                }
                                
                                foreach ($single_options as $index => $option):
                                    $is_correct = in_array($option, $correct_answer);
                                ?>
                                <div class="option-item mb-2">
                                    <div class="input-group flex-row-reverse">
                                        <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="text" class="form-control neo-input" name="options[]" placeholder="الخيار <?php echo intval($index) + 1; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                        <div class="input-group-text neo-input-group-text">
                                            <input type="radio" name="correct_option" value="<?php echo $index; ?>" <?php echo $is_correct ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button type="button" class="neo-btn neo-btn-primary mt-2 add-option">
                                <i class="fas fa-plus"></i> إضافة خيار
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Multiple Choice Options -->
                <div id="multiple_choice_options" class="question-options" <?php echo ($form_data['question_type'] !== 'multiple_choice') ? 'style="display: none;"' : ''; ?>>
                    <div class="neo-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-check-square me-2"></i>خيارات الإجابة (يمكن اختيار أكثر من إجابة)</h6>
                        </div>
                        <div class="card-body">
                            <div class="options-container">
                                <?php
                                $multiple_options = $form_data['options'] ?? [];
                                $correct_answer = $form_data['correct_answer'] ?? [];
                                
                                // Ensure correct_answer is an array
                                if (!is_array($correct_answer)) {
                                    $correct_answer = [$correct_answer];
                                }
                                
                                foreach ($multiple_options as $index => $option):
                                    $is_correct = in_array($option, $correct_answer);
                                ?>
                                <div class="option-item mb-2">
                                    <div class="input-group flex-row-reverse">
                                        <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="text" class="form-control neo-input" name="options[]" placeholder="الخيار <?php echo intval($index) + 1; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                        <div class="input-group-text neo-input-group-text">
                                            <input type="checkbox" name="correct_options[]" value="<?php echo $index; ?>" <?php echo $is_correct ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button type="button" class="neo-btn neo-btn-primary mt-2 add-option">
                                <i class="fas fa-plus"></i> إضافة خيار
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Drag and Drop Options -->
                <div id="drag_drop_options" class="question-options" <?php echo ($form_data['question_type'] !== 'drag_drop') ? 'style="display: none;"' : ''; ?>>
                    <div class="neo-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-arrows-alt me-2"></i>عناصر السحب والإفلات</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold"><i class="fas fa-grip-vertical me-2"></i>عناصر السحب</h6>
                                    <div class="drag-items-container">
                                        <?php
                                        $drag_items = $form_data['options'] ?? [];
                                        
                                        foreach ($drag_items as $index => $item):
                                            $item_number = intval($index) + 1;
                                        ?>
                                        <div class="drag-item mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control neo-input" name="drag_items[]" placeholder="العنصر <?php echo intval($item_number); ?>" value="<?php echo htmlspecialchars($item); ?>">
                                                <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drag-item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <button type="button" class="neo-btn neo-btn-primary mt-2 add-drag-item">
                                        <i class="fas fa-plus"></i> إضافة عنصر
                                    </button>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="fw-bold"><i class="fas fa-bullseye me-2"></i>مناطق الإفلات</h6>
                                    <div class="drop-zones-container">
                                        <?php
                                        $drop_zones = $form_data['correct_answer'] ?? [];
                                        
                                        // Ensure drop_zones is an array
                                        if (!is_array($drop_zones)) {
                                            $drop_zones = [$drop_zones];
                                        }
                                        
                                        foreach ($drop_zones as $index => $zone):
                                            $zone_number = intval($index) + 1;
                                        ?>
                                        <div class="drop-zone mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control neo-input" name="drop_zones[]" placeholder="المنطقة <?php echo intval($zone_number); ?>" value="<?php echo htmlspecialchars($zone); ?>">
                                                <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drop-zone">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <button type="button" class="neo-btn neo-btn-primary mt-2 add-drop-zone">
                                        <i class="fas fa-plus"></i> إضافة منطقة
                                    </button>
                                </div>
                            </div>
                            
                            <div class="neo-alert neo-alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i> يجب أن يكون عدد عناصر السحب مساويًا لعدد مناطق الإفلات.
                            </div>
                            
                            <!-- Preview Section -->
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold"><i class="fas fa-eye me-2"></i>معاينة السؤال</h6>
                                <p class="text-muted">هذه معاينة لكيفية ظهور السؤال للمستخدم.</p>
                                
                                <div class="row mt-3" id="dragDropPreview">
                                    <div class="col-md-6">
                                        <div class="neo-card p-3 mb-3">
                                            <h6 class="fw-bold mb-3">العناصر للسحب</h6>
                                            <div class="drag-items-preview d-flex flex-wrap gap-2">
                                                <!-- Will be populated by JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="neo-card p-3 mb-3">
                                            <h6 class="fw-bold mb-3">مناطق الإفلات</h6>
                                            <div class="drop-zones-preview">
                                                <!-- Will be populated by JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/questions?exam_id=<?php echo $form_data['exam_id']; ?>" class="neo-btn neo-btn-secondary">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                    <button type="submit" class="neo-btn neo-btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define the functions first
        function updateDragItemIndices() {
            const dragItemsContainer = document.querySelector('.drag-items-container');
            if (!dragItemsContainer) return;
            
            const items = dragItemsContainer.querySelectorAll('.drag-item');
            items.forEach((item, index) => {
                const itemNumber = parseInt(index) + 1;
                item.querySelector('input[type="text"]').placeholder = `العنصر ${itemNumber}`;
            });
        }
        
        function updateDropZoneIndices() {
            const dropZonesContainer = document.querySelector('.drop-zones-container');
            if (!dropZonesContainer) return;
            
            const zones = dropZonesContainer.querySelectorAll('.drop-zone');
            zones.forEach((zone, index) => {
                const zoneNumber = parseInt(index) + 1;
                zone.querySelector('input[type="text"]').placeholder = `المنطقة ${zoneNumber}`;
            });
        }
        
        // Initialize indices for drag and drop items
        updateDragItemIndices();
        updateDropZoneIndices();
        // Toggle question options based on question type
        const questionType = document.getElementById('question_type');
        const singleChoiceOptions = document.getElementById('single_choice_options');
        const multipleChoiceOptions = document.getElementById('multiple_choice_options');
        const dragDropOptions = document.getElementById('drag_drop_options');
        
        function toggleQuestionOptions() {
            const type = questionType.value;
            
            singleChoiceOptions.style.display = 'none';
            multipleChoiceOptions.style.display = 'none';
            dragDropOptions.style.display = 'none';
            
            if (type === 'single_choice') {
                singleChoiceOptions.style.display = 'block';
            } else if (type === 'multiple_choice') {
                multipleChoiceOptions.style.display = 'block';
            } else if (type === 'drag_drop') {
                dragDropOptions.style.display = 'block';
            }
        }
        
        // Toggle on change
        questionType.addEventListener('change', toggleQuestionOptions);
        
        // Single Choice Options
        const singleChoiceContainer = singleChoiceOptions.querySelector('.options-container');
        const addSingleOption = singleChoiceOptions.querySelector('.add-option');
        
        addSingleOption.addEventListener('click', function() {
            const optionCount = singleChoiceContainer.querySelectorAll('.option-item').length;
            
            const optionItem = document.createElement('div');
            optionItem.className = 'option-item mb-2';
            optionItem.innerHTML = `
                <div class="input-group flex-row-reverse">
                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                    <input type="text" class="form-control neo-input" name="options[]" placeholder="الخيار ${optionCount + 1}">
                    <div class="input-group-text neo-input-group-text">
                        <input type="radio" name="correct_option" value="${optionCount}">
                    </div>
                </div>
            `;
            
            singleChoiceContainer.appendChild(optionItem);
            
            // Add event listener to remove button
            optionItem.querySelector('.remove-option').addEventListener('click', function() {
                if (singleChoiceContainer.querySelectorAll('.option-item').length > 2) {
                    optionItem.remove();
                    updateSingleChoiceIndices();
                } else {
                    alert('يجب أن يكون هناك خياران على الأقل');
                }
            });
        });
        
        // Add event listeners to existing remove buttons
        singleChoiceContainer.querySelectorAll('.remove-option').forEach(button => {
            button.addEventListener('click', function() {
                if (singleChoiceContainer.querySelectorAll('.option-item').length > 2) {
                    this.closest('.option-item').remove();
                    updateSingleChoiceIndices();
                } else {
                    alert('يجب أن يكون هناك خياران على الأقل');
                }
            });
        });
        
        function updateSingleChoiceIndices() {
            const options = singleChoiceContainer.querySelectorAll('.option-item');
            options.forEach((option, index) => {
                option.querySelector('input[type="radio"]').value = index;
                option.querySelector('input[type="text"]').placeholder = `الخيار ${index + 1}`;
            });
        }
        
        // Multiple Choice Options
        const multipleChoiceContainer = multipleChoiceOptions.querySelector('.options-container');
        const addMultipleOption = multipleChoiceOptions.querySelector('.add-option');
        
        addMultipleOption.addEventListener('click', function() {
            const optionCount = multipleChoiceContainer.querySelectorAll('.option-item').length;
            
            const optionItem = document.createElement('div');
            optionItem.className = 'option-item mb-2';
            optionItem.innerHTML = `
                <div class="input-group flex-row-reverse">
                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                    <input type="text" class="form-control neo-input" name="options[]" placeholder="الخيار ${optionCount + 1}">
                    <div class="input-group-text neo-input-group-text">
                        <input type="checkbox" name="correct_options[]" value="${optionCount}">
                    </div>
                </div>
            `;
            
            multipleChoiceContainer.appendChild(optionItem);
            
            // Add event listener to remove button
            optionItem.querySelector('.remove-option').addEventListener('click', function() {
                if (multipleChoiceContainer.querySelectorAll('.option-item').length > 2) {
                    optionItem.remove();
                    updateMultipleChoiceIndices();
                } else {
                    alert('يجب أن يكون هناك خياران على الأقل');
                }
            });
        });
        
        // Add event listeners to existing remove buttons
        multipleChoiceContainer.querySelectorAll('.remove-option').forEach(button => {
            button.addEventListener('click', function() {
                if (multipleChoiceContainer.querySelectorAll('.option-item').length > 2) {
                    this.closest('.option-item').remove();
                    updateMultipleChoiceIndices();
                } else {
                    alert('يجب أن يكون هناك خياران على الأقل');
                }
            });
        });
        
        function updateMultipleChoiceIndices() {
            const options = multipleChoiceContainer.querySelectorAll('.option-item');
            options.forEach((option, index) => {
                option.querySelector('input[type="checkbox"]').value = index;
                option.querySelector('input[type="text"]').placeholder = `الخيار ${index + 1}`;
            });
        }
        
        // Drag and Drop Options
        const dragItemsContainer = dragDropOptions.querySelector('.drag-items-container');
        const dropZonesContainer = dragDropOptions.querySelector('.drop-zones-container');
        const addDragItem = dragDropOptions.querySelector('.add-drag-item');
        const addDropZone = dragDropOptions.querySelector('.add-drop-zone');
        
        addDragItem.addEventListener('click', function() {
            const itemCount = dragItemsContainer.querySelectorAll('.drag-item').length;
            
            const dragItem = document.createElement('div');
            dragItem.className = 'drag-item mb-2';
            dragItem.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control neo-input" name="drag_items[]" placeholder="العنصر ${parseInt(itemCount) + 1}">
                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drag-item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            dragItemsContainer.appendChild(dragItem);
            
            // Add event listener to remove button
            dragItem.querySelector('.remove-drag-item').addEventListener('click', function() {
                if (dragItemsContainer.querySelectorAll('.drag-item').length > 2) {
                    dragItem.remove();
                    updateDragItemIndices();
                } else {
                    alert('يجب أن يكون هناك عنصران على الأقل');
                }
            });
        });
        
        addDropZone.addEventListener('click', function() {
            const zoneCount = dropZonesContainer.querySelectorAll('.drop-zone').length;
            
            const dropZone = document.createElement('div');
            dropZone.className = 'drop-zone mb-2';
            dropZone.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control neo-input" name="drop_zones[]" placeholder="المنطقة ${parseInt(zoneCount) + 1}">
                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drop-zone">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            dropZonesContainer.appendChild(dropZone);
            
            // Add event listener to remove button
            dropZone.querySelector('.remove-drop-zone').addEventListener('click', function() {
                if (dropZonesContainer.querySelectorAll('.drop-zone').length > 2) {
                    dropZone.remove();
                    updateDropZoneIndices();
                } else {
                    alert('يجب أن يكون هناك منطقتان على الأقل');
                }
            });
        });
        
        // Add event listeners to existing remove buttons
        dragItemsContainer.querySelectorAll('.remove-drag-item').forEach(button => {
            button.addEventListener('click', function() {
                if (dragItemsContainer.querySelectorAll('.drag-item').length > 2) {
                    this.closest('.drag-item').remove();
                    updateDragItemIndices();
                } else {
                    alert('يجب أن يكون هناك عنصران على الأقل');
                }
            });
        });
        
        dropZonesContainer.querySelectorAll('.remove-drop-zone').forEach(button => {
            button.addEventListener('click', function() {
                if (dropZonesContainer.querySelectorAll('.drop-zone').length > 2) {
                    this.closest('.drop-zone').remove();
                    updateDropZoneIndices();
                } else {
                    alert('يجب أن يكون هناك منطقتان على الأقل');
                }
            });
        });
        
        // Functions are now defined at the top of the script
        
        // Initialize indices on page load
        updateDragItemIndices();
        updateDropZoneIndices();
        
        // Call the update functions again after a short delay to ensure all elements are properly initialized
        setTimeout(() => {
            updateDragItemIndices();
            updateDropZoneIndices();
            updateDragDropPreview();
        }, 100);
        
        // Define the form validation function that will be called on form submit
        function validateQuestionForm(event) {
            console.log("Form validation function called");
            
            const type = questionType.value;
            
            // Remove required attribute from all option inputs to prevent validation issues
            document.querySelectorAll('input[name="options[]"]').forEach(input => {
                input.removeAttribute('required');
            });
            
            // Disable all inputs that are not relevant to the current question type
            // This prevents HTML5 validation errors for hidden fields
            if (type === 'single_choice') {
                // Disable drag and drop inputs
                document.querySelectorAll('input[name="drag_items[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                document.querySelectorAll('input[name="drop_zones[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                
                // Disable multiple choice inputs
                document.querySelectorAll('input[name="correct_options[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                
                // Validate single choice
                const options = singleChoiceContainer.querySelectorAll('input[name="options[]"]');
                const correctOption = singleChoiceContainer.querySelector('input[name="correct_option"]:checked');
                
                let validOptions = 0;
                options.forEach(option => {
                    if (option.value.trim() !== '') {
                        validOptions++;
                    }
                });
                
                if (validOptions < 2) {
                    alert('يجب إضافة خيارين على الأقل');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
                
                if (!correctOption) {
                    alert('يجب تحديد الإجابة الصحيحة');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
            } else if (type === 'multiple_choice') {
                // Disable drag and drop inputs
                document.querySelectorAll('input[name="drag_items[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                document.querySelectorAll('input[name="drop_zones[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                
                // Disable single choice inputs
                document.querySelectorAll('input[name="correct_option"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                
                // Validate multiple choice
                const options = multipleChoiceContainer.querySelectorAll('input[name="options[]"]');
                const correctOptions = multipleChoiceContainer.querySelectorAll('input[name="correct_options[]"]:checked');
                
                let validOptions = 0;
                options.forEach(option => {
                    if (option.value.trim() !== '') {
                        validOptions++;
                    }
                });
                
                if (validOptions < 2) {
                    alert('يجب إضافة خيارين على الأقل');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
                
                if (correctOptions.length === 0) {
                    alert('يجب تحديد إجابة صحيحة واحدة على الأقل');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
            } else if (type === 'drag_drop') {
                // Disable single choice inputs
                document.querySelectorAll('input[name="options[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                document.querySelectorAll('input[name="correct_option"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                
                // Disable multiple choice inputs
                document.querySelectorAll('input[name="correct_options[]"]').forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                
                // Validate drag and drop
                const dragItems = dragItemsContainer.querySelectorAll('input[name="drag_items[]"]');
                const dropZones = dropZonesContainer.querySelectorAll('input[name="drop_zones[]"]');
                
                let validDragItems = 0;
                dragItems.forEach(item => {
                    if (item.value.trim() !== '') {
                        validDragItems++;
                    }
                });
                
                let validDropZones = 0;
                dropZones.forEach(zone => {
                    if (zone.value.trim() !== '') {
                        validDropZones++;
                    }
                });
                
                if (validDragItems < 2) {
                    alert('يجب إضافة عنصرين على الأقل');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
                
                if (validDropZones < 2) {
                    alert('يجب إضافة منطقتين على الأقل');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
                
                if (validDragItems !== validDropZones) {
                    alert('عدد عناصر السحب يجب أن يساوي عدد مناطق الإفلات');
                    event.preventDefault();
                    
                    // Re-enable all inputs before returning
                    document.querySelectorAll('input:disabled').forEach(input => {
                        input.disabled = false;
                    });
                    
                    return false;
                }
            }
            
            console.log("Form validation passed, submitting form");
            return true;
        }
        
        // Drag and Drop Preview Functionality
        function updateDragDropPreview() {
            const dragItems = Array.from(dragItemsContainer.querySelectorAll('input[name="drag_items[]"]'))
                .map(input => input.value)
                .filter(value => value.trim() !== '');
                
            const dropZones = Array.from(dropZonesContainer.querySelectorAll('input[name="drop_zones[]"]'))
                .map(input => input.value)
                .filter(value => value.trim() !== '');
            
            // Update drag items preview
            const dragItemsPreview = document.querySelector('.drag-items-preview');
            dragItemsPreview.innerHTML = '';
            
            dragItems.forEach((item, index) => {
                if (item.trim() === '') return;
                
                const itemElement = document.createElement('div');
                itemElement.className = 'neo-badge neo-badge-primary p-2 m-1 draggable-preview';
                itemElement.setAttribute('data-index', index);
                itemElement.innerHTML = `
                    <i class="fas fa-grip-lines me-2"></i>${item}
                `;
                dragItemsPreview.appendChild(itemElement);
            });
            
            // Update drop zones preview
            const dropZonesPreview = document.querySelector('.drop-zones-preview');
            dropZonesPreview.innerHTML = '';
            
            dropZones.forEach((zone, index) => {
                if (zone.trim() === '') return;
                
                const zoneElement = document.createElement('div');
                zoneElement.className = 'neo-card p-3 mb-2 droppable-preview';
                zoneElement.setAttribute('data-index', index);
                zoneElement.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bullseye me-2"></i>
                        <span>${zone}</span>
                        <div class="ms-auto drop-placeholder">
                            <span class="text-muted">منطقة إفلات</span>
                        </div>
                    </div>
                `;
                dropZonesPreview.appendChild(zoneElement);
            });
        }
        
        // Update preview when drag items or drop zones change
        dragItemsContainer.addEventListener('input', updateDragDropPreview);
        dropZonesContainer.addEventListener('input', updateDragDropPreview);
        
        // Update preview when items are added or removed
        addDragItem.addEventListener('click', () => setTimeout(updateDragDropPreview, 50));
        addDropZone.addEventListener('click', () => setTimeout(updateDragDropPreview, 50));
        
        // Initial preview update
        if (questionType.value === 'drag_drop') {
            setTimeout(updateDragDropPreview, 100);
        }
        
        // Update preview when switching to drag_drop type
        questionType.addEventListener('change', function() {
            if (this.value === 'drag_drop') {
                setTimeout(updateDragDropPreview, 100);
            }
        });
    });
</script>

<style>
/* Neo Brutalism styles for drag and drop preview */
.draggable-preview {
    cursor: move;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 3px 3px 0 rgba(0,0,0,0.2);
}

.draggable-preview:hover {
    transform: translate(-2px, -2px);
    box-shadow: 5px 5px 0 rgba(0,0,0,0.3);
}

.droppable-preview {
    border: 2px dashed #ccc;
    transition: all 0.2s;
}

.droppable-preview:hover {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.drop-placeholder {
    min-width: 120px;
    padding: 0.5rem;
    border: 1px dashed #ccc;
    border-radius: 0.25rem;
    text-align: center;
}

/* Neo Brutalism form styles */
.neo-input, .neo-select, .neo-textarea {
    border: 2px solid #000;
    border-radius: 0;
    box-shadow: 3px 3px 0 rgba(0,0,0,0.2);
    transition: transform 0.1s, box-shadow 0.1s;
}

.neo-input:focus, .neo-select:focus, .neo-textarea:focus {
    transform: translate(-2px, -2px);
    box-shadow: 5px 5px 0 rgba(0,0,0,0.3);
    border-color: var(--bs-primary);
}

.neo-input-group-text {
    border: 2px solid #000;
    border-radius: 0;
    background-color: #f8f9fa;
}
</style>