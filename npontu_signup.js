// Initialize Feather Icons
document.addEventListener('DOMContentLoaded', () => {
    feather.replace();
});

// Form validation
const signupForm = document.querySelector('form');
const inputs = {
    firstName: document.getElementById('first-name'),
    lastName: document.getElementById('last-name'),
    email: document.getElementById('email'),
    company: document.getElementById('company'),
    password: document.getElementById('password'),
    confirmPassword: document.getElementById('confirm-password')
};
const signupButton = document.querySelector('.btn-signup');

// Validation patterns
const patterns = {
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    name: /^[a-zA-Z\s'-]{2,}$/,
    password: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/
};

// Error messages
const errorMessages = {
    required: 'This field is required',
    email: 'Please enter a valid email address',
    name: 'Please enter a valid name (minimum 2 characters)',
    password: 'Password must be at least 8 characters and contain at least one letter and one number',
    passwordMatch: 'Passwords do not match',
    company: 'Company name must be at least 2 characters'
};

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

// Validate input
function validateInput(input, pattern, errorMessage) {
    const value = input.value.trim();
    
    if (value === '') {
        showError(input, errorMessages.required);
        return false;
    } else if (pattern && !pattern.test(value)) {
        showError(input, errorMessage);
        return false;
    } else {
        removeError(input);
        return true;
    }
}

// Validate all inputs
function validateForm() {
    const validations = {
        firstName: validateInput(inputs.firstName, patterns.name, errorMessages.name),
        lastName: validateInput(inputs.lastName, patterns.name, errorMessages.name),
        email: validateInput(inputs.email, patterns.email, errorMessages.email),
        company: validateInput(inputs.company, /.{2,}/, errorMessages.company),
        password: validateInput(inputs.password, patterns.password, errorMessages.password),
        confirmPassword: inputs.confirmPassword.value === inputs.password.value
    };
    
    if (!validations.confirmPassword) {
        showError(inputs.confirmPassword, errorMessages.passwordMatch);
    }
    
    return Object.values(validations).every(v => v === true);
}

// Real-time validation
Object.entries(inputs).forEach(([key, input]) => {
    input.addEventListener('input', () => {
        if (key === 'confirmPassword') {
            if (input.value === inputs.password.value) {
                removeError(input);
            } else {
                showError(input, errorMessages.passwordMatch);
            }
        } else {
            validateInput(
                input,
                patterns[key],
                errorMessages[key]
            );
        }
    });
});

// Password strength indicator
function createPasswordStrengthIndicator() {
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength';
    strengthIndicator.style.marginTop = '4px';
    
    const strengthBar = document.createElement('div');
    strengthBar.className = 'strength-bar';
    strengthBar.style.height = '4px';
    strengthBar.style.backgroundColor = '#e2e8f0';
    strengthBar.style.borderRadius = '2px';
    strengthBar.style.overflow = 'hidden';
    
    const strengthFill = document.createElement('div');
    strengthFill.className = 'strength-fill';
    strengthFill.style.height = '100%';
    strengthFill.style.width = '0%';
    strengthFill.style.backgroundColor = '#cbd5e1';
    strengthFill.style.transition = 'all 0.3s ease';
    
    const strengthText = document.createElement('div');
    strengthText.className = 'strength-text';
    strengthText.style.fontSize = '12px';
    strengthText.style.marginTop = '4px';
    strengthText.style.color = '#64748b';
    
    strengthBar.appendChild(strengthFill);
    strengthIndicator.appendChild(strengthBar);
    strengthIndicator.appendChild(strengthText);
    
    inputs.password.parentElement.appendChild(strengthIndicator);
    
    return { strengthFill, strengthText };
}

const { strengthFill, strengthText } = createPasswordStrengthIndicator();

inputs.password.addEventListener('input', () => {
    const password = inputs.password.value;
    let strength = 0;
    let message = '';
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[A-Z]/)) strength += 25;
    if (password.match(/[0-9]/)) strength += 25;
    if (password.match(/[^A-Za-z0-9]/)) strength += 25;
    
    strengthFill.style.width = `${strength}%`;
    
    if (strength <= 25) {
        strengthFill.style.backgroundColor = '#ef4444';
        message = 'Weak';
    } else if (strength <= 50) {
        strengthFill.style.backgroundColor = '#f59e0b';
        message = 'Fair';
    } else if (strength <= 75) {
        strengthFill.style.backgroundColor = '#10b981';
        message = 'Good';
    } else {
        strengthFill.style.backgroundColor = '#059669';
        message = 'Strong';
    }
    
    strengthText.textContent = `Password strength: ${message}`;
});

// Form submission
signupForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    if (validateForm()) {
        // Show loading state
        signupButton.disabled = true;
        signupButton.innerHTML = `
            <span class="loading-spinner"></span>
            Creating account...
        `;
        
        // Simulate API call
        setTimeout(() => {
            // Here you would typically make your actual API call
            // For demonstration, we'll just simulate a successful signup
            signupForm.submit();
        }, 1500);
    }
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