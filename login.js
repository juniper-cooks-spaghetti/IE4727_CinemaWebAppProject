document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        let isValid = true;
        let errorMessage = '';

        // Basic email validation
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }

        // Basic password validation
        if (!password || password.length < 6) {
            isValid = false;
            errorMessage = 'Password must be at least 6 characters long';
        }

        if (!isValid) {
            e.preventDefault();
            // You might want to add an error message display element in your HTML
            const errorDiv = document.querySelector('.error-message') || document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = errorMessage;
            
            if (!document.querySelector('.error-message')) {
                loginForm.insertBefore(errorDiv, loginForm.firstChild);
            }
        }
    });
});