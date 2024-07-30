<?php
// Database connection parameters
include("db.php");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $doctor_id = $_POST['doctor_id'];

    // Validate if all fields are filled
    if (empty($name) || empty($phone_number) || empty($email) || empty($appointment_date) || empty($appointment_time) || empty($doctor_id)) {
        echo "All fields are required.";
        exit();
    }

    // SQL query to check if the booking exists and matches the provided details
    $check_sql = "SELECT * FROM appointments WHERE name = '$name' AND phone_number = '$phone_number' AND email = '$email' 
                  AND appointment_date = '$appointment_date' AND appointment_time = '$appointment_time' AND doctor_id = '$doctor_id'";
    $result = mysqli_query($conn, $check_sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        // Booking found, proceed with cancellation
        $delete_sql = "DELETE FROM appointments WHERE name = '$name' AND phone_number = '$phone_number' AND email = '$email' 
                       AND appointment_date = '$appointment_date' AND appointment_time = '$appointment_time' AND doctor_id = '$doctor_id'";
        if (mysqli_query($conn, $delete_sql)) {
            // Redirect to success page with cancellation message
            header("Location: success.php?message=Booking canceled successfully");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        // Booking not found or details do not match
        echo "No booking found matching the provided details.";
    }
}

// Close connection
mysqli_close($conn);
?>
