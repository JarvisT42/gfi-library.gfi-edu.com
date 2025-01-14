<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accession_no'])) {
    $accession_no = trim($_POST['accession_no']);

    $sql = "SELECT accession_no FROM accession_records WHERE accession_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $accession_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
    $conn->close();
}
?>
