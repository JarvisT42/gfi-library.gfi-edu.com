<?php
session_start();
include '../connection2.php'; // Ensure you have your database connection
include '../connection.php'; // Ensure you have your database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if `id` and `table` (category) exist in the URL
if (isset($_GET['id']) && isset($_GET['table'])) {
    $book_id = $_GET['id'];
    $category = $_GET['table'];
} else {
    // Redirect if no ID or table is provided
    echo "<script>window.location.href='books.php';</script>";
    exit;
}

// Archive Book
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['accession_numbers'])) {
    $accession_numbers = $data['accession_numbers'];

    // Check if the provided accession numbers are available
    $check_availability_sql = "SELECT accession_no, available 
FROM accession_records 
WHERE book_id = ? 
  AND book_category = ? 
  AND (available = 'reserved' OR available = 'borrowed' OR available = 'no') 
  AND archive != 'yes';
";
    $check_availability_stmt = $conn->prepare($check_availability_sql);
    $check_availability_stmt->bind_param("is", $book_id, $category);
    $check_availability_stmt->execute();
    $check_availability_result = $check_availability_stmt->get_result();

    // Define a function to handle archiving and deduction
    function archiveAndDeductCopies($accession_numbers, $book_id, $category, $conn, $conn2)
    {
        // Archive books by updating their `archive` field
        $archive_sql = "UPDATE accession_records SET archive = 'yes' WHERE accession_no = ?";
        $archive_stmt = $conn->prepare($archive_sql);

        // Loop through each accession number and update it
        foreach ($accession_numbers as $accession_no) {
            $archive_stmt->bind_param("s", $accession_no);
            $archive_stmt->execute();

            // Deduct copies from the category table for each book archived
            $bookDeductionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies - 1 WHERE id = ?";
            $deductionStmt = $conn2->prepare($bookDeductionSql);
            $deductionStmt->bind_param("i", $book_id);
            $deductionStmt->execute();
        }
    }

    // Proceed if there are available records to archive
    if ($check_availability_result->num_rows > 0) {
        archiveAndDeductCopies($accession_numbers, $book_id, $category, $conn, $conn2);

        // Return success message
        echo json_encode(['success' => true, 'message' => 'Books archived successfully.']);
    } else {
        // If no available books to archive, still archive at the category level and deduct copies
        $sql = "UPDATE `$category` SET archive = 'yes' WHERE id = ?";
        $stmt = $conn2->prepare($sql);
        $stmt->bind_param("i", $book_id);

        if ($stmt->execute()) {
            archiveAndDeductCopies($accession_numbers, $book_id, $category, $conn, $conn2);

            // Return success message
            echo json_encode(['success' => true, 'message' => 'Books archived at the category level.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No available books to archive or books already archived.']);
        }
    }
} else {
    // If no accession numbers were sent
    echo json_encode(['success' => false, 'message' => 'Invalid request. No accession numbers provided.']);
}
