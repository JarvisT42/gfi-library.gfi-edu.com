<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require '../connection2.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = $_GET['search'] ?? '';
$recordsPerPage = 10;
$offset = ($page - 1) * $recordsPerPage;

$response = ['data' => [], 'totalRecords' => 0];
$searchCondition = !empty($search) ? "WHERE title LIKE '%$search%' OR author LIKE '%$search%'" : '';

try {
    if ($_GET['table'] === 'All fields') {
        // Fetch all table names
        $tablesQuery = "SHOW TABLES";
        $tablesResult = $conn2->query($tablesQuery);

        if (!$tablesResult) {
            throw new Exception("Failed to fetch table names: " . $conn2->error);
        }

        $unionQueries = [];
        $totalCountQueries = [];
        while ($tableRow = $tablesResult->fetch_array()) {
            $tableName = $tableRow[0];
            if ($tableName === 'e-books') continue; // Exclude 'e-books'

            $unionQueries[] = "SELECT id, title, author, date_of_publication_copyright AS publicationDate, No_Of_Copies AS copies, '$tableName' AS tableName
                               FROM `$tableName` $searchCondition";

            $totalCountQueries[] = "SELECT COUNT(*) AS total FROM `$tableName` $searchCondition";
        }

        if (empty($unionQueries)) {
            throw new Exception("No tables found in the database.");
        }

        // Combine all table queries for data and count
        $query = implode(" UNION ALL ", $unionQueries) . " LIMIT $recordsPerPage OFFSET $offset";
        $countQuery = implode(" UNION ALL ", $totalCountQueries);

        // Calculate total records
        $totalCountResult = $conn2->query($countQuery);
        $totalRecords = 0;
        while ($row = $totalCountResult->fetch_assoc()) {
            $totalRecords += $row['total'];
        }

        $response['totalRecords'] = $totalRecords;
    } else {
        // Query a specific table
        $table = $conn2->real_escape_string($_GET['table']);
        if ($table === 'e-books') {
            throw new Exception("'e-books' table is excluded from this query.");
        }

        $query = "SELECT id, title, author, date_of_publication_copyright AS publicationDate, No_Of_Copies AS copies
                  FROM `$table` $searchCondition LIMIT $recordsPerPage OFFSET $offset";
        $countQuery = "SELECT COUNT(*) AS total FROM `$table` $searchCondition";

        $countResult = $conn2->query($countQuery);
        $response['totalRecords'] = $countResult->fetch_assoc()['total'];
    }

    // Fetch data for the current page
    $result = $conn2->query($query);
    if (!$result) {
        throw new Exception("Query failed: " . $conn2->error);
    }

    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $response['error'] = $e->getMessage();
    http_response_code(500);
}

header('Content-Type: application/json');
echo json_encode($response);
?>
