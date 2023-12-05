<?php
$servername = 'localhost';
$dbname = 'ecom';   
$username = 'root'; 
$password = '';
$connection = mysqli_connect($servername, $username, $password, $dbname);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phone'];
$query = "INSERT INTO users (name, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')";

if (mysqli_query($connection, $query)) {
    header("Location: login.html");
} else {
    echo "Error: " . mysqli_error($connection);
}
mysqli_close($connection);
?>
