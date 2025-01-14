<?php
// Database connection details for cPanel MySQL database
$servername = "gfi-library.gfi-edu.com"; // Replace with your MySQL host if needed
$username = "dnllaaww_ramoza"; // Your MySQL username
$password = "Ramoza@30214087695"; // Your MySQL password
$dbname = "dnllaaww_gfi_library"; // Your MySQL database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // Connection failed
    die("Connection failed: " . mysqli_connect_error());
} else {
    // Connection successful
    echo "Connected successfully to the database!";
}
?>
