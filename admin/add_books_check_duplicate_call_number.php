<?php
require '../connection2.php';

$callNumber = isset($_GET['call_number']) ? $_GET['call_number'] : '';

// Validate input to prevent SQL injection
if (empty($callNumber)) {
    echo json_encode(['error' => 'Call Number is required']);
    exit;
}

// Check if the Call Number exists in any of the tables (except "e-books")
$sql = "SHOW TABLES";
$result = $conn2->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Error fetching tables: ' . $conn2->error]);
    $conn2->close();
    exit;
}

$data = ['exists' => false];

while ($row = $result->fetch_row()) {
    $tableName = $row[0];

    if ($tableName === "e-books") {
        continue;
    }

    // Prepare SQL query to check if Call Number exists
    $sql = "SELECT COUNT(*) FROM `$tableName` WHERE Call_Number = ?";
    $stmt = $conn2->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn2->error]);
        $conn2->close();
        exit;
    }

    $stmt->bind_param('s', $callNumber);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    if ($count > 0) {
        $data['exists'] = true; // Found a duplicate
        break; // No need to check other tables
    }

    $stmt->close();
}

echo json_encode($data); // Return the result
$conn2->close();
?>
