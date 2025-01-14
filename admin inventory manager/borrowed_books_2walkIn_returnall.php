<?php
session_start();
include '../connection.php';
include '../connection2.php';

// Get data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Log the incoming data for debugging
error_log("Received data: " . print_r($data, true));

if (!empty($data['books']) && (isset($_GET['student_id']) || isset($_GET['faculty_id']) || isset($_GET['walk_in_id']))) {
    $books = $data['books'];
    $success = true;

    // Determine user type and ID based on URL parameter
    if (isset($_GET['student_id'])) {
        $userId = $_GET['student_id'];
        $userColumn = 'student_id';
        $userRole = 'student';
    } elseif (isset($_GET['faculty_id'])) {
        $userId = $_GET['faculty_id'];
        $userColumn = 'faculty_id';
        $userRole = 'faculty';
    } elseif (isset($_GET['walk_in_id'])) {
        $userId = $_GET['walk_in_id'];
        $userColumn = 'walk_in_id';
        $userRole = 'walk-in';
    } else {
        echo json_encode(['success' => false, 'message' => 'User ID not specified.']);
        exit;
    }

    // Prepare main update for the borrow table with expected_replacement_date included
    $updateQuery = "UPDATE borrow SET status = 'lost', Total_Fines = ?, expected_replacement_date = ? WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'borrowed'";
    $stmt = $conn->prepare($updateQuery);

    // Prepare update for accession_records based on user role
    $updateAccessionQuery = "UPDATE accession_records SET status = 'lost', available = 'no' WHERE accession_no = ? AND " . ($userRole === 'walk-in' ? 'walk_in_id' : 'user_id') . " = ? AND status = 'borrowed'";
    $stmtAccession = $conn->prepare($updateAccessionQuery);

    foreach ($books as $book) {
        $total_fines = $book['total_fines'];
        $book_id = $book['book_id'];
        $category = $book['category'];
        $accession_no = $book['accession_no'];
        $expected_replacement_date = $book['expected_replacement_date'];

        // Check if each item has the required data
        if (!isset($total_fines, $expected_replacement_date, $book_id, $category, $accession_no)) {
            error_log("Missing book data: " . print_r($book, true));
            $success = false;
            break;
        }

        // Bind parameters and execute the query for the borrow table
        if (!$stmt->bind_param('dsssi', $total_fines, $expected_replacement_date, $userId, $book_id, $category) || !$stmt->execute()) {
            error_log("Borrow Table Update Failed: " . $stmt->error);  // Log error for debugging
            $success = false;
            break;
        }

        // Bind parameters and execute the query for accession_records
        if (!$stmtAccession->bind_param('si', $accession_no, $userId) || !$stmtAccession->execute()) {
            error_log("Accession Table Update Failed: " . $stmtAccession->error);  // Log error for debugging
            $success = false;
            break;
        }
    }

    $stmt->close();
    $stmtAccession->close();

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to process all books.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
}
?>
