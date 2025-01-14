<?php
// Include the database connection
require '../connection2.php'; // Update with your actual path

if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// Retrieve data specifically from the "e-books" table
$table = "e-books";
$sql = "SELECT title, author, subject,  record_cover, link FROM `$table`";

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

        $data[] = [
            'title' => $row['title'],
            'author' => $row['author'],
            'subject' => $row['subject'],

            'coverImage' => $coverImageDataUrl,
            'link' => $row['link'],
        ];
    }
}

// Return the data as JSON
echo json_encode(['data' => $data]);
?>
