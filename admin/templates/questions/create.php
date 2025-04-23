<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إضافة سؤال جديد</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/questions">الأسئلة</a></li>
                    <li class="breadcrumb-item active">إضافة سؤال</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">معلومات السؤال</h5>
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
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);
            
            // Get exam_id from query string or form data
            $exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : (isset($form_data['exam_id']) ? (int)$form_data['exam_id'] : 0);
            ?>
            
            <form action="/admin/questions/store" method="post" id="questionForm" onsubmit="return validateQuestionForm(event)" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exam_id" class="form-label">الاختبار <span class="text-danger">*</span></label>
                            <select class="form-select" id="exam_id" name="exam_id" required>
                                <option value="">اختر الاختبار</option>
                                <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['id']; ?>" <?php echo ($exam_id == $exam['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($exam['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="question_type" class="form-label">نوع السؤال <span class="text-danger">*</span></label>
                            <select class="form-select" id="question_type" name="question_type" required>
                                <option value="single_choice" <?php echo (isset($form_data['question_type']) && $form_data['question_type'] === 'single_choice') ? 'selected' : ''; ?>>اختيار واحد</option>
                                <option value="multiple_choice" <?php echo (isset($form_data['question_type']) && $form_data['question_type'] === 'multiple_choice') ? 'selected' : ''; ?>>اختيار متعدد</option>
                                <option value="drag_drop" <?php echo (isset($form_data['question_type']) && $form_data['question_type'] === 'drag_drop') ? 'selected' : ''; ?>>سحب وإفلات</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="points" class="form-label">النقاط <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="points" name="points" min="1" value="<?php echo htmlspecialchars($form_data['points'] ?? '1'); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="question_text" class="form-label">نص السؤال <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($form_data['question_text'] ?? ''); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="question_image" class="form-label">صورة السؤال (اختياري)</label>
                    <input type="file" class="form-control" id="question_image" name="question_image" accept="image/*">
                    <div class="form-text text-muted">يمكنك إضافة صورة توضيحية للسؤال (الصيغ المدعومة: JPG, PNG, GIF)</div>
                </div>
                
                <!-- Single Choice Options -->
                <div id="single_choice_options" class="question-options">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">خيارات الإجابة</h6>
                        </div>
                        <div class="card-body">
                            <div class="options-container">
                                <?php
                                $single_options = $form_data['options'] ?? [];
                                $correct_option = isset($form_data['correct_option']) ? (int)$form_data['correct_option'] : 0;
                                
                                if (empty($single_options)) {
                                    $single_options = ['', '', '', ''];
                                }
                                
                                foreach ($single_options as $index => $option):
                                ?>
                                <div class="option-item mb-2">
                                    <div class="input-group flex-row-reverse">
                                        <button type="button" class="btn btn-outline-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="text" class="form-control" name="options[]" placeholder="الخيار <?php echo (int)$index + 1; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_option" value="<?php echo $index; ?>" <?php echo ($correct_option === $index) ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary mt-2 add-option">
                                <i class="fas fa-plus"></i> إضافة خيار
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Multiple Choice Options -->
                <div id="multiple_choice_options" class="question-options" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">خيارات الإجابة (يمكن اختيار أكثر من إجابة)</h6>
                        </div>
                        <div class="card-body">
                            <div class="options-container">
                                <?php
                                $multiple_options = $form_data['options'] ?? [];
                                $correct_options = $form_data['correct_options'] ?? [];
                                
                                if (empty($multiple_options)) {
                                    $multiple_options = ['', '', '', ''];
                                }
                                
                                foreach ($multiple_options as $index => $option):
                                ?>
                                <div class="option-item mb-2">
                                    <div class="input-group flex-row-reverse">
                                        <button type="button" class="btn btn-outline-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="text" class="form-control" name="options[]" placeholder="الخيار <?php echo (int)$index + 1; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="correct_options[]" value="<?php echo $index; ?>" <?php echo (in_array($index, $correct_options)) ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary mt-2 add-option">
                                <i class="fas fa-plus"></i> إضافة خيار
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Drag and Drop Options -->
                <div id="drag_drop_options" class="question-options" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">عناصر السحب والإفلات</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>عناصر السحب</h6>
                                    <div class="drag-items-container">
                                        <?php
                                        $drag_items = $form_data['drag_items'] ?? [];
                                        
                                        if (empty($drag_items)) {
                                            $drag_items = ['', '', '', ''];
                                        }
                                        
                                        foreach ($drag_items as $index => $item):
                                            $item_number = (int)$index + 1;
                                        ?>
                                        <div class="drag-item mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control neo-input" name="drag_items[]" placeholder="العنصر <?php echo $item_number; ?>" value="<?php echo htmlspecialchars($item); ?>">
                                                <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drag-item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-primary mt-2 add-drag-item">
                                        <i class="fas fa-plus"></i> إضافة عنصر
                                    </button>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6>مناطق الإفلات</h6>
                                    <div class="drop-zones-container">
                                        <?php
                                        $drop_zones = $form_data['drop_zones'] ?? [];
                                        
                                        if (empty($drop_zones)) {
                                            $drop_zones = ['', '', '', ''];
                                        }
                                        
                                        foreach ($drop_zones as $index => $zone):
                                            $zone_number = (int)$index + 1;
                                        ?>
                                        <div class="drop-zone mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control neo-input" name="drop_zones[]" placeholder="المنطقة <?php echo $zone_number; ?>" value="<?php echo htmlspecialchars($zone); ?>">
                                                <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drop-zone">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-primary mt-2 add-drop-zone">
                                        <i class="fas fa-plus"></i> إضافة منطقة
                                    </button>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> يجب أن يكون عدد عناصر السحب مساويًا لعدد مناطق الإفلات.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/questions<?php echo $exam_id ? '?exam_id=' . $exam_id : ''; ?>" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById("questionForm").addEventListener("submit", function (e) {
        document.querySelectorAll('input[name="options[]"]').forEach(input => {
            if (input.value.trim() === "") {
                input.remove();
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Define the functions first
        function updateDragItemIndices() {
            const dragItemsContainer = document.querySelector('.drag-items-container');
            if (!dragItemsContainer) return;
            
            const items = dragItemsContainer.querySelectorAll('.drag-item');
            items.forEach((item, index) => {
                const itemNumber = Number(index) + 1;
                item.querySelector('input[type="text"]').placeholder = `العنصر ${itemNumber}`;
            });
        }
        
        function updateDropZoneIndices() {
            const dropZonesContainer = document.querySelector('.drop-zones-container');
            if (!dropZonesContainer) return;
            
            const zones = dropZonesContainer.querySelectorAll('.drop-zone');
            zones.forEach((zone, index) => {
                const zoneNumber = Number(index) + 1;
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
        
        // Initial toggle
        toggleQuestionOptions();
                
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
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                    <input type="text" class="form-control" name="options[]" placeholder="الخيار ${Number(optionCount) + 1}">
                    <div class="input-group-text">
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
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="checkbox" name="correct_options[]" value="${optionCount}">
                    </div>
                    <input type="text" class="form-control" name="options[]" placeholder="الخيار ${Number(optionCount) + 1}">
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
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
                    <input type="text" class="form-control" name="drag_items[]" placeholder="العنصر ${Number(itemCount) + 1}">
                    <button type="button" class="btn btn-outline-danger remove-drag-item">
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
                    <input type="text" class="form-control" name="drop_zones[]" placeholder="المنطقة ${Number(zoneCount) + 1}">
                    <button type="button" class="btn btn-outline-danger remove-drop-zone">
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
            
            // Remove required attribute from ALL inputs to prevent validation issues
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
    });
</script>