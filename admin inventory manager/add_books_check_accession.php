<?php
require '../connection.php';

header('Content-Type: application/json');

$term = $_GET['term'] ?? '';
$accession_no = $_GET['accession_no'] ?? '';

try {
    if (!empty($term)) {
        // Autocomplete logic
        $results = [];
        $query = "SELECT DISTINCT accession_no FROM accession_records WHERE accession_no LIKE ? LIMIT 5";
        $stmt = $conn->prepare($query);
        $searchTerm = "%$term%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $results[] = $row['accession_no'];
        }

        echo json_encode($results);
    } elseif (!empty($accession_no)) {
        // Duplicate validation logic
        $query = "SELECT COUNT(*) as count FROM accession_records WHERE accession_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $accession_no);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo json_encode(["isDuplicate" => $row['count'] > 0]);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    error_log("Error in add_books_check_accession.php: " . $e->getMessage());
    echo json_encode(["error" => "An error occurred while processing your request."]);
} finally {
    $conn->close();
}
?>
