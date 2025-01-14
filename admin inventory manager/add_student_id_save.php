<?php
// Include the database connection file
include '../connection.php'; // Replace with your actual connection file

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the input data is valid
if (!$data || !is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit;
}

// Prepare an array to store the SQL placeholders and the values
$placeholders = [];
$values = [];

// Loop through the student IDs dynamically
for ($i = 1; $i <= 10; $i++) {
    $key = "student{$i}";
    if (!empty($data[$key])) {
        $placeholders[] = '(?)';
        $values[] = $data[$key];
    }
}

// Check if there are any values to insert
if (count($values) > 0) {
    // Create the SQL query dynamically with placeholders
    $sql = "INSERT INTO students_id (student_id) VALUES " . implode(', ', $placeholders);

    if ($stmt = $conn->prepare($sql)) {
        // Bind the values dynamically
        $types = str_repeat('s', count($values)); // 's' for each string (student_id)
        $stmt->bind_param($types, ...$values);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database insertion failed: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare SQL statement: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'No valid student IDs provided']);
}
?>
