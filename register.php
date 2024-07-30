<?php
// Database connection parameters
include("db.php");


// Signup form handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newUsername']) && isset($_POST['newPassword'])) {
    $newUsername = $_POST['newUsername'];
    $newPassword = $_POST['newPassword'];

    $newUsername = mysqli_real_escape_string($conn, $newUsername);
    $newPassword = mysqli_real_escape_string($conn, $newPassword);

    // Validate password
    $uppercase = preg_match('@[A-Z]@', $newPassword);
    $specialChars = preg_match('@[^\w]@', $newPassword);

    if(strlen($newPassword) < 8 || !$uppercase || !$specialChars) {
        $registerError = "Password must be at least 8 characters long and contain at least one uppercase letter and one special character.";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$newUsername', '$newPassword')";
    
        if (mysqli_query($conn, $sql)) {
            $registerError = "User registered successfully";
        } else {
            $registerError = "Error: " . $sql . "<br>" . mysqli_error($conn);
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
        background-image: url('images/Mobile\ login-rafiki.svg');
        background-repeat: no-repeat;
        background-position: 15%;
    }

    .flex {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 400px; /* Adjusted width */
        margin: 0 auto;
        box-shadow: 0px 0px 10px 0px #20b99b;
        margin-left: 800px;
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
        box-shadow:  0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        backdrop-filter: blur(50px);
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
    }
    .form-group p{
        margin-left: 80px;
    }
    .form-group a{
        text-decoration: none;
        margin-left: 155px;
        font-weight: 600;
    }
    .form-group a:hover{
        color: #20b99b;
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
        position: relative; /* Added */
    }

    .form-group .toggle-password {
        position: absolute;
        margin-top: 20px;
        margin-left: -30px;
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
        margin-top: 15px;
    }

    .form-group button:hover {
        background-color: #16a085;
    }

    .error-message {
        color: red;
        margin-top: 10px;
    }

    .link2 a {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        text-decoration: none;
        color: #20b99b;
        background-color: #fff;
        font-size: 16px;
        font-weight: bold;
        align-self: center;
        margin-top: auto;
        text-align: center;
    }

    .link2 a:hover {
        background-color: #333333;
        color: #f1f1f1;
    }
</style>

</head>
<body>
    <div class="flex">
        <div class="container">
            <h2>Signup</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="newUsername">Username:</label>
                    <input type="text" id="newUsername" name="newUsername" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">Password:</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="newPassword" name="newPassword" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit">Signup</button>
                </div>
                <div id="registerErrorMessage" class="error-message">
                    <?php
                    if (isset($registerError)) {
                        echo $registerError;
                    }
                    ?>
                </div>
                <div class="form-group">
                    <p>If you already have an account, <span style="font-size: 20px;margin-left:80px;">👇</span></p>
                    <a href="login.php">Login</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('newPassword');
            var toggleIcon = document.getElementById('toggleIcon');

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

