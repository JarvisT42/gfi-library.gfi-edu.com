<?php
// Database connection
include '../connection.php';

// Decode JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Update the agree_of_term column
$agree_of_term = $data['agree_of_terms'];
$student_id = 1; // Replace with dynamic student ID

$sql = "UPDATE students SET agree_of_terms = '$agree_of_term' WHERE Student_Id = $student_id";
if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
