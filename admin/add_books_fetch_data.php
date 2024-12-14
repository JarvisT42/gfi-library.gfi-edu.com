<?php
require '../connection2.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

// Validate field input to prevent SQL injection
$sql = "SHOW TABLES";
$result = $conn2->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Error fetching tables: ' . $conn2->error]);
    $conn2->close();
    exit;
}

$data = [];
$excludedTable = "e-books";

// Loop through each table and fetch data
while ($row = $result->fetch_row()) {
    $tableName = $row[0];

    if ($tableName === $excludedTable) {
        continue;
    }

    // Fetch Title, CallNumber, and Author from the table
    $sql = "SELECT Title, Call_Number, Author FROM `$tableName` WHERE Title LIKE ? OR Call_Number LIKE ? OR Author LIKE ?";
    $stmt = $conn2->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn2->error]);
        $conn2->close();
        exit;
    }

    $searchParam = $query . '%';
    $stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $tableResult = $stmt->get_result();

    while ($row = $tableResult->fetch_assoc()) {
        $data[] = [
            'Title' => $row['Title'] ?? '',
            'CallNumber' => $row['Call_Number'] ?? '',
            'Author' => $row['Author'] ?? '' // Ensure this matches your column name
        ];
    }

    $stmt->close();
}

echo json_encode($data); // Output the array of suggestions with Title, CallNumber, and Author
$conn2->close();
?>
