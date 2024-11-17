<?php
include '../connection.php'; // Ensure this defines $conn
include '../connection2.php'; // Ensure this defines $conn2 for the second database

// Log request data for debugging
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
    $fineAmount = isset($data['fine_amount']) ? (float)$data['fine_amount'] : 0;
    $userId = htmlspecialchars($data['user_id']);
    $userType = htmlspecialchars($data['user_type']);
    $damageDescription = htmlspecialchars($data['damage_description'] ?? '');
    $accessionNo = htmlspecialchars($data['accession_no']);

    // Determine the user column and bind type based on user type
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

    // Prepare the query to update the borrow table
    $updateBorrowQuery = "
        UPDATE borrow 
        SET status = 'returned', Total_Fines = ?, Return_Date = NOW(), Damage_Description = ? 
        WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'borrowed'";

    $stmtBorrow = $conn->prepare($updateBorrowQuery);
    if ($stmtBorrow === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the borrow update statement.']);
        exit;
    }

    // Bind parameters for the borrow update based on user type
    if ($bindType === 'i') {
        $stmtBorrow->bind_param('dsisi', $fineAmount, $damageDescription, $userId, $bookId, $category);
    } else {
        $stmtBorrow->bind_param('dsssi', $fineAmount, $damageDescription, $userId, $bookId, $category);
    }

    // Execute the borrow update
    if (!$stmtBorrow->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error updating book return: ' . $stmtBorrow->error]);
        $stmtBorrow->close();
        $conn->close();
        exit;
    }


    


    // Prepare the query to update the accession_records table
    $updateAccessionQuery = "
        UPDATE accession_records 
        SET status = 'returned', damage_description = ?, damage = 'yes', available = 'no', repaired = 'no'
        WHERE accession_no = ? AND borrower_id = ? AND book_id = ? AND status = 'borrowed'";

    $stmtAccession = $conn->prepare($updateAccessionQuery);
    if ($stmtAccession === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the accession update statement.']);
        $stmtBorrow->close();
        $conn->close();
        exit;
    }

    // Bind parameters for the accession update
    $stmtAccession->bind_param('ssii', $damageDescription, $accessionNo, $userId, $bookId);
    
    // Execute the accession update and check success
    if ($stmtAccession->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book returned successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating accession records: ' . $stmtAccession->error]);
    }

    // Close statements and connection
    $stmtBorrow->close();
    $stmtAccession->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
