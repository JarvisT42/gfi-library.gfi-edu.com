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
require '../connection.php'; // For accession_records database connection

require '../connection2.php'; // Update with your actual path

if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// Now $borrowedBooks contains the titles, authors, and categories of the borrowed books

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


            $sql = "SELECT id, Call_Number, title, author, Date_Of_Publication_Copyright, record_cover, No_Of_Copies FROM `$tableName`  ";

            $tableResult = $conn2->query($sql);

            if (!$tableResult) {
                die("Error fetching data from table $tableName: " . $conn2->error);
            }



            // Now we need to check the accession_records table
            while ($tableRow = $tableResult->fetch_assoc()) {
                $bookId = $tableRow['id'];

                // Query using the second connection (conn) to check if the book is in the accession_records table
                $accessionSql = "SELECT * FROM accession_records
                                 WHERE book_id = ?
                                 AND book_category = ?
                                 AND repaired != 'yes'";

                $stmt = $conn->prepare($accessionSql);
                $stmt->bind_param('is', $bookId, $tableName);  // 'is' means integer and string
                $stmt->execute();
                $accessionResult = $stmt->get_result();

                if ($accessionResult && $accessionResult->num_rows > 0) {
                    $coverImage = $tableRow['record_cover'];
                    $coverImageBase64 = base64_encode($coverImage);
                    $coverImageDataUrl = 'data:image/jpeg;base64,' . $coverImageBase64;

                    $isInBag = in_array($tableRow['title'] . '|' . $tableRow['author'] . '|' . $tableRow['Date_Of_Publication_Copyright'] . '|' . $tableName, $bookBagTitles);

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
                    ];
                }
            }













        }
    }

    echo json_encode(['data' => $allData, 'bookBagCount' => $bookBagCount]);
} else {
    $table = $conn2->real_escape_string($table);
    $sql = "SELECT id, Call_Number, title, author, Date_Of_Publication_Copyright, record_cover, No_Of_Copies FROM `$table`  ";

    $result = $conn2->query($sql);

    if (!$result) {
        die("Error fetching data from table $table: " . $conn2->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $bookId = $row['id'];

        // Query using the second connection (conn) to check if the book is in the accession_records table
        $accessionSql = "SELECT * FROM accession_records
                         WHERE book_id = ?
                         AND book_category = ?
                         AND repaired != 'yes'";

        $stmt = $conn->prepare($accessionSql);
        $stmt->bind_param('is', $bookId, $table);  // 'is' means integer and string
        $stmt->execute();
        $accessionResult = $stmt->get_result();

        if ($accessionResult && $accessionResult->num_rows > 0) {
            $coverImage = $row['record_cover'];
            $coverImageBase64 = base64_encode($coverImage);
            $coverImageDataUrl = 'data:image/jpeg;base64,' . $coverImageBase64;

            $isInBag = in_array($row['title'] . '|' . $row['author'] . '|' . $row['Date_Of_Publication_Copyright'] . '|' . $table, $bookBagTitles);

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
            ];
        }
    }

    echo json_encode(['data' => $data, 'bookBagCount' => $bookBagCount]);
}
?>