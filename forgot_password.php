<?php
include("db.php");

$message = '';
$error = '';

// Check if the username is already in session
if (!isset($_SESSION['username'])) {
    // If not, check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
        $username = $_POST['username'];
        $username = mysqli_real_escape_string($conn, $username);

        // Check if the username exists
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
            $message = 'Username found. Please enter your new password.';
        } else {
            $error = 'Username not found. Please try again.';
        }
    }
} else {
    // If the username is in session, handle the new password form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        $username = $_SESSION['username'];

        // Password validation
        $uppercase = preg_match('@[A-Z]@', $newPassword);
        $lowercase = preg_match('@[a-z]@', $newPassword);
        $number    = preg_match('@[0-9]@', $newPassword);
        $specialChars = preg_match('@[^\w]@', $newPassword);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($newPassword) < 8) {
            $error = 'Password should be at least 8 characters long, contain one capital letter, one number, and one special character.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Passwords do not match. Please try again.';
        } else {
            $newPassword = mysqli_real_escape_string($conn, $newPassword);

            // Update the password in the database
            $sql = "UPDATE users SET password='$newPassword' WHERE username='$username'";
            if (mysqli_query($conn, $sql)) {
                $message = 'Password updated successfully!';
                // Clear the session
                session_unset();
                session_destroy();
            } else {
                $error = 'Error updating password: ' . mysqli_error($conn);
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK WEBSITE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="path/to/font-awesome/css/all.min.css">
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-image: url('images/Forgot\ password-bro.svg');
        background-repeat: no-repeat;
        background-position: 15%;
    }

    .flex {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 350px;
        margin: 0 auto;
        box-shadow: -3px 0px 10px 0px #20b99b;
        margin-left: 850px;
        border-radius: 10px;
        animation: slideIn 0.5s forwards; /* Animation added */
        opacity: 0; /* Initially hidden */
    }

    @keyframes slideIn {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .container {
        width: 100%;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 350px;
        backdrop-filter: blur(50px);
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
        position: relative; /* Added for relative positioning */
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #555;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .form-group .password-input-wrapper {
        position: relative;
    }

    .form-group .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px; /* Adjusted for proper alignment */
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
    }

    .form-group button {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: #20b99b;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #16a085;
    }

    .error-message {
        color: red;
        margin-top: 10px;
    }

    .success-message {
        color: green;
        margin-top: 10px;
    }

    .link {
        margin-top: 15px;
        text-align: center;
    }

    .link a {
        text-decoration: none;
        font-weight: 700;
    }

    .link a:hover {
        color: #20b99b;
        border: 1px;
        border-radius: 0%;
    }
</style>

</head>

<body>
    <div class="flex">
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if (!isset($_SESSION['username'])): ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <button type="submit">Submit</button>
                </div>
                <div class="error-message">
                    <?php if (!empty($error)) { echo $error; } ?>
                </div>
                <div class="success-message">
                    <?php if (!empty($message)) { echo $message; } ?>
                </div>
            </form>
        <?php else: ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="newPassword">New
                    Password:</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="newPassword" name="newPassword" required>
                        <span class="toggle-password toggle-password1" onclick="togglePasswordVisibility('newPassword')">
                            <i class="fa fa-eye" id="toggleIcon1"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                        <span class="toggle-password toggle-password2" onclick="togglePasswordVisibility('confirmPassword')">
                            <i class="fa fa-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit">Update Password</button>
                </div>
                <div class="error-message">
                    <?php if (!empty($error)) { echo $error; } ?>
                </div>
                <div class="success-message">
                    <?php if (!empty($message)) { echo $message; } ?>
                </div>
            </form>
        <?php endif; ?>
        <div class="link">
            <p>Remembered your password? </p>
            <a href="login.php">Login</a>
        </div>
    </div>
</div>
<script>
    function togglePasswordVisibility(inputId) {
        var passwordInput = document.getElementById(inputId);
        var toggleIconId = inputId === 'newPassword' ? 'toggleIcon1' : 'toggleIcon2';
        var toggleIcon = document.getElementById(toggleIconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>

