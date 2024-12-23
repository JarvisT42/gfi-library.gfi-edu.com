<?php
require 'connection2.php'; // Update with your actual connection file

if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

$table = $_GET['table'] ?? 'All fields';

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
            if ($tableName === 'e-books') {
                continue; // Skip excluded table
            }

            $sql = "SELECT id, title, author, Date_Of_Publication_Copyright, No_Of_Copies, image_path, Available_To_Borrow FROM `$tableName`";
            $tableResult = $conn2->query($sql);

            if (!$tableResult) {
                die("Error fetching data from table $tableName: " . $conn2->error);
            }

            if ($tableResult->num_rows > 0) {
                while ($tableRow = $tableResult->fetch_assoc()) {
                    $allData[] = [
                        'id' => $tableRow['id'],
                        'title' => $tableRow['title'],
                        'author' => $tableRow['author'],
                        'publicationDate' => $tableRow['Date_Of_Publication_Copyright'],
                        'table' => $tableName,
                        'copies' => $tableRow['No_Of_Copies'],
                        'imagePath' => $tableRow['image_path'],

                        'availableToBorrow' => $tableRow['Available_To_Borrow']
                    ];
                }
            }
        }
    }

    echo json_encode(['data' => $allData]);
} else {
    $table = $conn2->real_escape_string($table);
    $sql = "SELECT id, title, author, Date_Of_Publication_Copyright, No_Of_Copies, image_path, Available_To_Borrow FROM `$table`";
    $result = $conn2->query($sql);

    if (!$result) {
        die("Error fetching data from table $table: " . $conn2->error);
    }

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'author' => $row['author'],
                'publicationDate' => $row['Date_Of_Publication_Copyright'],
                'table' => $table,
                'image' => $row['image_path'],
                'copies' => $row['No_Of_Copies'],

                'availableToBorrow' => $row['Available_To_Borrow']
            ];
        }
    }

    echo json_encode(['data' => $data]);
}
?>