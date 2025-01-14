<?php
include '../connection.php'; // Include your database connection

// Retrieve the posted JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data was received correctly
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

// Prepare and bind the update query
$updateQuery = "UPDATE borrow SET status = 'lost', Over_Due_Fines = ?, Book_Fines = ? WHERE walk_in_id = ? AND book_id = ? AND category = ?";

$stmt = $conn->prepare($updateQuery);

// Check if the statement preparation was successful
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn->error]);
    exit();
}

// Bind parameters (adjust data types accordingly)
$fines = $data['fines'];
$bookFines = $data['book_fines']; // Ensure you include this in the fetch data from JS
$walkinId = $data['walkin_id'];
$bookId = $data['book_id'];
$category = $data['category'];

// Change the bind_param types if necessary
$stmt->bind_param('ddsss', $fines, $bookFines, $walkinId, $bookId, $category); // 'ddsss' for two doubles and three strings

// Execute the update query
if ($stmt->execute()) {
    // After the update is successful, insert the record into the book_replacement table
    $insertQuery = "INSERT INTO book_replacement (book_id, category) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    
    if ($insertStmt) {
        // Bind the parameters for the insert statement
        $insertStmt->bind_param('ss', $bookId, $category); // 'ss' for two strings
        
        // Execute the insert query
        if ($insertStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Book status updated and replacement record added']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert into book_replacement: ' . $insertStmt->error]);
        }
        
        // Close the insert statement
        $insertStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare insert statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
