<?php
require '../connection2.php';

header('Content-Type: application/json');

// Get input parameters
$term = $_GET['term'] ?? '';
$call_number = $_GET['call_number'] ?? '';
$type = $_GET['type'] ?? '';

try {
    // Autocomplete logic
    if (!empty($term) && !empty($type)) {
        $results = [];
        $term = "%$term%";
        $tables = [];

        // Fetch all table names
        $queryTables = "SHOW TABLES";
        $resultTables = $conn2->query($queryTables);

        while ($row = $resultTables->fetch_row()) {
            if ($row[0] !== 'e-books') {
                $tables[] = $row[0];
            }
        }

        foreach ($tables as $table) {
            $sql = match ($type) {
                'call_number' => "SELECT Call_Number AS result FROM `$table` WHERE Call_Number LIKE ? limit 5",
                'book_title' => "SELECT Title AS result FROM `$table` WHERE Title LIKE ? limit 5",
                'author' => "SELECT Author AS result FROM `$table` WHERE Author LIKE ? limit 5",
                default => null,
            };

            if ($sql) {
                $stmt = $conn2->prepare($sql);
                $stmt->bind_param("s", $term);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $results[] = $row['result'];
                }

                $stmt->close();
            }
        }

        echo json_encode($results);
        exit;
    }

    // Duplicate validation logic
    if (!empty($call_number)) {
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
        exit;
    }

    // If no valid parameters are provided, return an empty response
    echo json_encode([]);
} catch (Exception $e) {
    error_log("Error in add_books_handler.php: " . $e->getMessage());
    echo json_encode(["error" => "An error occurred while processing your request."]);
} finally {
    $conn2->close();
}
