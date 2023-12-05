<?php
$servername = 'localhost';
$dbname = 'ecom';
$username = 'root';
$password = '';
$connection = mysqli_connect($servername, $username, $password, $dbname);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['nameofproduct'])) {
    $names = $_POST['nameofproduct'];
    $prices = $_POST['price'];
    $stacks = $_POST['stack'];
    for ($i = 0; $i < count($names); $i++) {
        $nameofproduct = $names[$i];
        $price = $prices[$i];
        $stack = $stacks[$i];

        $query = "INSERT INTO client (nameofproduct, price, stack) VALUES ('$nameofproduct', '$price', '$stack')";

        if (mysqli_query($connection, $query)) {
            echo "<script>alert('Product added successfully');</script>";
        } else {
            echo "<script>alert('Failed to add product');</script>";
        }
    }
}

mysqli_close($connection);
?>
