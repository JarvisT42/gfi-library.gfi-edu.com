<?php
include '../connection.php'; // Ensure this defines $conn
include '../connection2.php'; // Ensure this defines $conn2 for the second database

file_put_contents('debug_log.txt', print_r(file_get_contents('php://input'), true), FILE_APPEND);

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $userId = htmlspecialchars($data['user_id']);
    $userType = htmlspecialchars($data['user_type']);
    $accessionNo = htmlspecialchars($data['accession_no']);
    $totalFee = htmlspecialchars($data['total_fee']);

    // Determine user column and binding type based on user type
    $userColumn = '';
    $bindType = ''; // Variable to determine the bind type for user_id
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

    // Query to update the borrow status to "returned"
    $updateBorrowQuery = "
        UPDATE borrow 
        SET status = 'replaced', Return_Date = NOW(), total_fines = ?
        WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'lost'";

    $stmtBorrow = $conn->prepare($updateBorrowQuery);

    // Bind the parameters based on the user type
    if ($bindType === 'i') {
        $stmtBorrow->bind_param('diis', $totalFee, $userId, $bookId, $category);
    } else {
        $stmtBorrow->bind_param('dsis', $totalFee, $userId, $bookId, $category); // 's' for string in $userId
    }

    // Execute the borrow table update
    if ($stmtBorrow->execute()) {
        // Prepare the query to update the accession_records table
        $updateAccessionQuery = "
            UPDATE accession_records 
            SET status = 'returned', status = 'subject-for-replacement' 
            WHERE accession_no = ? AND borrower_id = ? AND book_id = ? AND book_category = ? AND status = 'lost'";

        $stmtAccession = $conn->prepare($updateAccessionQuery);
        
        if ($stmtAccession === false) {
            echo json_encode(['success' => false, 'message' => 'Error preparing the accession update statement.']);
            $stmtBorrow->close();
            $conn->close();
            exit;
        }

        // Bind parameters for the accession records update
        $stmtAccession->bind_param('siis', $accessionNo, $userId, $bookId, $category);
        
        // Execute the accession records update
        if ($stmtAccession->execute()) {
            echo json_encode(['success' => true, 'message' => 'Book returned and accession record updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update the accession record: ' . $stmtAccession->error]);
        }

        // Close the accession statement
        $stmtAccession->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update the return status.']);
    }

    // Close the borrow statement and the database connection
    $stmtBorrow->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
