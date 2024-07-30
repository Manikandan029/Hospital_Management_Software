<?php
// Example connection parameters
include("db.php");

// Fetch doctors data from database
$sql = "SELECT name, status FROM doctors";
$result = $conn->query($sql);

$doctorsData = array();

if ($result->num_rows > 0) {
    // Fetch data and store in $doctorsData array
    while ($row = $result->fetch_assoc()) {
        // Determine availability based on status
        $availability = ($row['status'] == 'online') ? 'Available' : 'Not Available';
        $doctorsData[] = [
            'name' => $row['name'],
            'availability' => $availability
        ];
    }
}

// Close connection
$conn->close();

// Output doctors availability as JSON
header('Content-Type: application/json');
echo json_encode($doctorsData);
?>
