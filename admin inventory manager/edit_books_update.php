<?php
header('Content-Type: application/json');
include '../connection.php';
include '../connection2.php';

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

            // $bookDeductionSql = "UPDATE `$category` SET no_of_copies = no_of_copies - 1 WHERE id = ?";
            // $deductionStmt = $conn2->prepare($bookDeductionSql);


        } elseif (isset($data['archive'])) {
            // Update archive field
            $sql = "UPDATE accession_records SET archive = 'yes' WHERE accession_no = ? AND book_id = ? AND book_category = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sis", $accessionNo, $bookId, $category);

                // Execute the archive update statement
                if ($stmt->execute()) {
                    // Deduct book copy from category table
                    $bookDeductionSql = "UPDATE `$category` SET no_of_copies = no_of_copies - 1 WHERE id = ?";
                    $deductionStmt = $conn2->prepare($bookDeductionSql);
                    if ($deductionStmt) {
                        $deductionStmt->bind_param("i", $bookId);

                        // Execute the deduction statement
                        if (!$deductionStmt->execute()) {
                            error_log("Failed to execute book deduction: " . $deductionStmt->error);
                        }

                        $deductionStmt->close();
                    } else {
                        error_log("Failed to prepare deduction statement: " . $conn2->error);
                    }
                } else {
                    error_log("Failed to execute archive update: " . $stmt->error);
                }
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
    } else
    
    
    if (!empty($data['book_id']) && !empty($data['category']) && isset($data['archiveAll'])) {
        $bookId = $data['book_id'];
        $category = $data['category'];

        // Update all records to archive
        $sql = "UPDATE `$category` SET archive = 'yes' WHERE id = ? ";
        $stmt = $conn2->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $bookId);
        } else {
            error_log("Failed to prepare statement for archiveAll: " . $conn2->error);
        }

        if (isset($stmt) && $stmt->execute()) {

            // Update all records to archive in the accession_records table
            $sql = "UPDATE accession_records SET archive = 'yes' WHERE book_id = ? AND book_category = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("is", $bookId, $category);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'All records archived successfully.']);
                } else {
                    error_log("Execution error for archiveAll in accession_records: " . $stmt->error);
                    echo json_encode(['success' => false, 'message' => 'Failed to archive all records in accession_records.']);
                }
            } else {
                error_log("Failed to prepare statement for archiveAll in accession_records: " . $conn->error);
                echo json_encode(['success' => false, 'message' => 'Failed to prepare statement for archiveAll in accession_records.']);
            }
        } else {
            error_log("Execution error: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Failed to archive all records.']);
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
