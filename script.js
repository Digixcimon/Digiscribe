document.addEventListener('DOMContentLoaded', function() {
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
            alert('Please fill in all fields');
            return;
        }
        
        // Create email body
        const emailBody = `
Name: ${name}
Email: ${email}

Message:
${message}

---
This message was sent from the contact form.
        `.trim();
        
        // Create mailto URL
        // Replace YOUR_EMAIL@gmail.com with your actual Gmail address
        const mailtoURL = `mailto:YOUR_EMAIL@gmail.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(emailBody)}`;
        
        // Open email client
        window.location.href = mailtoURL;
        
        // Show confirmation message
        showConfirmation();
        
        // Reset form after a delay
        setTimeout(() => {
            form.reset();
        }, 2000);
    });
    
    function showConfirmation() {
        const submitBtn = document.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;
        
        submitBtn.textContent = 'Opening Email Client...';
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