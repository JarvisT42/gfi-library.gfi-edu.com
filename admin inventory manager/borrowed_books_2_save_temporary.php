<?php
// Database connection
include '../connection.php';

// Log the POST data for debugging
file_put_contents('debug_log.txt', print_r(file_get_contents('php://input'), true), FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if essential fields are provided
    if (!isset($data['book_id'], $data['category'], $data['user_type'], $data['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
        exit;
    }

    // Retrieve data from the JSON request
    $bookId = htmlspecialchars($data['book_id']);
    $category = htmlspecialchars($data['category']);
    $fineAmount = isset($data['fineAmount']) ? (float)$data['fineAmount'] : 0;
    $userId = htmlspecialchars($data['user_id']);
    $userType = htmlspecialchars($data['user_type']);
    // Determine the user column based on user type
    $userColumn = '';
    if ($userType === 'student') {
        $userColumn = 'student_id';
    } elseif ($userType === 'faculty') {
        $userColumn = 'faculty_id';
    } elseif ($userType === 'walk_in') {
        $userColumn = 'walk_in_id';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user type.']);
        exit;
    }

    // Prepare the SQL query to update the borrow table, setting Return_Date to the current time
    $updateQuery = "UPDATE borrow 
                    SET  Total_Fines = ?, Return_Date = NOW()
                    WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'borrowed'";
    $stmt = $conn->prepare($updateQuery);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the SQL statement.']);
        exit;
    }

    // Bind parameters and execute the query
    $stmt->bind_param('diss', $fineAmount, $userId, $bookId, $category);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book returned successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating book return: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
