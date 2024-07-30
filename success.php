<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK WEBSITE APPOINTMENT STATUS</title>
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9f7ef;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        h2 {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
            color: #555;
        }
        .details {
            text-align: left;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9fff9;
        }
        .details h3 {
            margin-top: 0;
            color: #28a745;
        }
        .details p {
            margin-bottom: 10px;
            color: #333;
        }
        a {
            text-decoration: none;
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        a:hover {
            background-color: #218838;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Appointment Booking Success</h2>
        <p>You have successfully booked your appointment. Thank you!</p>
        <div class="details">
            <h3>Appointment Details:</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($_GET['name']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($_GET['phone_number']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_GET['email']); ?></p>
            <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($_GET['appointment_date']); ?></p>
            <p><strong>Appointment Time:</strong> <?php echo htmlspecialchars($_GET['appointment_time']); ?></p>
            <p><strong>Doctor:</strong> <?php echo htmlspecialchars($_GET['doctor_id']); ?></p>
        </div>
        <a href="hospital-homepage.html">Go back to homepage</a>
    </div>
</body>
</html>
