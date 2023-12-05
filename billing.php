<?php
session_start(); // Start the session

$servername = 'localhost';
$dbname = 'ecom';
$username = 'root';
$password = '';

$connection = mysqli_connect($servername, $username, $password, $dbname);

if (!$connection) {
    die("Error: " . mysqli_error($connection));
}

// Check if stock is below 10 for any product
$stock_notification = '';
$query = "SELECT * FROM client WHERE stack < 10";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $stock_notification = 'Some products have low stock (below 10).';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["product_ids"])) {
        $product_ids = $_POST["product_ids"];
        $product_ids = explode(",", $product_ids);

        // Initialize selected products array
        $selected_products = [];

        echo '<h2>Selected Products:</h2>
        <form method="post">
        <table border="1">
        <tr><th>Product Name</th><th>Price</th><th>Quantity</th></tr>';

        foreach ($product_ids as $product_id) {
            $query = "SELECT * FROM client WHERE id = $product_id";
            $result = mysqli_query($connection, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $stack = $row['stack'];

                if ($stack >= 1) {
                    $product_name = $row['nameofproduct'];
                    $product_price = $row['price'];

                    echo "<tr><td>$product_name</td><td>$product_price</td>";
                    echo "<td><input type='number' name='quantity[$product_id]' value='1' min='1' max='$stack'></td></tr>";

                    // Add product to the selected_products array
                    $selected_products[] = [
                        'id' => $product_id,
                        'name' => $product_name,
                        'price' => $product_price,
                    ];
                } else {
                    echo "Insufficient stock for product with ID $product_id.<br>";
                }
            }
        }

        echo '</table>';
        
        // Display a confirmation button and a hidden field with selected product IDs
        echo '<input type="hidden" name="confirmed_products" value="' . htmlentities(json_encode($selected_products)) . '">';
        echo '<label for="customer_name">Customer Name:</label>';
        echo '<input type="text" name="customer_name" required><br>';
        echo '<label for "customer_mobile">Mobile Number:</label>';
        echo '<input type="text" name="customer_mobile" required><br>';
        echo '<button type="submit" name="confirm_purchase">Confirm Purchase</button>';
        echo '<button type="submit" name="refresh" style="float: right;">Refresh</button>';
        echo '</form>';
    } elseif (isset($_POST["confirmed_products"])) {
        $confirmed_products = json_decode($_POST["confirmed_products"], true);
        $success = true;
        $total_quantity = 0;
        $total_price = 0;

        echo '<h2>Bill Summary:</h2>
        <table border="1">
        <tr><th>Product Name</th><th>Quantity</th><th>Price</th></tr>';

        foreach ($confirmed_products as $product) {
            $product_id = $product['id'];
            $product_name = $product['name'];
            $product_price = $product['price'];
            $quantity = $_POST['quantity'][$product_id];

            echo "<tr><td>$product_name</td><td>$quantity</td><td>$product_price</td></tr>";

            // Update total quantity and total price
            $total_quantity += $quantity;
            $total_price += $product_price * $quantity;

            // Insert billing details into the 'billing' table
            $insert_query = "INSERT INTO billing (product_id, product_name, quantity, total_price) VALUES ($product_id, '$product_name', $quantity, " . ($product_price * $quantity) . ")";
            if (mysqli_query($connection, $insert_query)) {
                // Update stack value in the 'client' table
                $update_query = "UPDATE client SET stack = stack - $quantity WHERE id = $product_id";
                if (!mysqli_query($connection, $update_query)) {
                    $success = false;
                    echo "Error updating stack: " . mysqli_error($connection);
                    break;
                }
            }
        }

        echo '</table>';

        if ($success) {
            $customer_name = $_POST['customer_name'];
            $customer_mobile = $_POST['customer_mobile'];

            // Insert customer details into the 'customer' table
            $customer_insert_query = "INSERT INTO customer (name, phone) VALUES ('$customer_name', '$customer_mobile')";
            mysqli_query($connection, $customer_insert_query);

            // Reset the form if it exists
            echo '<script>var form = document.getElementById("product-selection-form"); if (form) form.reset();</script>';

            // Rest of your code to display the bill
            echo '<p>Customer Name: ' . $customer_name . '</p>';
            echo '<p>Mobile Number: ' . $customer_mobile . '</p>';
            echo '<p>Total Quantity: ' . $total_quantity . '</p>';
            echo '<p>Total Price: ' . $total_price . '</p>';
            echo 'Billing successful. Thank you for your purchase!';
            echo '<br><button onclick="window.print();">Print Total Bill</button>';
        } else {
            echo "Billing failed. Please check product availability or contact support.";
        }
    }

    // Handle the "Refresh" button click
    if (isset($_POST['refresh'])) {
        // Redirect to a fresh page
        header("Location: " . $_SERVER['PHP_SELF']);
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Supermarket Billing System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        h1 {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        .notification {
            display: none;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1;
        }

        .fixed-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .nav-buttons {
            display: flex;
        }

        .nav-buttons button {
            margin-right: 10px;
        }

        #bill-summary {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #print-button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #print-button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="fixed-navbar">
        <h1>Supermarket Billing System</h1>
        <div class="nav-buttons">
            <button type="button" onclick="window.location.href = 'login.html';">Logout</button>
            <button type="button" onclick="window.location.href = 'sam.php';">Stock</button>
        </div>
    </div>

    <div class="container">
        <form method="post" id="product-selection-form">
            <!-- Your product selection form -->
            <label for="customer_name">Customer Name:</label>
            <input type="text" name="customer_name"><br>
            <label for="customer_mobile">Mobile Number:</label>
            <input type="text" name="customer_mobile"><br>
            <label for="product_ids">Select Products (comma-separated IDs):</label>
            <input type="text" name="product_ids">
            <button type="submit" name="select_products">Select Products</button>
            <button type="submit" name="refresh">Refresh</button>
        </form>
        
        <!-- Notification container -->
        <div class="notification" id="notification"></div>

        <!-- Bill Summary -->
        <div id="bill-summary">
            <!-- Your PHP code will print the bill summary here -->
            <!-- Include the code for printing the bill summary here -->
        </div>
    </div>
    
    <script>
        // JavaScript function to show the notification
        function showNotification(message) {
            var notification = document.getElementById('notification');
            notification.innerText = message;
            notification.style.display = 'block';
            setTimeout(function() {
                notification.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        }

        // Check for the session variable and show the alert
        var notificationMessage = <?php
            echo json_encode($stock_notification);
        ?>;
        
        if (notificationMessage) {
            showNotification(notificationMessage);
        }
    </script>
</body>
</html>