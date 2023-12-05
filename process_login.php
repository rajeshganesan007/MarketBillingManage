<?php
$servername = 'localhost';
$dbname = 'ecom';
$username = 'root';
$password = '';
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = mysqli_real_escape_string($connection, $_POST['email']); // Use mysqli_real_escape_string to prevent SQL injection
$password = mysqli_real_escape_string($connection, $_POST['password']); // Use mysqli_real_escape_string to prevent SQL injection

$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

$check = mysqli_fetch_array($result);

if ($check) {
    header("Location: sam.php");
} else {
    echo 'failure';
}

mysqli_close($connection);
?>
