<?php
header('Content-Type: application/json');
include '../connection.php';

// Enable error logging to error.txt
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.txt');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data['accession_no']) && !empty($data['book_id']) && !empty($data['category'])) {
        $accessionNo = $data['accession_no'];
        $bookId = $data['book_id'];
        $category = $data['category'];
        $borrowable = isset($data['borrowable']) ? $data['borrowable'] : null;

        if (isset($data['borrowable'])) {
            // Update borrowable field
            $sql = "UPDATE accession_records SET editable = ? WHERE accession_no = ? AND book_id = ? AND book_category = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssis", $borrowable, $accessionNo, $bookId, $category);
            } else {
                error_log("Failed to prepare statement for borrowable: " . $conn->error);
            }
        } elseif (isset($data['archive'])) {
            // Update archive field
            $sql = "UPDATE accession_records SET archive = 'yes' WHERE accession_no = ? AND book_id = ? AND book_category = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sis", $accessionNo, $bookId, $category);
            } else {
                error_log("Failed to prepare statement for archive: " . $conn->error);
            }
        }

        if (isset($stmt) && $stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Record updated successfully.']);
        } else {
            error_log("Execution error: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Failed to update the record.']);
        }

        if (isset($stmt)) {
            $stmt->close();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
