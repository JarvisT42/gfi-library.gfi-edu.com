<?php
require '../connection2.php';

header('Content-Type: application/json');

$call_number = $_GET['call_number'] ?? '';

if (empty($call_number)) {
    echo json_encode(["isDuplicate" => false]);
    exit;
}

$isDuplicate = false;
$queryTables = "SHOW TABLES";
$resultTables = $conn2->query($queryTables);

while ($row = $resultTables->fetch_row()) {
    $table = $row[0];
    if ($table === 'e-books') {
        continue;
    }

    $sql = "SELECT COUNT(*) as count FROM `$table` WHERE Call_Number = ?";
    $stmt = $conn2->prepare($sql);
    $stmt->bind_param("s", $call_number);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $isDuplicate = true;
        $stmt->close();
        break;
    }
    $stmt->close();
}

echo json_encode(["isDuplicate" => $isDuplicate]);
$conn2->close();
?>
