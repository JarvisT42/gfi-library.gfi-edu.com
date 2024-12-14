<?php
// Assuming you have a database connection file included
include '../connection.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if ID and status are set in the POST request
if (isset($data['id']) && isset($data['status'])) {
    $id = $data['id'];
    $status = $data['status'];

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE students SET status = ? WHERE Student_Id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update status']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
?>
