document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const responseMessage = document.getElementById('responseMessage');
    const submitBtn = document.querySelector('.submit-btn');
    const btnText = document.querySelector('.btn-text');
    const btnLoader = document.querySelector('.btn-loader');

    // Form validation functions
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePhone(phone) {
        if (!phone) return true; // Phone is optional
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
    }

    function validateForm() {
        let isValid = true;
        
        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(error => {
            error.textContent = '';
        });

        // Validate name
        const name = document.getElementById('name').value.trim();
        if (name.length < 2) {
            document.getElementById('nameError').textContent = 'Name must be at least 2 characters long';
            isValid = false;
        }

        // Validate email
        const email = document.getElementById('email').value.trim();
        if (!validateEmail(email)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address';
            isValid = false;
        }

        // Validate phone (optional)
        const phone = document.getElementById('phone').value.trim();
        if (phone && !validatePhone(phone)) {
            document.getElementById('phoneError').textContent = 'Please enter a valid phone number';
            isValid = false;
        }

        // Validate subject
        const subject = document.getElementById('subject').value.trim();
        if (subject.length < 3) {
            document.getElementById('subjectError').textContent = 'Subject must be at least 3 characters long';
            isValid = false;
        }

        // Validate message
        const message = document.getElementById('message').value.trim();
        if (message.length < 10) {
            document.getElementById('messageError').textContent = 'Message must be at least 10 characters long';
            isValid = false;
        }

        return isValid;
    }

    function showResponseMessage(message, type) {
        responseMessage.textContent = message;
        responseMessage.className = `response-message ${type}`;
        responseMessage.style.display = 'block';
        
        // Scroll to message
        responseMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Hide message after 5 seconds for success, keep error messages
        if (type === 'success') {
            setTimeout(() => {
                responseMessage.style.display = 'none';
            }, 5000);
        }
    }

    function setLoadingState(isLoading) {
        if (isLoading) {
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
        } else {
            submitBtn.disabled = false;
            btnText.style.display = 'inline-block';
            btnLoader.style.display = 'none';
        }
    }

    // Form submission handler
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Hide previous response message
        responseMessage.style.display = 'none';
        
        // Validate form
        if (!validateForm()) {
            showResponseMessage('Please correct the errors above and try again.', 'error');
            return;
        }

        // Set loading state
        setLoadingState(true);

        try {
            // Prepare form data
            const formData = new FormData(form);
            
            // Send form data to PHP script
            const response = await fetch('send_email.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showResponseMessage(result.message, 'success');
                form.reset(); // Clear form on success
            } else {
                showResponseMessage(result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showResponseMessage('An error occurred while sending your message. Please try again later.', 'error');
        } finally {
            setLoadingState(false);
        }
    });

    // Real-time validation feedback
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value.trim();
        const errorElement = document.getElementById('emailError');
        
        if (email && !validateEmail(email)) {
            errorElement.textContent = 'Please enter a valid email address';
        } else {
            errorElement.textContent = '';
        }
    });

    document.getElementById('phone').addEventListener('blur', function() {
        const phone = this.value.trim();
        const errorElement = document.getElementById('phoneError');
        
        if (phone && !validatePhone(phone)) {
            errorElement.textContent = 'Please enter a valid phone number';
        } else {
            errorElement.textContent = '';
        }
    });

    // Character counter for message
    const messageTextarea = document.getElementById('message');
    const messageGroup = messageTextarea.parentElement;
    
    // Create character counter
    const charCounter = document.createElement('div');
    charCounter.style.cssText = 'text-align: right; font-size: 0.9rem; color: #666; margin-top: 5px;';
    charCounter.textContent = '0 characters';
    messageGroup.appendChild(charCounter);

    messageTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCounter.textContent = `${length} characters`;
        
        if (length < 10) {
            charCounter.style.color = '#e74c3c';
        } else {
            charCounter.style.color = '#27ae60';
        }
    });

    // Auto-resize textarea
    messageTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Add smooth transitions to form inputs
    document.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});