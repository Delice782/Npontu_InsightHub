function validateForm() {
    const fullName = document.getElementById('full-name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    let valid = true;

    if (fullName.split(" ").length < 2) {
        document.getElementById('full-name-error').innerText = "Please enter both first and last names.";
        document.getElementById('full-name-error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('full-name-error').style.display = "none";
    }

    if (!/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById('email-error').innerText = "Please enter a valid email address.";
        document.getElementById('email-error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('email-error').style.display = "none";
    }

    const passwordRegex = /^(?=.*[A-Z])(?=(.*\d){3,})(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;
    if (!passwordRegex.test(password)) {
        document.getElementById('password-error').innerText = "Password must be at least 8 characters long, include one uppercase letter, three digits, and one special character.";
        document.getElementById('password-error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('password-error').style.display = "none";
    }

    if (password !== confirmPassword) {
        document.getElementById('confirm-password-error').innerText = "Passwords do not match.";
        document.getElementById('confirm-password-error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('confirm-password-error').style.display = "none";
    }

    return valid;
}