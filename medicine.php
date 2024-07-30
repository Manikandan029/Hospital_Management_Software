<?php
include("db.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $quantity = $_POST["quantity"];
    $stock = $_POST["stock"];
    $price = $_POST["price"];

    // Check if the medicine with the same name and quantity already exists
    $sql = "SELECT id, stock FROM medicines WHERE name='$name' AND quantity='$quantity'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Medicine exists with the same name and quantity, update the stock
        $row = $result->fetch_assoc();
        $new_stock = $row["stock"] + $stock;
        $id = $row["id"];
        $sql = "UPDATE medicines SET stock='$new_stock', price='$price' WHERE id=$id";
    } else {
        // Insert new medicine record
        $sql = "INSERT INTO medicines (name, quantity, stock, price) VALUES ('$name', '$quantity', '$stock', '$price')";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Record updated successfully";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM medicines WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Record deleted successfully";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Fetch and display medicines' details
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT id, name, quantity, stock, price FROM medicines WHERE name LIKE '%$search%' OR quantity LIKE '%$search%' OR stock LIKE '%$search%'";
} else {
    $sql = "SELECT id, name, quantity, stock, price FROM medicines";
}
$result = $conn->query($sql);

$medicines = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK HOSPITAL MEDICINES</title>
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
    <style>
        body {
            background-image: url('images/background.avif');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }
        h1 {
            text-align: center;
        }

        .div {
            margin: 0 auto;
            max-width: 1000px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            padding-left: 40px;
        }

        .medicine-container {
            border: 1px solid;
            max-width: 150px;
            padding: 20px;
            border-radius: 10px;
            object-fit: cover;
            background-color: rgb(216, 245, 225);
            color: black;
            margin: 25px -10px 25px 0px;
            position: relative;
        }

        .medicine-container img {
            max-width: 150px;
            max-height: 1000px;
        }

        .header {
            margin: 0 auto;
            max-width: 1000px;
            position: relative;
        }

        .header .search {
            padding: 7px 150px 7px 10px;
            margin: 20px 0px 20px 250px;
            border-radius: 10px;
            box-shadow: 2px 2px 5px 1px #20b99b;
            background-color: rgb(142, 210, 189);
        }

        .header button {
            padding: 5px 10px;
            border-radius: 10px;
            background-color: rgb(142, 210, 189);
        }

        .header button:hover {
            background-color: #20b99b;
            cursor: pointer;
        }

        .form {
            border-radius: 10px;
            border: 1px solid;
            max-width: 400px;
            max-height: 400px;
            margin: 10px;
            padding: 20px;
            background-color: rgb(216, 245, 225);
            border-width: 5px;
            border-color: lightseagreen;
            position: absolute;
            top: 120px; /* Adjust this value to control vertical positioning */
            right: -450px; /* Start off the screen to the right */
            transition: right 0.5s ease-in-out;
            display: none; /* Hide the form initially */
        }

        h2 {
            margin-left: 90px;
        }

        .form input {
            margin: 8px 0px;
            padding: 5px 80px 5px 10px;
            border-radius: 10px;
            font-size: 1em;
            box-shadow: 2px 2px 5px 1px #20b99b;
        }

        .form #details-input1 {
            margin-left: 17px;
        }

        .form #details-input3 {
            margin-left: 20px;
        }

        .form #details-input4 {
            margin-left: 23px;
        }

        .form button {
            padding: 5px 10px;
            border-radius: 10px;
            background-color: rgb(142, 210, 189);
            font-size: 1em;
            margin: 15px 0px 0px 160px;
        }

        .form button:hover {
            background-color: #20b99b;
            cursor: pointer;
        }

        .options {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 24px;
                        color: #555;
        }

        .options:hover {
            color:#000;
        }

        .options-menu {
            position: absolute;
            top: 30px;
            right: 10px;
            display: none;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1;
        }

        .options-menu button {
            display: block;
            width: 100%;
            padding: 5px 10px;
            border: none;
            background: none;
            cursor: pointer;
        }

        .options-menu button:hover {
            background-color: #f0f0f0;
        }

        #notification {
            display: none;
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #ffdddd;
            color: #a94442;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
        }

        #notification.success {
            background-color: #dff0d8;
            color: #3c763d;
            border-color: #d6e9c6;
        }
    </style>
</head>
<body>
    <div id="notification"></div>
    <div class="header">
        <h1>Medicine details</h1><br>
        <input type="search" class="search" placeholder="search">
        <button>search</button>
        <button id="addButton">Add</button>
    </div>

    <div class="div">
        <?php if (!empty($medicines)) : ?>
            <?php foreach ($medicines as $medicine) : ?>
                <div class="medicine-container">
                    <?php
                        // Determine the image based on the quantity unit
                        if (stripos($medicine['quantity'], 'g') !== false || stripos($medicine['quantity'], 'kg') !== false) {
                            $image = 'images/pills.avif';
                        } elseif (stripos($medicine['quantity'], 'l') !== false || stripos($medicine['quantity'], 'ml') !== false) {
                            $image = 'images/bottle.jpg';
                        } else {
                            $image = 'images/default.jpg'; // Default image
                        }
                    ?>
                    <img src="<?php echo $image; ?>" alt="">
                    <p>Name: <?php echo $medicine['name']; ?></p>
                    <p>Quantity: <?php echo $medicine['quantity']; ?></p>
                    <p>Stock: <?php echo $medicine['stock']; ?></p>
                    <p>Price: <?php echo $medicine['price']; ?></p>
                    <div class="options" onclick="toggleOptions(<?php echo $medicine['id']; ?>)">⋮</div>
                    <div class="options-menu" id="options-menu-<?php echo $medicine['id']; ?>">
                        <button onclick="removeMedicine(<?php echo $medicine['id']; ?>)">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No medicine records found</p>
        <?php endif; ?>
    </div>

    <div class="form" id="form">
        <h2>Enter the details</h2>
        <form method="POST" action="">
            <label for="name" class="details-label">Name:</label>
            <input type="text" id="details-input1" name="name" required><br>
            <label for="quantity" class="details-label">Quantity:</label>
            <input type="text" id="details-input" name="quantity" required><br>
            <label for="stock" class="details-label">Stock:</label>
            <input type="text" id="details-input3" name="stock" required><br>
            <label for="price" class="details-label">Price:</label>
            <input type="text" id="details-input4" name="price" required><br>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        document.getElementById('addButton').addEventListener('click', function () {
            var form = document.getElementById('form');
            form.style.display = 'block';
            setTimeout(function () {
                form.style.right = '0';
            }, 10);
        });

        function toggleOptions(id) {
            var optionsMenu = document.getElementById('options-menu-' + id);
            if (optionsMenu.style.display === 'block') {
                optionsMenu.style.display = 'none';
            } else {
                optionsMenu.style.display = 'block';
            }
        }

        function removeMedicine(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = "?delete=" + id;
            }
        }

        // Add event listener for the search button
        document.querySelector('.header button:first-of-type').addEventListener('click', function() {
            var searchValue = document.querySelector('.search').value;
            if (searchValue) {
                window.location.href = "?search=" + searchValue;
            }
        });

        // Allow pressing 'Enter' key to trigger search
        document.querySelector('.search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('.header button:first-of-type').click();
            }
        });

        // Show notification if there is a message or error in session
        <?php if (isset($_SESSION['message'])): ?>
            showNotification("<?php echo $_SESSION['message']; ?>", 'success');
            <?php unset($_SESSION['message']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            showNotification("<?php echo $_SESSION['error']; ?>", 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        function showNotification(message, type) {
            var notification = document.getElementById('notification');
            notification.textContent = message;
            if (type === 'success') {
                notification.classList.add('success');
            } else {
                notification.classList.remove('success');
            }
            notification.style.display = 'block';
            setTimeout(function () {
                notification.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>

