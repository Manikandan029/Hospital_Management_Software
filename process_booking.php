<?php
include("db.php");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $doctor_id = $_POST['doctor_id'];

    // Check if any field is empty
    if (empty($name) || empty($phone_number) || empty($email) || empty($appointment_date) || empty($appointment_time) || empty($doctor_id)) {
        echo "All fields are required.";
        exit();
    }

    // Calculate appointment start and end times with a buffer of 1 hour
    $appointment_start_time = date('H:i:s', strtotime('-1 hour', strtotime($appointment_time)));
    $appointment_end_time = date('H:i:s', strtotime('+1 hour', strtotime($appointment_time)));

    // SQL query to check if there are any appointments for the selected doctor within the specified time range
    $check_timing_sql = "SELECT COUNT(*) as appointment_count FROM appointments WHERE doctor_id = '$doctor_id' 
                         AND appointment_date = '$appointment_date' 
                         AND appointment_time >= '$appointment_start_time' 
                         AND appointment_time <= '$appointment_end_time'";
    $timing_result = mysqli_query($conn, $check_timing_sql);

    if (!$timing_result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $timing_row = mysqli_fetch_assoc($timing_result);

    if ($timing_row['appointment_count'] > 0) {
        // Redirect to failure page indicating the selected time slot is not available
        header("Location: failure.php?message=Selected time slot is not available. Please choose another time.");
        exit();
    }

    // Continue with the insertion process if the time slot is available
    // SQL query to check the number of appointments for the specified doctor and date
    $check_sql = "SELECT COUNT(*) as appointment_count FROM appointments WHERE doctor_id = '$doctor_id' AND appointment_date = '$appointment_date'";
    $result = mysqli_query($conn, $check_sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);

    if ($row['appointment_count'] >= 5) {
        // Redirect to failure page indicating the maximum number of appointments has been reached for the selected date
        header("Location: failure.php?message=Maximum appointments reached for the selected date.");
        exit();
    } else {
        // SQL query to insert data into appointments table
        $sql = "INSERT INTO appointments (doctor_id, name, phone_number, email, appointment_date, appointment_time) 
                VALUES ('$doctor_id', '$name', '$phone_number', '$email', '$appointment_date', '$appointment_time')";

        if (mysqli_query($conn, $sql)) {
            // Redirect to success page with user details as URL parameters
            header("Location: success.php?name=" . urlencode($name) . "&phone_number=" . urlencode($phone_number) . "&email=" . urlencode($email) . "&appointment_date=" . urlencode($appointment_date) . "&appointment_time=" . urlencode($appointment_time) . "&doctor_id=" . urlencode($doctor_id));
            exit(); // Make sure to exit after sending the header to prevent further execution
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Close connection
mysqli_close($conn);
?>
!