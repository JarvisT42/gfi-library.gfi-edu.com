<?php
session_start(); // Start the session

if (!isset($_SESSION['book_bag'])) {
    $_SESSION['book_bag'] = [];
}

$bookBag = $_SESSION['book_bag'];
$bookBagTitles = array_map(function($book) {
    return $book['title'] . '|' . $book['author'] . '|' . $book['publicationDate'] . '|' . $book['table'];
}, $bookBag);

// Count of items in the book bag
$bookBagCount = count($bookBag);

require '../connection2.php'; // Update with your actual path

if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

$table = $_GET['table'] ?? '';

if ($table === 'All fields') {
    $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
    $result = $conn2->query($sql);

    if (!$result) {
        die("Error fetching tables: " . $conn2->error);
    }

    $allData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array()) {
            $tableName = $row[0];
            $tableName = $conn2->real_escape_string($tableName);
            $excludedTable = "e-books";

            if ($tableName === $excludedTable) {
                continue; // Skip this iteration
            }

            $sql = "SELECT id, title, author, Call_Number, Date_Of_Publication_Copyright, No_Of_Copies, image_path FROM `$tableName` where archive !='yes' ";

            $tableResult = $conn2->query($sql);

            if (!$tableResult) {
                die("Error fetching data from table $tableName: " . $conn2->error);
            }

            if ($tableResult->num_rows > 0) {
                while ($tableRow = $tableResult->fetch_assoc()) {
                    $isInBag = in_array($tableRow['title'] . '|' . $tableRow['author'] . '|' . $tableRow['Date_Of_Publication_Copyright'] . '|' . $tableName, $bookBagTitles);

                    $allData[] = [
                        'id' => $tableRow['id'],
                        'title' => $tableRow['title'],
                        'author' => $tableRow['author'],
                        'callNumber' => $tableRow['Call_Number'],
                        'publicationDate' => $tableRow['Date_Of_Publication_Copyright'],
                        'table' => $tableName,
                        'imagePath' => $tableRow['image_path'],
                        'copies' => $tableRow['No_Of_Copies'],
                        'inBag' => $isInBag,
                    ];
                }
            }
        }
    }

    echo json_encode(['data' => $allData, 'bookBagCount' => $bookBagCount]);
} else {
    $table = $conn2->real_escape_string($table);
    $sql = "SELECT id, title, author, Call_Number, Date_Of_Publication_Copyright, No_Of_Copies, image_path FROM `$table` where archive !='yes' ";

    $result = $conn2->query($sql);

    if (!$result) {
        die("Error fetching data from table $table: " . $conn2->error);
    }

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $isInBag = in_array($row['title'] . '|' . $row['author'] . '|' . $row['Date_Of_Publication_Copyright'] . '|' . $table, $bookBagTitles);

            $data[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'author' => $row['author'],
                'callNumber' => $row['Call_Number'],
                'publicationDate' => $row['Date_Of_Publication_Copyright'],
                'table' => $table,
                'copies' => $row['No_Of_Copies'],
                'imagePath' => $row['image_path'],
                'inBag' => $isInBag,
            ];
        }
    }

    echo json_encode(['data' => $data, 'bookBagCount' => $bookBagCount]);
}
?>
