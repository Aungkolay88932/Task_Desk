// Simple password toggle function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Wait for page to load
document.addEventListener('DOMContentLoaded', function() {
    
    // LOGIN FORM VALIDATION
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        
        // Real-time password validation for login
        const loginPasswordInput = document.getElementById('password');
        
        if (loginPasswordInput) {
            // Add a div for password feedback if it doesn't exist
            let passwordFeedback = loginPasswordInput.parentElement.nextElementSibling;
            if (!passwordFeedback || !passwordFeedback.classList.contains('input-feedback')) {
                passwordFeedback = document.createElement('div');
                passwordFeedback.className = 'input-feedback';
                passwordFeedback.id = 'loginPasswordFeedback';
                loginPasswordInput.parentElement.insertAdjacentElement('afterend', passwordFeedback);
            }
            
            loginPasswordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Clear previous message
                passwordFeedback.innerHTML = '';
                
                // Check password length
                if (password.length > 0 && password.length < 8) {
                    passwordFeedback.innerHTML = '<span style="color: red;">Password must be at least 8 characters</span>';
                } else if (password.length >= 8) {
                    passwordFeedback.innerHTML = '<span style="color: green;">✓ Password is valid</span>';
                }
            });
        }
        
        loginForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const password = document.getElementById('password').value;
            
            // Check if fields are empty
            if (name === '' || password === '') {
                e.preventDefault();
                alert('Please fill in all fields!');
                return false;
            }
            
            // Check password length - MUST BE AT LEAST 8 CHARACTERS
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
            
            // All checks passed - form can submit
            return true;
        });
    }
    
    // SIGNUP FORM VALIDATION
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        
        // Real-time password validation
        const passwordInput = document.getElementById('signupPassword');
        const strengthDiv = document.getElementById('passwordStrength');
        
        if (passwordInput && strengthDiv) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Clear previous message
                strengthDiv.innerHTML = '';
                
                // Check password length
                if (password.length > 0 && password.length < 8) {
                    strengthDiv.innerHTML = '<span style="color: red;">Password must be at least 8 characters</span>';
                } else if (password.length >= 8) {
                    strengthDiv.innerHTML = '<span style="color: green;">✓ Password is valid</span>';
                }
            });
        }
        
        // Password confirmation check
        const confirmInput = document.getElementById('confirmPassword');
        const confirmFeedback = document.getElementById('confirmFeedback');
        
        if (confirmInput && confirmFeedback) {
            confirmInput.addEventListener('input', function() {
                const password = document.getElementById('signupPassword').value;
                const confirmPassword = this.value;
                
                // Clear previous message
                confirmFeedback.innerHTML = '';
                
                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        confirmFeedback.innerHTML = '<span style="color: green;">✓ Passwords match</span>';
                    } else {
                        confirmFeedback.innerHTML = '<span style="color: red;">Passwords do not match</span>';
                    }
                }
            });
        }
        
        // Form submission validation
        signupForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Check if all fields are filled
            if (username === '' || email === '' || password === '' || confirmPassword === '') {
                e.preventDefault();
                alert('Please fill in all fields!');
                return false;
            }
            
            // Check password length - MUST BE AT LEAST 8 CHARACTERS
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
            
            // Check if passwords match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            // All checks passed - form can submit
            return true;
        });
    }
    
});

// Logout confirmation function
function confirmLogout() {
    // Show confirmation dialog
    const result = confirm("Are you sure do you want to log out?");
    
    // If user clicks OK (true), redirect to login page
    if (result) {
        window.location.href = "login.html";
    }
    // If user clicks Cancel (false), nothing happens - alert box closes automatically
}
