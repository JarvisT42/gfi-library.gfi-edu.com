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
require '../connection.php'; // Update with your actual path

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
            $sql = "SELECT id, Call_Number, title, author, Date_Of_Publication_Copyright, record_cover, No_Of_Copies, status FROM `$tableName` where archive !='yes' ";

            $tableResult = $conn2->query($sql);

            if (!$tableResult) {
                die("Error fetching data from table $tableName: " . $conn2->error);
            }

            if ($tableResult->num_rows > 0) {
                while ($tableRow = $tableResult->fetch_assoc()) {
                    $coverImage = $tableRow['record_cover'];
                    $coverImageBase64 = base64_encode($coverImage);
                    $coverImageDataUrl = 'data:image/jpeg;base64,' . $coverImageBase64;

                    $isInBag = in_array($tableRow['title'] . '|' . $tableRow['author'] . '|' . $tableRow['Date_Of_Publication_Copyright'] . '|' . $tableName, $bookBagTitles);

                    // Check availability in accession_records
                    $bookId = $tableRow['id']; // Assume this is the book_id
                    $bookCategory = $tableName; // Adjust if necessary

                    $accessionQuery = "SELECT COUNT(*) as available_count FROM accession_records WHERE book_id = '$bookId' AND book_category = '$bookCategory' AND available = 'yes'";
                    $accessionResult = $conn->query($accessionQuery);
                    $accessionRow = $accessionResult->fetch_assoc();

                    // Determine availability
                    $availableToBorrow = ($accessionRow['available_count'] <= 1) ? 'No' : 'Yes';

                    $allData[] = [
                        'id' => $tableRow['id'],
                        'callNumber' => $tableRow['Call_Number'],
                        'title' => $tableRow['title'],
                        'author' => $tableRow['author'],
                        'publicationDate' => $tableRow['Date_Of_Publication_Copyright'],
                        'table' => $tableName,
                        'coverImage' => $coverImageDataUrl,
                        'copies' => $tableRow['No_Of_Copies'],
                        'inBag' => $isInBag,
                        'availableToBorrow' => $availableToBorrow,
                    ];
                }
            }
        }
    }

    echo json_encode(['data' => $allData, 'bookBagCount' => $bookBagCount]);
} else {
    $table = $conn2->real_escape_string($table);
    $sql = "SELECT id, Call_Number, title, author, Date_Of_Publication_Copyright, record_cover, No_Of_Copies, status FROM `$table` where archive !='yes' ";

    $result = $conn2->query($sql);

    if (!$result) {
        die("Error fetching data from table $table: " . $conn2->error);
    }

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $coverImage = $row['record_cover'];
            $coverImageBase64 = base64_encode($coverImage);
            $coverImageDataUrl = 'data:image/jpeg;base64,' . $coverImageBase64;

            $isInBag = in_array($row['title'] . '|' . $row['author'] . '|' . $row['Date_Of_Publication_Copyright'] . '|' . $table, $bookBagTitles);

            // Check availability in accession_records
            $bookId = $row['id']; // Assume this is the book_id
            $bookCategory = $table; // Adjust if necessary

            $accessionQuery = "SELECT COUNT(*) as available_count FROM accession_records WHERE book_id = '$bookId' AND book_category = '$bookCategory' AND available = 'yes'";
            $accessionResult = $conn->query($accessionQuery);
            $accessionRow = $accessionResult->fetch_assoc();

            // Determine availability
            $availableToBorrow = ($accessionRow['available_count'] <= 1) ? 'No' : 'Yes';

            $data[] = [
                'id' => $row['id'],
                'callNumber' => $row['Call_Number'],
                'title' => $row['title'],
                'author' => $row['author'],
                'publicationDate' => $row['Date_Of_Publication_Copyright'],
                'table' => $table,
                'coverImage' => $coverImageDataUrl,
                'copies' => $row['No_Of_Copies'],
                'inBag' => $isInBag,
                'availableToBorrow' => $availableToBorrow,
            ];
        }
    }

    echo json_encode(['data' => $data, 'bookBagCount' => $bookBagCount]);
}
?>
