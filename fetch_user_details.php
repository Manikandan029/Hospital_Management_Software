<?php
// Start session

include("db.php");
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters
/*$host = 'localhost';
$user = 'root';
$password = 'mani123';
$database = 'hospital_login';

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}*/

$username = $_SESSION['username'];

// Fetch user details from the database
$sql = "SELECT * FROM login WHERE username='$username'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
} else {
    // If user details not found, show a message
    $message = "Your details are not updated yet. Please wait and try again later.";
}

// Fetch treatments from the database
$treatments = array(); // Placeholder for treatments
// Example: $treatments = array("Medication A", "Medication B", "Lifestyle Changes", "Physical Therapy");

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MK WEBSITE</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" href="images/Logo.png" type="image/x-icon">
  <style>
    body {
      background-color: #f5f5f5; /* Light gray background */
      font-family: Arial, sans-serif;
    }
    
    .container {
      margin-top: 3rem;
      background-color: #fff;
      border-radius: 8px; /* Slightly smaller border radius */
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
      padding: 2rem;
    }
    
    h1 {
      color: #333;
      text-align: center;
      margin-bottom: 2rem;
    }
    
    h2 {
      color: #555;
      font-size: 1.8rem;
      margin-bottom: 1rem;
    }
    
    h3 {
      color: #666;
      font-size: 1.6rem;
      margin-bottom: 0.5rem;
    }
    
    p {
      color: #777;
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    
    th {
      background-color: #f2f2f2;
    }
    
    canvas {
      margin-top: 1rem;
      border-radius: 5px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Healthcare Dashboard</h1>
    
    <?php if(isset($message)): ?>
      <p><?php echo $message; ?></p>
    <?php else: ?>
      <h2>Patient Status</h2>
      <p>Patient Name: <?php echo $user['name']; ?></p>
      <p>Age: <?php echo $user['age']; ?></p>
      <p>Gender: <?php echo $user['gender']; ?></p>
      <p>Admission Date: <?php echo $user['admission_date']; ?></p>
      <p>Room: <?php echo $user['room']; ?></p>
      
      <h3>Heart Rate</h3>
      <p>Current Heart Rate: 75 bpm</p> <!-- Adjusted heart rate -->
      <canvas id="heart-rate-chart" aria-label="Heart Rate Chart"></canvas>
      
      <h3>Blood Pressure</h3>
      <p>Current BP: 115/70 mmHg</p> <!-- Adjusted blood pressure -->
      <canvas id="bp-chart" aria-label="Blood Pressure Chart"></canvas>
      
      <h3>Treatments</h3>
      <table>
        <tr>
          <th>Date</th>
          <th>Treatment</th>
          <th>Health Improvement (%)</th>
        </tr>
        <tr>
          <td>2024-06-01</td>
          <td>Medication A</td>
          <td>20%</td>
        </tr>
        <tr>
          <td>2024-06-02</td>
          <td>Medication B</td>
          <td>15%</td>
        </tr>
        <tr>
          <td>2024-06-03</td>
          <td>Lifestyle Changes</td>
          <td>30%</td>
        </tr>
        <tr>
          <td>2024-06-04</td>
          <td>Physical Therapy</td>
          <td>25%</td>
        </tr>
        <tr>
          <td>2024-06-05</td>
          <td>Medication C</td>
          <td>10%</td>
        </tr>
      </table>
      
      <h3>Disease Status</h3>
      <p>Diagnosis: Hypertension</p> <!-- Changed diagnosis -->
      <p>Treatment Plan: Medications, Lifestyle Changes</p> <!-- Adjusted treatment plan -->
      <p>Prognosis: Stable with Proper Management</p> <!-- Adjusted prognosis -->
    <?php endif; ?>
  </div>
  
  <script>
    // Sample heart rate data
    const heartRateData = [75, 78, 72, 80, 76, 79, 74]; // Adjusted heart rate data
    
    // Sample blood pressure data  
    const systolicData = [115, 118, 112, 120, 116, 119, 114]; // Adjusted systolic data
    const diastolicData = [70, 73, 67, 75, 71, 74, 69]; // Adjusted diastolic data
    
    // Create heart rate chart
    const heartRateChart = new Chart(document.getElementById('heart-rate-chart'), {
      type: 'line',
      data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [{
          label: 'Heart Rate',
          data: heartRateData,
          borderColor: '#e83e8c', /* Changed color to pink */
          backgroundColor: 'rgba(232, 62, 140, 0.2)', /* Changed color to pink */
          borderWidth: 2,
          fill: true
        }]
      }
    });
    
    // Create blood pressure chart  
    const bpChart = new Chart(document.getElementById('bp-chart'), {
      type: 'line',
      data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [
          {
            label: 'Systolic',
            data: systolicData,
            borderColor: '#007bff', /* Changed color to blue */
            backgroundColor: 'rgba(0, 123, 255, 0.2)', /* Changed color to blue */
            borderWidth: 2,
            fill: true
          },
          {
            label: 'Diastolic',
            data: diastolicData,
            borderColor: '#28a745', /* Changed color to green */
            backgroundColor: 'rgba(40, 167, 69, 0.2)', /* Changed color to green */
            borderWidth: 2,
            fill: true
          }
        ]
      }
    });
  </script>
</body>
</html>

