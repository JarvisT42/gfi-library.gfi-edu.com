<?php
session_start();

include '../connection.php'; // Ensure this defines $conn
include '../connection2.php'; // Ensure this defines $conn2 for the second database

// Log request data for debugging
file_put_contents('debug_log.txt', print_r(file_get_contents('php://input'), true), FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Decode the JSON request body
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if essential fields are provided
        if (!isset($data['book_id'], $data['category'], $data['user_type'], $data['user_id'], $data['accession_no'])) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
            exit;
        }

        // Sanitize input data
        $bookId = htmlspecialchars($data['book_id']);
        $category = htmlspecialchars($data['category']);
        $fineAmount = isset($data['fine_amount']) ? (float)$data['fine_amount'] : 0;
        $userId = htmlspecialchars($data['user_id']);
        $userType = htmlspecialchars($data['user_type']);
        $accessionNo = htmlspecialchars($data['accession_no']);

        // Determine the user column and binding type based on user type
        $userColumn = '';
        $bindType = '';

        if ($userType === 'student') {
            $userColumn = 'student_id';
            $bindType = 'i'; // integer
        } elseif ($userType === 'faculty') {
            $userColumn = 'faculty_id';
            $bindType = 'i'; // integer
        } elseif ($userType === 'walk_in') {
            $userColumn = 'walk_in_id';
            $bindType = 's'; // string
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid user type.']);
            exit;
        }

        $adminId = $_SESSION["admin_id"]; // Get the admin ID from the session


        // Prepare the query to update the borrow table
        $updateBorrowQuery = "
            UPDATE borrow
            SET status = 'lost-pay', Total_Fines = ?, admin_id = ? 
            WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'borrowed'
        ";

        $stmtBorrow = $conn->prepare($updateBorrowQuery);
        if ($stmtBorrow === false) {
            throw new Exception('Error preparing the borrow update statement: ' . $conn->error);
        }

        // Bind parameters for the borrow update based on user type
        if ($bindType === 'i') {
            $stmtBorrow->bind_param('diiis', $fineAmount,  $adminId, $userId, $bookId, $category);
        } else {
            $stmtBorrow->bind_param('dissi', $fineAmount,  $adminId,  $userId, $bookId, $category);
        }

        // Prepare the query to update the accession table
        $updateAccessionQuery = "
            UPDATE accession_records
            SET status = 'subject-for-replacement', available = 'no'
            WHERE accession_no = ? AND borrower_id = ? AND book_id = ? AND status = 'borrowed'
        ";

        $stmtAccession = $conn->prepare($updateAccessionQuery);
        if ($stmtAccession === false) {
            throw new Exception('Error preparing the accession update statement: ' . $conn->error);
        }

        // Bind parameters and execute the accession update
        $stmtAccession->bind_param('sii', $accessionNo, $userId, $bookId);

        // Execute both statements and check success
        if ($stmtBorrow->execute() && $stmtAccession->execute()) {
            echo json_encode(['success' => true, 'message' => 'Replacement processed successfully.']);
        } else {
            throw new Exception('Error executing the replacement update: ' . $stmtBorrow->error . ' / ' . $stmtAccession->error);
        }

        // Close statements
        $stmtBorrow->close();
        $stmtAccession->close();

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage() . ' on line ' . $e->getLine()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
