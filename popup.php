<?php
session_start();

$servername = 'localhost';
$dbname = 'ecom';
$username = 'root';
$password = '';

$connection = mysqli_connect($servername, $username, $password, $dbname);

if (!$connection) {
    die("Error: " . mysqli_error($connection));
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["product_ids"])) {
    $product_ids = explode(",", $_GET["product_ids"]);
    echo '<h2>Select Products:</h2>
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
            }
        }
    }
    
    echo '</table>';
    echo '<button type="submit" name="confirm_purchase">Confirm Purchase</button>';
}
