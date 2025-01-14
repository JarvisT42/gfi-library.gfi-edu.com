<?php
include '../connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);
$renewStatus = 'yes';

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

// Check for student_id, faculty_id, or walk_in_id and prepare the appropriate query
if (isset($data['student_id']) && !empty($data['student_id'])) {
    $updateQuery = "UPDATE borrow SET Due_Date = ?, renew = ? WHERE student_id = ? AND book_id = ? AND category = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sssss', $data['due_date'], $renewStatus, $data['student_id'], $data['book_id'], $data['category']);
    
} elseif (isset($data['faculty_id']) && !empty($data['faculty_id'])) {
    $updateQuery = "UPDATE borrow SET Due_Date = ?, renew = ? WHERE faculty_id = ? AND book_id = ? AND category = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sssss', $data['due_date'], $renewStatus, $data['faculty_id'], $data['book_id'], $data['category']);


    
} elseif (isset($data['walk_in_id']) && !empty($data['walk_in_id'])) {
    $updateQuery = "UPDATE borrow SET Due_Date = ?, renew = ? WHERE walk_in_id = ? AND book_id = ? AND category = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sssss', $data['due_date'], $renewStatus, $data['walk_in_id'], $data['book_id'], $data['category']);
} else {
   $updateQuery = "UPDATE borrow SET Due_Date = ?, renew = ? WHERE walk_in_id = ? AND book_id = ? AND category = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sssss', $data['due_date'], $renewStatus, $data['walk_in_id'], $data['book_id'], $data['category']);
    
}

// Execute the statement and return success or failure
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Due date updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
