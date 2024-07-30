

document.getElementById('showRegister').addEventListener('click', function() {
    document.getElementById('login-container').style.display = 'none';
    document.getElementById('register-container').style.display = 'block';
});

document.getElementById('showLogin').addEventListener('click', function() {
    document.getElementById('register-container').style.display = 'none';
    document.getElementById('login-container').style.display = 'block';
});

document.getElementById('forgotPassword').addEventListener('click', function() {
    document.getElementById('login-container').style.display = 'none';
    document.getElementById('forgot-password-container').style.display = 'block';
});

document.getElementById('backToLogin').addEventListener('click', function() {
    document.getElementById('forgot-password-container').style.display = 'none';
    document.getElementById('login-container').style.display = 'block';
});
/*send  code section*/
document.getElementById('sendCodeButton').addEventListener('click', function() {
    const contactInfo = document.getElementById('contactInfo').value;
    const errorMessage = document.getElementById('forgotPasswordErrorMessage');
    const successMessage = document.getElementById('forgotPasswordSuccessMessage');

    if (contactInfo) {
        // Clear any error message
        errorMessage.textContent = '';

        successMessage.textContent = 'Verification code sent. Please check your email or phone.';
        successMessage.style.display = 'block';

        // Show the verification code input field and submit button
        document.getElementById('codeField').style.display = 'block';
        document.getElementById('submitCodeButtonField').style.display = 'block';
    } else {
        errorMessage.textContent = 'Please enter your email or phone number.';
    }
});

/*send  code section*/
/*forgot password section*/
document.getElementById('forgotPasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const userCode = document.getElementById('verificationCode').value;
    const errorMessage = document.getElementById('forgotPasswordErrorMessage');
    const successMessage = document.getElementById('forgotPasswordSuccessMessage');

    // For demonstration purposes, assume the verification code is '123456'
    const verificationCode = '123456';

    if (userCode === verificationCode) {
        // Clear any error message
        errorMessage.textContent = '';

        // Simulate successful verification
        successMessage.textContent = 'Verification successful. You can now reset your password.';
        successMessage.style.display = 'block';

        // Redirect to password reset page or show a password reset form
        setTimeout(function() {
            window.location.href = 'reset-password.html';
        }, 2000);
    } else {
        errorMessage.textContent = 'Invalid verification code.';
    }
});

/*forgot password section*/
/*register section

document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const newUsername = document.getElementById('newUsername').value;
    const newPassword = document.getElementById('newPassword').value;
    const errorMessage = document.getElementById('registerErrorMessage');
    const successMessage = document.getElementById('registerSuccessMessage');

    if (newUsername && newPassword) {
        // Clear any error message
        errorMessage.textContent = '';

        // Show success message
        successMessage.textContent = 'Successfully registered. Please login.';
        successMessage.style.display = 'block';

        // Redirect to the login page after a short delay
        setTimeout(function() {
            document.getElementById('register-container').style.display = 'none';
            document.getElementById('login-container').style.display = 'block';
        }, 2000);
    } else {
        errorMessage.textContent = 'Please fill in both fields.';
    }
});

register section*/

/*login section*/

document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');

    // Here you would normally send a request to a server to authenticate the user.
    // For demonstration purposes, I'll just check if the username and password are not empty.
    
});

/*login section*/
