<?php
$status_message = "";

// Function to sanitize inputs
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Check if POST request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    if (isset($_POST['name']) && isset($_POST['id']) && isset($_POST['status'])) {
        $name = sanitizeInput($_POST['name']);
        $id = sanitizeInput($_POST['id']);
        $status = sanitizeInput($_POST['status']);

        if (empty($name) || empty($id) || empty($status)) {
            $status_message = "Invalid name, ID, or status";
        } else {
            include("db.php");
            /*$servername = "localhost";
            $username = "root"; // Change to your DB username
            $password = "mani123"; // Change to your DB password
            $dbname = "hospital_login"; // Change to your database name

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            */
            // Check current status
            $sql_check_status = "SELECT status FROM doctors WHERE id=? AND name=?";
            $stmt_check_status = $conn->prepare($sql_check_status);
            $stmt_check_status->bind_param("is", $id, $name);
            $stmt_check_status->execute();
            $stmt_check_status->store_result();

            if ($stmt_check_status->num_rows > 0) {
                $stmt_check_status->bind_result($current_status);
                $stmt_check_status->fetch();

                if ($current_status === $status) {
                    $status_message = "Already " . ucfirst($status);
                } else {
                    // Update status
                    $sql_update_status = "UPDATE doctors SET status=? WHERE id=? AND name=?";
                    $stmt_update_status = $conn->prepare($sql_update_status);
                    $stmt_update_status->bind_param("sis", $status, $id, $name);

                    if ($stmt_update_status->execute()) {
                        $status_message = ucfirst($status) . " successful";
                    } else {
                        $status_message = "Error updating status: " . $stmt_update_status->error;
                    }
                }
            } else {
                $status_message = "Doctor not found";
            }

            $stmt_check_status->close();
            $conn->close();
        }
    } else {
        $status_message = "Invalid data received";
    }

    // Output the status message as JSON response
    echo json_encode(['message' => $status_message]);
    exit; // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK HOSPITAL DOCTORS LOGIN PORTAL</title>
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('images/online.jpg');
            background-repeat: no-repeat;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        #container {
            text-align: center;
            width: 100%;
            max-width: 600px;
            padding: 20p;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        #qr-canvas {
            display: none;
           
        }
        .status-message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        h1 {
            margin: 0;
            position: absolute;
            top: 20px;
            width: 100%;
            text-align: center;
            color: black;
        }
        .button-container {
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: rgb(88, 226, 175);
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #65e6b3;
        }
        .popup-message {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: rgb(88, 226, 175);
            color: white;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: none;
        }
        .popup-message.show {
            display: block;
        }
        #file-input-container {
            margin-top: 20px;
            border: 2px dashed #ddd;
            padding: 20px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            margin: 20px;
        }
        #file-input-container.dragover {
            border-color: #007bff;
        }
        #file-input-label {
            color: #007bff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Welcome to Doctor's Login</h1>
    <div id="container">
        <div id="status-container">
            <?php if (!empty($status_message)): ?>
                <div class="status-message <?php echo (strpos($status_message, 'Error') !== false) ? 'error' : 'success'; ?>">
                    <?php echo $status_message; ?>
                </div>
            <?php endif; ?>

            <div id="qr-scan-box" style="margin-bottom: 20px;">
                <div id="file-input-container">
                    <input type="file" accept="image/*" id="file-input" style="display:none;">
                    <label id="file-input-label" for="file-input">Choose QR Code or Drag and Drop</label>
                </div>
                <canvas id="qr-canvas" width="300" height="300"></canvas>
            </div>

            <div class="button-container">
                <button class="button" id="login-btn">Login</button>
                <button class="button" id="logout-btn">Logout</button>
            </div>
        </div>
    </div>

    <div id="popup-message" class="popup-message"></div>

    <script src="https://rawgit.com/cozmo/jsQR/master/dist/jsQR.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const fileInput = document.getElementById('file-input');
            const fileInputContainer = document.getElementById('file-input-container');
            const canvas = document.getElementById('qr-canvas');
            const canvasCtx = canvas.getContext('2d');
            const qrScanBox = document.getElementById('qr-scan-box');
            const loginBtn = document.getElementById('login-btn');
            const logoutBtn = document.getElementById('logout-btn');
            const popupMessage = document.getElementById('popup-message');

            function showPopupMessage(message) {
                popupMessage.textContent = message;
                popupMessage.classList.add('show');
                setTimeout(() => {
                    popupMessage.classList.remove('show');
                }, 3000);
            }

            // Show initial popup message
            showPopupMessage('Please choose to login or logout.');

            loginBtn.addEventListener('click', function() {
                showPopupMessage('Please choose the QR code to log in.');
                qrScanBox.style.display = 'block';
                scanAndChangeStatus('online');
            });

            logoutBtn.addEventListener('click', function() {
                showPopupMessage('Please choose the QR code to log out.');
                qrScanBox.style.display = 'block';
                scanAndChangeStatus('offline');
            });

            fileInput.addEventListener('change', handleFile);

            fileInputContainer.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileInputContainer.classList.add('dragover');
            });

            fileInputContainer.addEventListener('dragleave', function() {
                fileInputContainer.classList.remove('dragover');
            });

            fileInputContainer.addEventListener('drop', function(e) {
                e.preventDefault();
                fileInputContainer.classList.remove('dragover');
                const file = e.dataTransfer.files[0];
                handleFile({ target: { files: [file] } });
            });

            function handleFile(e) {
                const file = e.target.files[0];
                if (!file) {
                    displayStatusMessage("No file chosen", "error");
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    const image = new Image();
                    image.onload = function() {
                        canvas.width = image.width;
                        canvas.height = image.height;
                        canvasCtx.drawImage(image, 0, 0, canvas.width, canvas.height);
                        const imageData = canvasCtx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });

                        if (code) {
                            const data = code.data;
                            const { name, id } = getNameAndIdFromQRData(data);
                            
                            if (name && id) {
                                // Send data to server only on login/logout button click
                                // Do not send automatically upon QR scan
                                // Handle status change after button click
                            } else {
                                displayStatusMessage("Name or ID missing from QR code", "error");
                            }
                        } else {
                            displayStatusMessage("No QR Code found", "error");
                        }
                    };
                    image.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }

            function scanAndChangeStatus(status) {
                const canvas = document.getElementById('qr-canvas');
                const canvasCtx = canvas.getContext('2d');
                const fileInput = document.getElementById('file-input');
                const reader = new FileReader();

                fileInput.onchange = function() {
                    reader.readAsDataURL(fileInput.files[0]);
                };

                reader.onload = function() {
                    const img = new Image();
                    img.onload = function() {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        canvasCtx.drawImage(img, 0, 0, img.width, img.height);
                        const imageData = canvasCtx.getImageData(0, 0, img.width, img.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: 'dontInvert',
                        });

                        if (code) {
                            const qrData = code.data;
                            const { name, id } = getNameAndIdFromQRData(qrData);
                            
                            if (name && id) {
                                updateStatus(name, id, status);
                            } else {
                                displayStatusMessage('Name or ID missing from QR code', 'error');
                            }
                        } else {
                            displayStatusMessage('No QR code detected', 'error');
                        }
                    };

                    img.src = reader.result;
                };
            }

            function updateStatus(name, id, status) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            displayStatusMessage(response.message, 'success');
                            showPopupMessage(response.message);
                        } else {
                            displayStatusMessage('Error updating status: ' + xhr.statusText, 'error');
                            showPopupMessage('Error updating status');
                        }
                    }
                };

                xhr.send('name=' + encodeURIComponent(name) + '&id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(status));
            }

            function getNameAndIdFromQRData(data) {
                const pairs = data.split(',');
                let name = null;
                let id = null;

                for (let i = 0; i < pairs.length; i++) {
                    const pair = pairs[i].trim().split(':');
                    if (pair.length === 2) {
                        const key = pair[0].trim().toLowerCase();
                        const value = pair[1].trim();
                        if (key === 'name') {
                            name = value;
                        } else if (key === 'id') {
                            id = value;
                        }
                    }
                }

                return { name, id };
            }

            function displayStatusMessage(message, type) {
                const statusContainer = document.getElementById('status-container');
                const statusMessageElement = document.createElement('div');
                statusMessageElement.classList.add('status-message', type);
                statusMessageElement.textContent = message;
                statusContainer.appendChild(statusMessageElement);
            }
        });
    </script>
</body>
</html>
