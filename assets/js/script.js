/**
 * Main Website JavaScript
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
    
    // Language switcher
    const languageLinks = document.querySelectorAll('.dropdown-item[href*="lang="]');
    languageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // We're now using the server-side function to generate the URL
            // No need to prevent default or manipulate the URL here
        });
    });
    
    // Handle RTL/LTR direction changes
    function applyDirectionChanges(direction) {
        document.documentElement.setAttribute('dir', direction);
        
        // Update CSS classes for RTL/LTR
        if (direction === 'rtl') {
            document.body.classList.add('rtl');
            document.body.classList.remove('ltr');
        } else {
            document.body.classList.add('ltr');
            document.body.classList.remove('rtl');
        }
        
        // Adjust Bootstrap components for RTL/LTR
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dropdown => {
            if (direction === 'rtl') {
                dropdown.classList.add('dropdown-menu-end');
            } else {
                dropdown.classList.remove('dropdown-menu-end');
            }
        });
        
        // Adjust icon margins for RTL/LTR
        const icons = document.querySelectorAll('.fa, .fas, .far, .fab');
        icons.forEach(icon => {
            const parentElement = icon.parentElement;
            if (direction === 'rtl') {
                if (parentElement.classList.contains('me-1')) {
                    parentElement.classList.remove('me-1');
                    parentElement.classList.add('ms-1');
                } else if (parentElement.classList.contains('me-2')) {
                    parentElement.classList.remove('me-2');
                    parentElement.classList.add('ms-2');
                }
            } else {
                if (parentElement.classList.contains('ms-1')) {
                    parentElement.classList.remove('ms-1');
                    parentElement.classList.add('me-1');
                } else if (parentElement.classList.contains('ms-2')) {
                    parentElement.classList.remove('ms-2');
                    parentElement.classList.add('me-2');
                }
            }
        });
    }
    
    // Apply direction changes on page load
    const currentDirection = document.documentElement.getAttribute('dir') || 'ltr';
    applyDirectionChanges(currentDirection);
    
    // Course rating stars
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    if (ratingInputs.length > 0) {
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                const rating = this.value;
                const stars = document.querySelectorAll('.rating-stars i');
                
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('text-warning');
                        star.classList.remove('text-muted');
                    } else {
                        star.classList.add('text-muted');
                        star.classList.remove('text-warning');
                    }
                });
            });
        });
    }
    
    // Exam timer
    const timerElement = document.getElementById('timer');
    if (timerElement) {
        const duration = parseInt(timerElement.dataset.duration);
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
    }
    
    // Drag and drop functionality for exams
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
                    newPlaceholder.textContent = 'Drop here';
                    zone.appendChild(newPlaceholder);
                });
                
                this.appendChild(removeBtn);
            });
        });
    }
    
    // Question navigation in exams
    const questionNavBtns = document.querySelectorAll('.question-nav-btn');
    const nextButtons = document.querySelectorAll('.next-question');
    const prevButtons = document.querySelectorAll('.prev-question');
    
    if (questionNavBtns.length > 0) {
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
            const totalQuestions = questionNavBtns.length;
            const progress = (currentQuestion / totalQuestions) * 100;
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
        const finishButton = document.getElementById('finish-exam');
        if (finishButton) {
            finishButton.addEventListener('click', function() {
                showSubmitConfirmation();
            });
        }
        
        // Submit exam button
        const submitButton = document.getElementById('submit-exam-btn');
        if (submitButton) {
            submitButton.addEventListener('click', function() {
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
                    listItem.textContent = 'Question ' + questionNumber;
                    unansweredList.appendChild(listItem);
                });
            } else {
                unansweredAlert.classList.add('d-none');
            }
            
            // Show confirmation modal
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
            confirmModal.show();
        }
    }
});