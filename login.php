<?php
// Start session
include("db.php");

// Login form handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Check if the user is "meera" and the password matches
    if ($username == 'Meera' && $password == 'Meera@123') {
        // Set session variables
        $_SESSION['username'] = $username;
        
        // Redirect to the specific page for Meera
        header("Location: patient-details.php");
        exit();
    }

    elseif ($username == 'Admin' && $password == 'Admin@123') {
        // Set session variables
        $_SESSION['username'] = $username;
        
        // Redirect to the specific page for Meera
        header("Location: medicine.php");
        exit();
    }

    
    elseif ($username == 'Billing' && $password == 'Billing@123') {
        // Set session variables
        $_SESSION['username'] = $username;
        
        // Redirect to the specific page for Meera
        header("Location: billing.php");
        exit();
    }
    elseif($username == 'Doctor' && $password == 'Doctor@123'){
        $_SESSION['username'] = $username;
        
        // Redirect to the specific page for Meera
        header("Location: doctor.php");
        exit();
    }

    // For other users, check the username and password in the database
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['username'] = $username;
        header("Location: fetch_user_details.php");
        exit();
    } else {
        $error = "Invalid username or password";
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
            <h2>Login</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required >
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required> <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fa fa-eye" id="toggleIcon"></i>
            
                </div>
                <div class="form-group-forgot">
                    <a href="forgot_password.php" style="text-decoration:none;margin-left:200px; ">forgot_password?</a>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
                <div class="error-message">
                    <?php
                    if (isset($error)) {
                        echo $error;
                    }
                    ?>
                </div>
                <div class="form-group">
                    <p>If you don't have an account<span style="font-size: 20px; margin-left:80px;">👇</span></p>
                    <a href="register.php">Sign Up</a>
                </div>
            </form>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('.flex').style.opacity = '1'; // Make the form visible
            });
    
            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('password');
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
    </div>
</body>
</html>

