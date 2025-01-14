<?php
include '../connection.php';

// Read the raw POST data
$inputData = json_decode(file_get_contents("php://input"), true);

// Check if required parameters are provided
if (isset($inputData['accession_no'], $inputData['available'], $inputData['repair_description'], $inputData['book_id'], $inputData['category'])) {
    $accession_no = $inputData['accession_no'];
    $available = $inputData['available'];
    $repair_description = $inputData['repair_description'];
    $book_id = $inputData['book_id'];
    $category = $inputData['category'];

    // Update the record in the database

    $update_sql = "UPDATE accession_records SET repaired = 'yes', book_condition = ?, available = 'yes', borrowable = ? WHERE accession_no = ? AND book_id = ? AND book_category = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("sssis", $repair_description, $available, $accession_no, $book_id, $category);
        
        if ($stmt->execute()) {
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Book status updated successfully.']);
        } else {
            // Return error response
            echo json_encode(['success' => false, 'message' => 'Failed to update book status.']);
        }

        $stmt->close();
    } else {
        // Return error response if query preparation fails
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL query.']);
    }
} else {
    // Return error response if required data is missing
    echo json_encode(['success' => false, 'message' => 'Missing required data.']);
}
?>
