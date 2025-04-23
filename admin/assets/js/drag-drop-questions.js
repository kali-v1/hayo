/**
 * Drag and Drop Questions JavaScript
 * Handles the functionality for drag and drop question type in the admin panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements for drag and drop question type
    const dragItemsContainer = document.querySelector('.drag-items-container');
    const dropZonesContainer = document.querySelector('.drop-zones-container');
    const dragItemsPreview = document.querySelector('.drag-items-preview');
    const dropZonesPreview = document.querySelector('.drop-zones-preview');
    const addDragItemBtn = document.querySelector('.add-drag-item');
    const addDropZoneBtn = document.querySelector('.add-drop-zone');
    
    // Function to update the preview
    function updateDragDropPreview() {
        if (!dragItemsPreview || !dropZonesPreview) return;
        
        // Clear previews
        dragItemsPreview.innerHTML = '';
        dropZonesPreview.innerHTML = '';
        
        // Get all drag items
        const dragItems = document.querySelectorAll('input[name="drag_items[]"]');
        const dropZones = document.querySelectorAll('input[name="drop_zones[]"]');
        
        // Create preview elements for drag items
        dragItems.forEach((item, index) => {
            if (item.value.trim() === '') return;
            
            const dragItemPreview = document.createElement('div');
            dragItemPreview.className = 'neo-card p-2 mb-2 drag-item-preview';
            dragItemPreview.setAttribute('draggable', 'true');
            dragItemPreview.setAttribute('data-index', index);
            dragItemPreview.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-grip-lines me-2"></i>
                    <span>${item.value}</span>
                </div>
            `;
            
            // Add drag events
            dragItemPreview.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', item.value);
                e.dataTransfer.setData('index', index);
                this.classList.add('dragging');
            });
            
            dragItemPreview.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
            
            dragItemsPreview.appendChild(dragItemPreview);
        });
        
        // Create preview elements for drop zones
        dropZones.forEach((zone, index) => {
            if (zone.value.trim() === '') return;
            
            const dropZonePreview = document.createElement('div');
            dropZonePreview.className = 'neo-card p-3 mb-2 drop-zone-preview';
            dropZonePreview.setAttribute('data-index', index);
            
            // Create the drop zone content
            const dropZoneContent = document.createElement('div');
            dropZoneContent.className = 'd-flex align-items-center justify-content-between';
            dropZoneContent.innerHTML = `
                <div class="drop-zone-label">${zone.value}</div>
                <div class="drop-zone-target p-2 border border-dashed rounded" data-index="${index}">
                    <span class="text-muted">اسحب العنصر هنا</span>
                </div>
            `;
            
            dropZonePreview.appendChild(dropZoneContent);
            
            // Add drop events to the target area
            const dropTarget = dropZoneContent.querySelector('.drop-zone-target');
            
            dropTarget.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });
            
            dropTarget.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });
            
            dropTarget.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                const itemValue = e.dataTransfer.getData('text/plain');
                const itemIndex = e.dataTransfer.getData('index');
                
                // Update the drop target with the dragged item
                this.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>${itemValue}</span>
                    </div>
                `;
                
                // Mark the drag item as used
                const dragItem = dragItemsPreview.querySelector(`[data-index="${itemIndex}"]`);
                if (dragItem) {
                    dragItem.classList.add('used');
                }
            });
            
            dropZonesPreview.appendChild(dropZonePreview);
        });
    }
    
    // Add new drag item
    if (addDragItemBtn) {
        addDragItemBtn.addEventListener('click', function() {
            const newIndex = document.querySelectorAll('.drag-item').length;
            const newDragItem = document.createElement('div');
            newDragItem.className = 'drag-item mb-2';
            newDragItem.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control neo-input" name="drag_items[]" placeholder="العنصر ${newIndex + 1}" required>
                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drag-item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            // Add event listener to the remove button
            const removeBtn = newDragItem.querySelector('.remove-drag-item');
            removeBtn.addEventListener('click', function() {
                newDragItem.remove();
                updateDragDropPreview();
            });
            
            // Add event listener to the input for live preview update
            const input = newDragItem.querySelector('input');
            input.addEventListener('input', updateDragDropPreview);
            
            dragItemsContainer.appendChild(newDragItem);
            updateDragDropPreview();
        });
    }
    
    // Add new drop zone
    if (addDropZoneBtn) {
        addDropZoneBtn.addEventListener('click', function() {
            const newIndex = document.querySelectorAll('.drop-zone').length;
            const newDropZone = document.createElement('div');
            newDropZone.className = 'drop-zone mb-2';
            newDropZone.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control neo-input" name="drop_zones[]" placeholder="المنطقة ${newIndex + 1}" required>
                    <button type="button" class="neo-btn neo-btn-sm neo-btn-danger remove-drop-zone">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            // Add event listener to the remove button
            const removeBtn = newDropZone.querySelector('.remove-drop-zone');
            removeBtn.addEventListener('click', function() {
                newDropZone.remove();
                updateDragDropPreview();
            });
            
            // Add event listener to the input for live preview update
            const input = newDropZone.querySelector('input');
            input.addEventListener('input', updateDragDropPreview);
            
            dropZonesContainer.appendChild(newDropZone);
            updateDragDropPreview();
        });
    }
    
    // Add event listeners to existing remove buttons
    const removeDragItemBtns = document.querySelectorAll('.remove-drag-item');
    removeDragItemBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.drag-item').remove();
            updateDragDropPreview();
        });
    });
    
    const removeDropZoneBtns = document.querySelectorAll('.remove-drop-zone');
    removeDropZoneBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.drop-zone').remove();
            updateDragDropPreview();
        });
    });
    
    // Add event listeners to existing inputs for live preview update
    const dragItemInputs = document.querySelectorAll('input[name="drag_items[]"]');
    dragItemInputs.forEach(input => {
        input.addEventListener('input', updateDragDropPreview);
    });
    
    const dropZoneInputs = document.querySelectorAll('input[name="drop_zones[]"]');
    dropZoneInputs.forEach(input => {
        input.addEventListener('input', updateDragDropPreview);
    });
    
    // Initialize the preview
    updateDragDropPreview();
});