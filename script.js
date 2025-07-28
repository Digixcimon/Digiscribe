document.addEventListener('DOMContentLoaded', function() {
    // Initialize EmailJS
    emailjs.init({
        publicKey: "YOUR_PUBLIC_KEY", // Replace with your EmailJS public key
    });
    
    const form = document.getElementById('contactForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        
        // Validate form
        if (!name || !email || !subject || !message) {
            updateStatus('Please fill in all fields', 'error');
            return;
        }
        
        // Update status
        updateStatus('Sending email...', 'sending');
        
        // Send email using EmailJS
        const templateParams = {
            from_name: name,
            from_email: email,
            subject: subject,
            message: message,
            to_email: 'your-email@gmail.com' // Replace with your Gmail address
        };
        
        emailjs.send('YOUR_SERVICE_ID', 'YOUR_TEMPLATE_ID', templateParams)
            .then(function(response) {
                console.log('SUCCESS!', response.status, response.text);
                updateStatus('Email sent successfully!', 'success');
                showConfirmation();
                
                // Reset form after a delay
                setTimeout(() => {
                    form.reset();
                    updateStatus('Ready to send', 'ready');
                }, 3000);
            }, function(error) {
                console.log('FAILED...', error);
                updateStatus('Failed to send email. Please try again.', 'error');
            });
    });
    
    function updateStatus(message, type) {
        const statusElement = document.getElementById('status');
        statusElement.textContent = message;
        
        // Remove existing status classes
        statusElement.classList.remove('status-ready', 'status-sending', 'status-success', 'status-error');
        
        // Add appropriate class
        statusElement.classList.add(`status-${type}`);
    }
    
    function showConfirmation() {
        const submitBtn = document.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;
        
        submitBtn.textContent = 'Email Sent!';
        submitBtn.style.backgroundColor = '#28a745';
        
        setTimeout(() => {
            submitBtn.textContent = originalText;
            submitBtn.style.backgroundColor = '';
        }, 3000);
    }
    
    // Add input validation and styling
    const inputs = document.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
        
        input.addEventListener('focus', function() {
            this.style.borderColor = '#667eea';
        });
    });
    
    // Email validation
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.value) && this.value !== '') {
            this.style.borderColor = '#dc3545';
            showValidationMessage(this, 'Please enter a valid email address');
        } else if (this.value !== '') {
            this.style.borderColor = '#28a745';
            hideValidationMessage(this);
        }
    });
    
    function showValidationMessage(element, message) {
        // Remove existing validation message
        hideValidationMessage(element);
        
        const validationDiv = document.createElement('div');
        validationDiv.className = 'validation-message';
        validationDiv.textContent = message;
        validationDiv.style.cssText = `
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: block;
        `;
        
        element.parentNode.appendChild(validationDiv);
    }
    
    function hideValidationMessage(element) {
        const existingMessage = element.parentNode.querySelector('.validation-message');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
});

// Add some nice animations
document.addEventListener('DOMContentLoaded', function() {
    const formGroups = document.querySelectorAll('.form-group');
    
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            group.style.transition = 'all 0.6s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, index * 100);
    });
});