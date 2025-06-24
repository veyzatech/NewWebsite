/**
 * Google Sheets Form Handler
 * Handles form submission to Google Sheets via Google Apps Script
 */

(function($) {
    'use strict';    // Configuration
    const CONFIG = {
        // TODO: Replace this with your Google Apps Script web app URL
        // Get this URL after deploying your Google Apps Script as a web app
        GOOGLE_SCRIPT_URL: 'https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec',
        FORM_ID: '#contact-form',
        MESSAGE_DIV_ID: '#form-message'
    };

    // Form submission handler
    function handleFormSubmission() {
        $(CONFIG.FORM_ID).on('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const messageDiv = $(CONFIG.MESSAGE_DIV_ID);
            const submitBtn = $(form).find('input[type="submit"]');
            
            // Validate form before submission
            if (!validateForm(form)) {
                showMessage(messageDiv, 'Please fill in all required fields.', 'error');
                return;
            }
            
            // Show loading state
            setLoadingState(submitBtn, true);
            messageDiv.hide();
            
            // Convert FormData to URL-encoded string for Google Apps Script
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                params.append(key, value);
            }
              // Submit to Google Apps Script
            $.ajax({
                url: CONFIG.GOOGLE_SCRIPT_URL,
                type: 'POST',
                data: params.toString(),
                contentType: 'application/x-www-form-urlencoded',
                crossDomain: true,
                success: function(response) {
                    console.log('Server response:', response);
                    let result;
                    try {
                        result = JSON.parse(response);
                    } catch (e) {
                        result = { status: 'success', message: 'Data submitted successfully' };
                    }
                    
                    if (result.status === 'success') {
                        showMessage(messageDiv, 'Thank you! Your information has been submitted successfully.', 'success');
                        form.reset();
                    } else {
                        showMessage(messageDiv, result.message || 'An error occurred. Please try again.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Form submission error:', {xhr, status, error});
                    console.error('Response text:', xhr.responseText);
                    showMessage(messageDiv, 'An error occurred. Please try again later.', 'error');
                },
                complete: function() {
                    setLoadingState(submitBtn, false);
                }
            });
        });
    }

    // Validate form fields
    function validateForm(form) {
        const requiredFields = $(form).find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                field.addClass('error');
                isValid = false;
            } else {
                field.removeClass('error');
                
                // Additional validation for specific fields
                if (field.attr('type') === 'email') {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        field.addClass('error');
                        isValid = false;
                    }
                }
                
                if (field.attr('type') === 'tel') {
                    const phonePattern = /^[\d\s\-\+\(\)]+$/;
                    if (!phonePattern.test(value) || value.length < 10) {
                        field.addClass('error');
                        isValid = false;
                    }
                }
            }
        });
        
        return isValid;
    }

    // Show message to user
    function showMessage(messageDiv, text, type) {
        const styles = {
            success: {
                'background-color': '#d4edda',
                'color': '#155724',
                'border': '1px solid #c3e6cb'
            },
            error: {
                'background-color': '#f8d7da',
                'color': '#721c24',
                'border': '1px solid #f5c6cb'
            }
        };
        
        messageDiv.css({
            ...styles[type],
            'padding': '15px',
            'border-radius': '5px',
            'text-align': 'center',
            'margin-top': '20px',
            'display': 'block'
        });
        messageDiv.text(text);
        messageDiv.show();
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageDiv.fadeOut();
            }, 5000);
        }
    }

    // Set loading state for submit button
    function setLoadingState(submitBtn, isLoading) {
        if (isLoading) {
            submitBtn.val('Submitting...');
            submitBtn.prop('disabled', true);
        } else {
            submitBtn.val('Send Message');
            submitBtn.prop('disabled', false);
        }
    }

    // Add field validation styling
    function addFieldValidation() {
        $(CONFIG.FORM_ID + ' [required]').on('blur', function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                field.addClass('error');
            } else {
                field.removeClass('error');
                
                // Real-time validation for email
                if (field.attr('type') === 'email' && value) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        field.addClass('error');
                    }
                }
                
                // Real-time validation for phone
                if (field.attr('type') === 'tel' && value) {
                    const phonePattern = /^[\d\s\-\+\(\)]+$/;
                    if (!phonePattern.test(value) || value.length < 10) {
                        field.addClass('error');
                    }
                }
            }
        });
        
        // Handle select change events
        $(CONFIG.FORM_ID + ' select[required]').on('change', function() {
            const field = $(this);
            if (field.val()) {
                field.removeClass('error');
            } else {
                field.addClass('error');
            }
        });
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Only initialize if the form exists
        if ($(CONFIG.FORM_ID).length) {
            handleFormSubmission();
            addFieldValidation();
        }
    });

})(jQuery);
