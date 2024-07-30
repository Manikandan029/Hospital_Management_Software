<?php
include("db.php");

// Initialize session variable if not set
if (!isset($_SESSION['invoice'])) {
    $_SESSION['invoice'] = [];
}

// Handle search
$search = "";
$medicines = [];

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT id, name, quantity, stock, price FROM medicines WHERE name LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
    }
}

// Handle form submission for adding to invoice
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_invoice"])) {
    $medicine_id = $_POST["medicine_id"];
    $quantity = $_POST["quantity"];

    // Fetch medicine details based on ID
    $sql = "SELECT name, quantity, price FROM medicines WHERE id = $medicine_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $medicine = $result->fetch_assoc();
        $medicine['quantity'] = $quantity; // Override quantity with form input
        $_SESSION['invoice'][] = $medicine; // Add medicine to invoice session array

        // Redirect to avoid form resubmission on page refresh
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}

// Handle removal of item from invoice
if (isset($_GET['remove']) && isset($_SESSION['invoice'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['invoice'][$index])) {
        unset($_SESSION['invoice'][$index]);
        $_SESSION['invoice'] = array_values($_SESSION['invoice']); // Reset array keys
    }

    // Redirect to avoid resubmission on refresh
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Calculate subtotal
$subtotal = 0;
foreach ($_SESSION['invoice'] as $item) {
    $subtotal += ($item['quantity'] * $item['price']);
}

// Calculate GST (assuming 18% GST rate)
$gst_rate = 0.18;
$gst_amount = $subtotal * $gst_rate;

// Apply discount (assuming 10% discount)
$discount_rate = 0.10;
$discount_amount = $subtotal * $discount_rate;

// Calculate total after discount and GST
$total = $subtotal - $discount_amount + $gst_amount;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Billing System</title>
    <link rel="icon" href="images/Logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            background-image: url('images/billing-back.avif');
            background-repeat: no-repeat;
            background-size: cover; /* Ensure the background image covers the entire viewport */
            background-position: center center; /* Center the background image */
            height: 100%;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative; /* Added for positioning patient info and date/time */
            background-color: rgba(255, 255, 255, 0.8); /* Background with transparency */
        }

        h2, h3 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            overflow-x: auto; /* Enable horizontal scrolling */
        }

        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table tr:hover {
            background-color: #20b99b;
        }

        .total-section {
            margin-top: 20px;
            padding: 10px;
        }

        .total-section label {
            font-weight: bold;
            color: #000;
        }

        .total-section span {
            margin-left: 5px;
            color: #333;
        }

        .conditions {
            margin-top: 20px;
            padding: 10px;
        }

        .conditions h3 {
            color: #333;
        }

        .conditions p {
            color: #555;
        }

        .search-form {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8); /* Transparent background */
        }

        .search-form label {
            font-weight: bold;
            color: #333;
            margin-right: 10px;
        }

        .search-form input[type="text"] {
            padding: 5px 10px;
            width: 200px;
            border: 1px solid #ccc;
            font-size: 14px;
            border-radius: 10px;
        }

        .search-form button {
            padding: 5px 10px;
            border-radius: 10px;
            background-color: #20b99b;
            border: none;
            color: #fff;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #0073a8;
        }

        .add-to-invoice-form input[type="number"] {
            width: 50px;
            padding: 4px;
            border: 1px solid #000;
            font-size: 14px;
            border-radius: 10px;
        }

        .add-to-invoice-form button {
            padding: 5px 10px;
            border-radius: 10px;
            background-color: #20b99b;
            border: none;
            color: #fff;
            cursor: pointer;
        }

        .add-to-invoice-form button:hover {
            background-color: #0073a8;
        }

        .invoice-table {
            margin-top: 20px;
            border: 1px solid;
            overflow-x: auto; /* Enable horizontal scrolling */
        }

        .invoice-table th, .invoice-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .invoice-table a {
            color: #f44336;
            text-decoration: none;
            cursor: pointer;
        }

        .invoice-table a:hover {
            text-decoration: underline;
        }

        #print-button {
            margin-top: 10px;
            text-align: center;
        }

        #print-button button {
            padding: 10px 20px;
            background-color: #0073a8;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #print-button button:hover {
            background-color: #005c87;
        }

        /* Position patient info and date/time */
        .patient-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .patient-info label {
            font-weight: bold;
            color: #333;
            margin-right: 10px;
        }

        .patient-info input[type="text"],
        .patient-info input[type="number"] {
            padding: 5px 10px;
            width: 200px;
            border: 1px solid #ccc;
            font-size: 14px;
            border-radius: 10px;
        }

        .datetime {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 14px;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 120px; /* Adjust size as needed */
            height: auto; /* Maintain aspect ratio */
            margin-right: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); /* Add shadow effect */
        }

        @media print {
            body {
                background: none;
                -webkit-print-color-adjust: exact; /* Fix for Chrome */
            }

            .container {
                box-shadow: none;
                background-color: #fff; /* Print background color */
                padding: 0;
                margin: 0;
            }

            .datetime {
                display: none; /* Hide date/time in print */
            }

            .header {
                justify-content: center; /* Center align header in print */
            }

            #print-button {
                display: none; /* Hide print button in print */
            }

            .conditions {
                page-break-before: always; /* Ensure conditions start on a new page */
            }

            .invoice-table a {
                display: none; /* Hide remove links in print */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Logo Image -->
            <img class="logo" src="images/Logo.png" alt="Logo">

            <!-- Date and Time -->
            <div class="datetime">
                <?php echo date('l, F jS Y \- g:i A'); ?>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="patient-info">
            <div>
                <label>Patient Name:</label>
                <input type="text" name="patient_name">
            </div>
            <div>
                <label>Age:</label>
                <input type="number" name="patient_age">
            </div>
        </div>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label>Search Medicine:</label>
            <input type="text" name="search" value="<?php echo $search; ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Medicine List -->
        <?php if (!empty($medicines)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicines as $medicine): ?>
                        <tr>
                            <td><?php echo $medicine['name']; ?></td>
                            <td><?php echo $medicine['quantity']; ?></td>
                            <td><?php echo $medicine['stock']; ?></td>
                            <td><?php echo $medicine['price']; ?></td>
                            <td>
                                <form class="add-to-invoice-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="medicine_id" value="<?php echo $medicine['id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $medicine['stock']; ?>">
                                    <button type="submit" name="add_to_invoice">Add to Invoice</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Invoice Details -->
        <div class="invoice-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['invoice'] as $index => $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo $item['price']; ?></td>
                            <td>
                                <a href="?remove=<?php echo $index; ?>">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="total-section">
            <label>Subtotal:</label>
            <span><?php echo '$' . number_format($subtotal, 2); ?></span>
            <br>
            <label>GST (18%):</label>
            <span><?php echo '$' . number_format($gst_amount, 2); ?></span>
            <br>
            <label>Discount (10%):</label>
            <span><?php echo '$' . number_format($discount_amount, 2); ?></span>
            <br>
            <label>Total After Discount and GST:</label>
            <span><?php echo '$' . number_format($total, 2); ?></span>
        </div>

        <!-- Conditions/Instructions -->
        <div class="conditions">
            <h3>Conditions</h3>
            <p>Please pay your bills on time to avoid any inconvenience.</p>
        </div>

        <!-- Print Button -->
        <div id="print-button">
            <button onclick="window.print()">Print Invoice</button>
        </div>
    </div>

    <!-- JavaScript for confirmation -->
    <script>
        function confirmRemoval() {
            return confirm('Are you sure you want to remove this item from the invoice?');
        }
    </script>
</body>
</html>
