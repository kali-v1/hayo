/**
 * Lesson Sortable Functionality
 * This script handles the drag and drop functionality for reordering lessons
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log("Lesson sortable script loaded");
    
    // Check if jQuery and jQuery UI are available
    if (typeof jQuery === 'undefined') {
        console.error("jQuery is not loaded!");
        return;
    }
    
    if (typeof jQuery.ui === 'undefined') {
        console.error("jQuery UI is not loaded!");
        return;
    }
    
    console.log("jQuery version:", jQuery.fn.jquery);
    console.log("jQuery UI version:", jQuery.ui.version);
    
    // Debug information
    console.log("Document ready state:", document.readyState);
    console.log("Save order button exists:", jQuery("#save-order").length > 0);
    console.log("Sortable table exists:", jQuery("#sortable-lessons").length > 0);
    console.log("Number of rows in sortable table:", jQuery("#sortable-lessons tr").length);
    console.log("Number of lesson rows:", jQuery("#sortable-lessons .lesson-row").length);
    
    // Get the sortable table
    var sortableTable = jQuery("#sortable-lessons");
    
    if (sortableTable.length === 0) {
        console.error("Sortable table not found!");
        return;
    }
    
    console.log("Sortable table found:", sortableTable);
    
    // Initialize sortable
    try {
        sortableTable.sortable({
            items: ".lesson-row",
            cursor: "move",
            axis: "y",
            handle: ".handle",
            placeholder: "ui-state-highlight",
            forcePlaceholderSize: true,
            opacity: 0.8,
            tolerance: "pointer",
            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    jQuery(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            update: function(event, ui) {
                // Update the order numbers visually
                sortableTable.find(".lesson-row").each(function(index) {
                    jQuery(this).find(".handle").text(index + 1);
                });
            }
        }).disableSelection();
        
        console.log("Sortable initialized successfully");
    } catch (e) {
        console.error("Error initializing sortable:", e);
    }
    
    // Save order button
    jQuery("#save-order").on("click", function() {
        console.log("Save order button clicked");
        
        var lessonOrder = [];
        
        sortableTable.find(".lesson-row").each(function() {
            lessonOrder.push(jQuery(this).data("lesson-id"));
        });
        
        console.log("Lesson order:", lessonOrder);
        
        // Get the course ID from the URL
        var urlParts = window.location.pathname.split('/');
        var courseId = urlParts[3]; // Assuming URL pattern is /admin/courses/{id}/lessons
        
        console.log("Course ID:", courseId);
        
        // Show loading state
        var saveButton = jQuery(this);
        var originalText = saveButton.html();
        saveButton.html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        saveButton.prop('disabled', true);
        
        // Send AJAX request
        jQuery.ajax({
            url: "/admin/courses/" + courseId + "/lessons/reorder",
            method: "POST",
            data: {
                lesson_order: JSON.stringify(lessonOrder)
            },
            success: function(response) {
                console.log("Response:", response);
                
                try {
                    var result = JSON.parse(response);
                    
                    if (result.success) {
                        // Show success message
                        saveButton.html('<i class="fas fa-check"></i> تم الحفظ');
                        setTimeout(function() {
                            saveButton.html(originalText);
                            saveButton.prop('disabled', false);
                        }, 2000);
                        
                        // Show alert
                        alert(result.message);
                    } else {
                        // Reset button
                        saveButton.html(originalText);
                        saveButton.prop('disabled', false);
                        
                        // Show error
                        alert("حدث خطأ: " + result.message);
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    
                    // Reset button
                    saveButton.html(originalText);
                    saveButton.prop('disabled', false);
                    
                    alert("حدث خطأ أثناء معالجة الاستجابة");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                
                // Reset button
                saveButton.html(originalText);
                saveButton.prop('disabled', false);
                
                alert("حدث خطأ أثناء حفظ الترتيب");
            }
        });
    });
});