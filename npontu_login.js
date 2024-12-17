// Initialize Feather Icons
document.addEventListener('DOMContentLoaded', () => {
    feather.replace();
});

// Form validation
const loginForm = document.querySelector('form');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginButton = document.querySelector('.btn-login');

// Email validation regex
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Show error message
function showError(input, message) {
    const formGroup = input.parentElement;
    const errorDiv = formGroup.querySelector('.error-message') || document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#dc2626';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.marginTop = '4px';
    errorDiv.textContent = message;
    
    if (!formGroup.querySelector('.error-message')) {
        formGroup.appendChild(errorDiv);
    }
    
    input.style.borderColor = '#dc2626';
}

// Remove error message
function removeError(input) {
    const formGroup = input.parentElement;
    const errorDiv = formGroup.querySelector('.error-message');
    if (errorDiv) {
        formGroup.removeChild(errorDiv);
    }
    input.style.borderColor = '#cbd5e1';
}

// Validate email
function validateEmail() {
    const email = emailInput.value.trim();
    
    if (email === '') {
        showError(emailInput, 'Email is required');
        return false;
    } else if (!emailRegex.test(email)) {
        showError(emailInput, 'Please enter a valid email address');
        return false;
    } else {
        removeError(emailInput);
        return true;
    }
}

// Validate password
function validatePassword() {
    const password = passwordInput.value.trim();
    
    if (password === '') {
        showError(passwordInput, 'Password is required');
        return false;
    } else if (password.length < 6) {
        showError(passwordInput, 'Password must be at least 6 characters');
        return false;
    } else {
        removeError(passwordInput);
        return true;
    }
}

// Real-time validation
emailInput.addEventListener('input', validateEmail);
passwordInput.addEventListener('input', validatePassword);

// Form submission
loginForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();
    
    if (isEmailValid && isPasswordValid) {
        // Show loading state
        loginButton.disabled = true;
        loginButton.innerHTML = `
            <span class="loading-spinner"></span>
            Signing in...
        `;
        
        // Simulate API call
        setTimeout(() => {
            // Here you would typically make your actual API call
            // For demonstration, we'll just simulate a successful login
            loginForm.submit();
        }, 1500);
    }
});

// Remember me functionality
const rememberMeCheckbox = document.querySelector('input[name="remember"]');
if (rememberMeCheckbox) {
    // Check if there's a saved email
    const savedEmail = localStorage.getItem('rememberedEmail');
    if (savedEmail) {
        emailInput.value = savedEmail;
        rememberMeCheckbox.checked = true;
    }

    rememberMeCheckbox.addEventListener('change', () => {
        if (rememberMeCheckbox.checked) {
            localStorage.setItem('rememberedEmail', emailInput.value);
        } else {
            localStorage.removeItem('rememberedEmail');
        }
    });
}

// Password visibility toggle
const togglePassword = document.createElement('button');
togglePassword.type = 'button';
togglePassword.innerHTML = '<i data-feather="eye"></i>';
togglePassword.className = 'toggle-password';
togglePassword.style.position = 'absolute';
togglePassword.style.right = '12px';
togglePassword.style.top = '50%';
togglePassword.style.transform = 'translateY(-50%)';
togglePassword.style.background = 'none';
togglePassword.style.border = 'none';
togglePassword.style.cursor = 'pointer';
togglePassword.style.color = '#64748b';

const passwordWrapper = document.createElement('div');
passwordWrapper.style.position = 'relative';
passwordInput.parentNode.insertBefore(passwordWrapper, passwordInput);
passwordWrapper.appendChild(passwordInput);
passwordWrapper.appendChild(togglePassword);

togglePassword.addEventListener('click', () => {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    togglePassword.innerHTML = `<i data-feather="${type === 'password' ? 'eye' : 'eye-off'}"></i>`;
    feather.replace();
});

// Add loading spinner styles
const style = document.createElement('style');
style.textContent = `
    @keyframes spinner {
        to {transform: rotate(360deg);}
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 8px;
        border: 3px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
    }
`;
document.head.appendChild(style);