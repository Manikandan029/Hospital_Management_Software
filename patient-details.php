<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK HOSPITAL PATIENT DETAILS</title>
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
    <style>
    body {
        margin-left: 200px;
        margin-top: 50px;
        max-width: 1000px;
        background-image: url('images/patient-background.jpg');
        overflow: hidden;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        height: 100vh; /* Ensures the background covers the entire viewport height */
        overflow: hidden;
        
    }
    .body {
        max-width: 800px;

    }
    .search .search-box {
        margin-left: 250px;
        border-radius: 3em;
        padding: 5px 20px;
        font-size: 1rem;
        box-shadow: 2px 2px 5px 1px #20b99b;
        background-color: rgb(142, 210, 189);
    }
    .search button {
        margin-left:px;
        padding: 5px 20px;
        text-shadow: 3ch;
        border-radius: 10px;
        border-width: 2px;
        background-color: rgb(142, 210, 189);
    }
    .search button:hover {
        color: white;
        background-color: rgb(5, 107, 76);
    }
    h2 {
        text-align: center;
    }
    .patient-details {
        padding: 10px;
    }
    #patientForm {
        border: 1px solid;
        padding: 40px;
        margin: 20px;
        max-width: 300px;
        border-radius: 10px;
        background-color: aquamarine;
        position: absolute;
        right: -500px; /* Adjusted initial hidden position */
        transition: right 0.5s ease-in-out;
        z-index: 2; /* Added z-index to make it visible on top */
    }
    #patientForm.show {
        right: 100px; /* Adjusted visible position */
    }
    #patientForm input {
        margin-left: 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        padding: 5px;
    }
    #patientForm button {
        border-radius: 10px;
        border-width: 2px;
        background-color: rgb(88, 226, 175);
        padding: 5px 20px;
        margin-left: 100px;
    }
    #patientForm #name {
        margin-left: 36px;
    }
    #patientForm #age {
        margin-left: 46.5px;
    }
    #output {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 15px;
        color: black;
    }
    .patient-card {
        border: 1px solid;
        border-radius: 10px;
        padding: 15px;
        position: relative;
        background-color: #9FE2BF; /* background color for odd cards */
    }
    .patient-card.even {
        background-color: #40E0D0; /* darker shade for even cards */
    }
    .patient-card:hover {
        background-color: #188f80; /* hover effect for odd cards */
        cursor: pointer;
    }
    .patient-card.even:hover {
        background-color: #157466; /* hover effect for even cards */
    }
    .options {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }
    .options-menu {
        display: none;
        position: absolute;
        top: 25px;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        z-index: 1;
    }
    .options-menu button {
        display: block;
        padding: 5px 10px;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
    }
    .options-menu button:hover {
        background-color: #f0f0f0;
    }
</style>
</head>
<body>
    <div class="body">
        <h2>Patient's Details</h2>
        <div class="search">
            <input type="search" id="search-box" class="search-box" placeholder="Search">
            <button onclick="searchPatient()">Search</button>
            <button onclick="showForm()">Add</button>
        </div>
        <div class="patient-details">
            <form id="patientForm" action="" method="POST" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="patientId" name="patientId">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="name" required><br>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" class="age" required><br>
                <label for="phone">Phone No:</label>
                <input type="tel" id="phone" name="phone" class="phone" required><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root"; // Change to your MySQL username
    $password = "mani123"; // Change to your MySQL password
    $dbname = "hospital_login";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST["patientId"];
        $name = $_POST["name"];
        $age = $_POST["age"];
        $phone = $_POST["phone"];

        if ($id) {
            // Update existing patient record
            $sql = "UPDATE patients SET name='$name', age='$age', phone='$phone' WHERE id=$id";
        } else {
            // Insert new patient record
            $sql = "INSERT INTO patients (name, age, phone) VALUES ('$name', '$age', '$phone')";
        }

        $conn->query($sql);

        // Redirect to the same page to prevent form resubmission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    // Handle deletion
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $sql = "DELETE FROM patients WHERE id=$id";
        $conn->query($sql);

        // Redirect to the same page to prevent form resubmission
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }

    // Fetch and display patients' details
    $search = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $sql = "SELECT id, name, age, phone FROM patients WHERE name LIKE '%$search%' OR phone LIKE '%$search%' OR age LIKE '%$search%'";
    } else {
        $sql = "SELECT id, name, age, phone FROM patients";
    }
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div id='output'>";
        $counter = 0;
        while($row = $result->fetch_assoc()) {
            $class = ($counter % 2 == 0) ? "patient-card even" : "patient-card";
            echo "<div class='$class'>";
            echo "<h3>Patient " . ($counter + 1) . " Details:</h3>";
            echo "<p>Name: " . $row["name"] . "</p>";
            echo "<p>Age: " . $row["age"] . "</p>";
            echo "<p>Phone No: " . $row["phone"] . "</p>";
            echo "<div class='options' onclick='toggleOptions(" . $row["id"] . ")'>⋮</div>";
            echo "<div class='options-menu' id='options-menu-" . $row["id"] . "'>";
            echo "<button onclick='editPatient(" . $row["id"] . ", \"" . $row["name"] . "\", " . $row["age"] . ", \"" . $row["phone"] . "\")'>Edit</button>";
            echo "<button onclick='removePatient(" . $row["id"] . ")'>Remove</button>";
            echo "</div>";
            echo "</div>";
            $counter++;
        }
        echo "</div>";
    } else {
        echo "<p>No patient records found</p>";
    }

    $conn->close();
    ?>
    <script>
        function showForm() {
            var form = document.getElementById('patientForm');
            form.classList.toggle('show');
        }

        function toggleOptions(id) {
            var menu = document.getElementById('options-menu-' + id);
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        function editPatient(id, name, age, phone) {
            document.getElementById('patientId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('age').value = age;
            document.getElementById('phone').value = phone;
            showForm();
        }

        function removePatient(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = "?delete=" + id;
            }
        }

        function handleSubmit(event) {
            event.preventDefault(); // Prevent the default form submission
            var form = document.getElementById('patientForm');
            var formData = new FormData(form);

            fetch('', {
                method: 'POST',
                body: new URLSearchParams(formData).toString(),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => {
                if (response.ok) {
                    // If the response is successful, reset the form and reload the page
                    form.reset();
                    location.reload();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
            });
        }

        function searchPatient() {
            var searchQuery = document.getElementById('search-box').value;
            window.location.href = "?search=" + searchQuery;
        }
    </script>
</body>
</html>

