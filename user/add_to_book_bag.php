<?php
session_start();
include '../connection.php'; // Include your database connection script

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['Student_Id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Student not logged in']);
    exit;
}

$studentId = $_SESSION['Student_Id']; // Assuming the student ID is stored in the session

// Check how many books the student currently has borrowed
$query = "SELECT COUNT(*) AS borrowed_count FROM borrow WHERE student_id = ? AND (status = 'pending' OR status = 'ready_to_claim' OR status = 'borrowed' OR status = 'lost')";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$currentBorrowedCount = $row['borrowed_count'];

// Calculate the total number of books in the book bag and borrowed
$totalBooksCount = $currentBorrowedCount + (isset($_SESSION['book_bag']) ? count($_SESSION['book_bag']) : 0);

// Check if the total exceeds the limit of 3 books
if ($totalBooksCount >= 3) {
    if ($currentBorrowedCount > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You can only borrow 3 books total. Look for activity log.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'You can only borrow 3 books total.']);
    }
    exit;
}

// Check if the 'book_bag' session exists
if (!isset($_SESSION['book_bag'])) {
    $_SESSION['book_bag'] = [];
}

// Include the 'id' in the book array
$book = [
    'id' => $data['id'], // Include the 'id' field
    'title' => $data['title'],
    'author' => $data['author'],
    'publicationDate' => $data['publicationDate'],
    'table' => $data['table'],
    'coverImage' => $data['coverImage'],
    'copies' => $data['copies'],
    'volume' => $data['volume'], // Include the 'volume'
    'edition' => $data['edition'] // Include the 'edition'
];

// Check if the book already exists in the session to prevent duplicates based on title and author
$bookExists = false;
foreach ($_SESSION['book_bag'] as $existingBook) {
    if (
        $existingBook['id'] === $book['id'] &&
        $existingBook['table'] === $book['table'] &&

        $existingBook['title'] === $book['title'] &&
        $existingBook['author'] === $book['author'] &&
        $existingBook['publicationDate'] === $book['publicationDate'] &&
        $existingBook['volume'] === $book['volume'] && // Check for volume
        $existingBook['edition'] === $book['edition'] // Check for edition
    ) {
                $bookExists = true;
        break;
    }
}

if (!$bookExists) {
    $_SESSION['book_bag'][] = $book;
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'This book is already in your bag']);
}
