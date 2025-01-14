<?php
// Include database connection
include '../connection.php';

// Check if the student_id is passed
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Sanitize the student_id input
    $student_id = $conn->real_escape_string($student_id);

    // Create SQL query to check if student ID exists
    $query = "SELECT student_id FROM students_ids WHERE student_id = '$student_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // If student ID exists, return 'exists'
        echo 'exists';
    } else {
        // If student ID does not exist, return 'available'
        echo 'available';
    }

    // Close the database connection
    $conn->close();
} else {
    echo 'error';  // Return error if student_id is not provided
}
?>
