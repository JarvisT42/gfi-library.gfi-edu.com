<?php
include '../connection.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$Student_Id = $input['Student_Id'] ?? null;
$agreeOfTerms = $input['agree_of_terms'] ?? null;

if ($Student_Id && $agreeOfTerms === 'yes') {
    $sql = "UPDATE students SET agree_of_terms = ? WHERE Student_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $agreeOfTerms, $Student_Id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
?>
