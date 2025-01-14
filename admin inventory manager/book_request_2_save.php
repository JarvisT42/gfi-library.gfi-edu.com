<?php
session_start();
include '../connection.php'; // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture submitted form data
    $selected_books = $_POST['selected_books'] ?? [];
    $accession_numbers = $_POST['accession_no'] ?? []; // Accession numbers from form
    $user_id = $_POST['student_id'] ?? $_POST['faculty_id'] ?? null;
    $isStudent = isset($_POST['student_id']);
    $user_role = $isStudent ? 'student' : 'faculty';
    $due_date = $_POST['due_date'] ?? null;

    // Check for required form fields
    if (empty($selected_books) || !$user_id || !$due_date) {
        echo "<script>alert('No books were selected, no user ID provided, or no due date selected.');</script>";
        exit;
    }

    $issued_date = date('Y-m-d'); // Get the current date for issuance

    // Prepare SQL queries
    $update_borrow_query = "UPDATE borrow
        SET status = 'borrowed', accession_no = ?, Issued_date = ?, Due_Date = ?
        WHERE " . ($isStudent ? "student_id" : "faculty_id") . " = ? AND book_id = ? AND Category = ? AND status = 'ready_to_claim'";

    $insert_most_borrowed = "INSERT INTO most_borrowed_books (book_id, category, date) VALUES (?, ?, ?)";

    $update_accession_query = "UPDATE accession_records
        SET status = 'borrowed', available ='borrowed'
        WHERE accession_no = ? and  borrower_id = ?  AND available = 'reserved' LIMIT 1";



    // Prepare statements
    $stmt = $conn->prepare($update_borrow_query);
    $stmt_most_borrowed = $conn->prepare($insert_most_borrowed);
    $stmt_accession = $conn->prepare($update_accession_query);

    // Loop through selected books
    foreach ($selected_books as $book_info) {
        list($book_id, $category) = explode('|', $book_info);
        $book_id = (int)$book_id;


        // Get corresponding accession number for this book
        $accession_no = isset($accession_numbers[$book_id]) ? $accession_numbers[$book_id][0] : null;

        if ($accession_no) {  // Check if accession number is available
            // Update the borrow table to mark the book as borrowed and include the accession number
            $stmt->bind_param("ssssis", $accession_no, $issued_date, $due_date, $user_id, $book_id, $category);
            $stmt->execute();
            if ($stmt->error) echo "Error updating borrow table: " . $stmt->error;

            // Insert into the most_borrowed_books table
            $stmt_most_borrowed->bind_param("iss", $book_id, $category, $issued_date);
            $stmt_most_borrowed->execute();
            if ($stmt_most_borrowed->error) echo "Error updating most_borrowed_books: " . $stmt_most_borrowed->error;

            // Update the accession_records table to set the book as borrowed
            $stmt_accession->bind_param("si", $accession_no, $user_id);
            $stmt_accession->execute();
            if ($stmt_accession->error) echo "Error updating accession_records: " . $stmt_accession->error;
        } else {
            echo "<script>alert('Accession number not found for book ID $book_id');</script>";
        }
    }

    // Display success message and redirect
    echo "<script>alert('Books successfully borrowed!');</script>";
    header("Location: dashboard.php");
    exit();
}
