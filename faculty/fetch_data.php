


<?php
// fetch_data.php

// Database connection
require '../connection.php'; // Emits a fatal error if database.php is missing, stopping script execution.

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Query to get the data for the dropdown (e.g., actions)
$result = $mysqli->query("SELECT id, action FROM dnllaaww_gfi_library_books_inventory");

$actions = [];
while ($row = $result->fetch_assoc()) {
    $actions[] = $row;
}

// Output the data as JSON
echo json_encode($actions);
?>
