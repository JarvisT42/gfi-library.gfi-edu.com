<?php
include '../connection.php';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate data
if (isset($data['accession_no'], $data['available'], $data['book_id'], $data['category'])) {
    $accession_no = $data['accession_no'];
    $available = $data['available'];
    $book_id = $data['book_id'];
    $category = $data['category'];

    // Update the database
    $update_sql = "UPDATE accession_records SET borrower_id = null, status = null, available = 'yes', borrowable = ? WHERE accession_no = ? AND book_id = ? AND book_category = ?";
    $update_stmt = $conn->prepare($update_sql);

    if ($update_stmt) {
        $update_stmt->bind_param("ssis", $available, $accession_no, $book_id, $category);
        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made.']);
        }

        $update_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
}

$conn->close();
?>
