/**
 * Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Confirmation for delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من رغبتك في الحذف؟ هذا الإجراء لا يمكن التراجع عنه.')) {
                e.preventDefault();
            }
        });
    });
    
    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    }
    
    // File input preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById(this.id + '-preview');
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Question type change handler
    const questionTypeSelect = document.getElementById('question_type');
    if (questionTypeSelect) {
        const singleChoiceOptions = document.getElementById('single_choice_options');
        const multipleChoiceOptions = document.getElementById('multiple_choice_options');
        const dragDropOptions = document.getElementById('drag_drop_options');
        
        questionTypeSelect.addEventListener('change', function() {
            const questionType = this.value;
            
            // Hide all option containers
            if (singleChoiceOptions) singleChoiceOptions.style.display = 'none';
            if (multipleChoiceOptions) multipleChoiceOptions.style.display = 'none';
            if (dragDropOptions) dragDropOptions.style.display = 'none';
            
            // Show the selected option container
            switch (questionType) {
                case 'single_choice':
                    if (singleChoiceOptions) singleChoiceOptions.style.display = 'block';
                    break;
                case 'multiple_choice':
                    if (multipleChoiceOptions) multipleChoiceOptions.style.display = 'block';
                    break;
                case 'drag_drop':
                    if (dragDropOptions) dragDropOptions.style.display = 'block';
                    break;
            }
        });
        
        // Trigger change event to initialize the form
        questionTypeSelect.dispatchEvent(new Event('change'));
    }
    
    // Add option button
    const addOptionButtons = document.querySelectorAll('.add-option-btn');
    addOptionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const optionsContainer = this.closest('.options-container').querySelector('.options-list');
            const optionTemplate = this.closest('.options-container').querySelector('.option-template');
            const newOption = optionTemplate.cloneNode(true);
            
            newOption.classList.remove('option-template');
            newOption.classList.remove('d-none');
            
            // Clear input values
            const inputs = newOption.querySelectorAll('input');
            inputs.forEach(input => {
                input.value = '';
            });
            
            // Add event listener to remove button
            const removeButton = newOption.querySelector('.remove-option-btn');
            if (removeButton) {
                removeButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.closest('.option-item').remove();
                });
            }
            
            optionsContainer.appendChild(newOption);
        });
    });
    
    // Remove option button (for existing options)
    const removeOptionButtons = document.querySelectorAll('.remove-option-btn');
    removeOptionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.option-item').remove();
        });
    });
    
    // Drag and drop for question options
    const dragItems = document.querySelectorAll('.drag-item');
    const dropZones = document.querySelectorAll('.drop-zone');
    
    if (dragItems.length > 0 && dropZones.length > 0) {
        dragItems.forEach(item => {
            item.setAttribute('draggable', 'true');
            
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', this.innerHTML);
                e.dataTransfer.setData('key', this.dataset.key || '');
                e.dataTransfer.setData('index', this.dataset.index || '');
                this.classList.add('dragging');
            });
            
            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
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
                    newPlaceholder.textContent = 'اسحب هنا';
                    zone.appendChild(newPlaceholder);
                });
                
                this.appendChild(removeBtn);
            });
        });
    }
    
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = document.getElementById(this.dataset.target);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    // Course price toggle based on is_free checkbox
    const isFreeCheckbox = document.getElementById('is_free');
    const priceInput = document.getElementById('price');
    
    if (isFreeCheckbox && priceInput) {
        isFreeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                priceInput.value = '0';
                priceInput.disabled = true;
            } else {
                priceInput.disabled = false;
            }
        });
        
        // Initialize on page load
        if (isFreeCheckbox.checked) {
            priceInput.value = '0';
            priceInput.disabled = true;
        }
    }
    
    // Exam is_free toggle based on course selection
    const courseSelect = document.getElementById('course_id');
    const examIsFreeCheckbox = document.getElementById('exam_is_free');
    
    if (courseSelect && examIsFreeCheckbox) {
        courseSelect.addEventListener('change', function() {
            const courseOption = this.options[this.selectedIndex];
            const courseIsFree = courseOption.dataset.isFree === '1';
            
            if (courseIsFree) {
                examIsFreeCheckbox.checked = true;
                examIsFreeCheckbox.disabled = true;
            } else {
                examIsFreeCheckbox.disabled = false;
            }
        });
        
        // Initialize on page load
        if (courseSelect.selectedIndex >= 0) {
            const courseOption = courseSelect.options[courseSelect.selectedIndex];
            const courseIsFree = courseOption.dataset.isFree === '1';
            
            if (courseIsFree) {
                examIsFreeCheckbox.checked = true;
                examIsFreeCheckbox.disabled = true;
            }
        }
    }
    
    // Data tables initialization
    const dataTables = document.querySelectorAll('.datatable');
    if (dataTables.length > 0 && typeof $.fn.DataTable !== 'undefined') {
        dataTables.forEach(table => {
            $(table).DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json'
                },
                responsive: true
            });
        });
    }
});