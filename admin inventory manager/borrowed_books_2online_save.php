<?php
include '../connection.php'; // Include your first database connection
include '../connection2.php'; // Include your second database connection

// Retrieve the posted JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data was received correctly
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

// Get the current date
$returnedDate = date('Y-m-d');

// Prepare and bind the update query, including Damage_Description and Returned_Date
$updateQuery = "UPDATE borrow SET status = 'returned', Over_Due_Fines = ?, Book_Fines = ?, Damage_Description = ?, Return_Date = ? WHERE student_id = ? AND book_id = ? AND category = ?";

$stmt = $conn->prepare($updateQuery);

// Check if the statement preparation was successful
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn->error]);
    exit();
}

// Bind parameters (adjust data types accordingly)
$fines = $data['fines'];
$bookFines = $data['book_fines']; // Ensure you include this in the fetch data from JS
$damageDescription = $data['damage_description']; // Add damage description
$studentId = $data['student_id'];
$bookId = $data['book_id'];
$category = $data['category'];

// Change the bind_param types if necessary, 's' for string, 'd' for double, 'i' for integer
$stmt->bind_param('ddsssss', $fines, $bookFines, $damageDescription, $returnedDate, $studentId, $bookId, $category); // 'ddsssss' for two doubles and five strings

// Execute the first query
if ($stmt->execute()) {

    // Prepare the second query to update No_Of_Copies
    $bookAdditionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies + 1, status = 'damage' WHERE id = ?";

    $bookStmt = $conn2->prepare($bookAdditionSql);

    // Check if the statement preparation was successful for the second query
    if (!$bookStmt) {
        echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn2->error]);
        exit();
    }

    // Bind the book ID for the second query, assuming 'id' is an integer
    $bookStmt->bind_param('i', $bookId); // Use 'i' for integer, adjust if 'id' is a different type

    // Execute the second query
    if ($bookStmt->execute()) {
        // If both queries are successful
        echo json_encode(['success' => true]);
    } else {
        // If the second query fails
        echo json_encode(['success' => false, 'message' => 'Failed to update book copies: ' . $bookStmt->error]);
    }

    // Close the second statement
    $bookStmt->close();

} else {
    // If the first query fails
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
}

// Close both statements and connections
$stmt->close();
$conn->close();
$conn2->close();
?>
