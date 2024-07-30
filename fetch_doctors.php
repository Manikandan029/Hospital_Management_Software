<?php
include("db.php");

// Fetch doctor data
$sql = "SELECT id, status FROM doctors";
$result = $conn->query($sql);

$doctors = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

$conn->close();

echo json_encode($doctors);
?>
