<?php
include '../connection.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$Faculty_Id = $input['Faculty_Id'] ?? null;
$agreeOfTerms = $input['agree_of_terms'] ?? null;

if ($Faculty_Id && $agreeOfTerms === 'yes') {
    $sql = "UPDATE faculty SET agree_of_terms = ? WHERE Faculty_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $agreeOfTerms, $Faculty_Id);
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
