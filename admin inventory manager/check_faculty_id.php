<?php
// Include database connection
include '../connection.php';

// Check if the faculty_id is passed
if (isset($_GET['faculty_id'])) {
    $faculty_id = $_GET['faculty_id'];

    // Sanitize the faculty_id input
    $faculty_id = $conn->real_escape_string($faculty_id);

    // Create SQL query to check if faculty ID exists
    $query = "SELECT faculty_id FROM faculty_ids WHERE faculty_id = '$faculty_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // If faculty ID exists, return 'exists'
        echo 'exists';
    } else {
        // If faculty ID does not exist, return 'available'
        echo 'available';
    }

    // Close the database connection
    $conn->close();
} else {
    echo 'error';  // Return error if faculty_id is not provided
}
?>
