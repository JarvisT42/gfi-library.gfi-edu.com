<?php
session_start(); // Start the session

include '../connection.php'; // Primary database connection
include '../connection2.php'; // Secondary database connection

header('Content-Type: application/json');

// Enable error logging to error.txt
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.txt');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

    // Retrieve and sanitize the main fields
    $book_id = htmlspecialchars($_POST['book_id'] ?? '');
    $category = htmlspecialchars($_POST['category'] ?? '');
    $isbn = htmlspecialchars($_POST['isbn'] ?? '');
    $call_number = htmlspecialchars($_POST['call_number'] ?? '');
    $book_title = htmlspecialchars($_POST['book_title'] ?? '');
    $author = htmlspecialchars($_POST['author'] ?? '');
    $department = htmlspecialchars($_POST['department'] ?? '');
    $date_of_publication_copyright = htmlspecialchars($_POST['date_of_publication_copyright'] ?? '');
    $publisher_name = htmlspecialchars($_POST['publisher_name'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $book_copies = htmlspecialchars($_POST['book_copies'] ?? '');
    $accession_data = json_decode($_POST['accession_data'] ?? '[]', true);

    // Handle file upload
    $files = $_FILES ?? [];
    $imageName = $_FILES['image']['name'] ?? null;
    $imageTmpName = $_FILES['image']['tmp_name'] ?? null;
    $uploadDir = '../uploads/';
    $imagePath = $uploadDir . basename($imageName);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($imageName) {
        move_uploaded_file($imageTmpName, $imagePath);
        $sql = "UPDATE `$category` SET 
            isbn = ?, 
            call_number = ?, 
            department = ?, 
            title = ?, 
            author = ?, 
            publisher = ?, 
            no_of_copies = ?, 
            date_of_publication_copyright = ?, 
            subjects = ?, 
            image_name = ?, 
            image_path = ? 
            WHERE id = ?";
    } else {
        $sql = "UPDATE `$category` SET 
            isbn = ?, 
            call_number = ?, 
            department = ?, 
            title = ?, 
            author = ?, 
            publisher = ?, 
            no_of_copies = ?, 
            date_of_publication_copyright = ?, 
            subjects = ? 
            WHERE id = ?";
    }

    // Update the book details in the primary table
    if ($stmt = $conn2->prepare($sql)) {
        if ($imageName) {
            $stmt->bind_param("ssssssissssi", $isbn, $call_number, $department, $book_title, $author, $publisher_name, $book_copies, $date_of_publication_copyright, $subject, $imageName, $imagePath, $book_id);
        } else {
            $stmt->bind_param("ssssssissi", $isbn, $call_number, $department, $book_title, $author, $publisher_name, $book_copies, $date_of_publication_copyright, $subject, $book_id);
        }

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Book details updated successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to update book details.'];
        }

        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to prepare the SQL statement for updating book details.'];
    }

    // Handle accession data
    if (!empty($accession_data)) {
        $success = true;

        foreach ($accession_data as $accession) {
            $current_accession_no = $accession['original_accession_no'] ?? null;
            $new_accession_no = $accession['accession_no'] ?? null;
            $book_condition = $accession['book_condition'] ?? null;
            $borrowable = $accession['borrowable'] === "yes" ? "yes" : "no";
            $isNew = $accession['isNew'] ?? false;

            if ($isNew && $new_accession_no) {
                // Insert new accession
                $insert_sql = "INSERT INTO `accession_records` (book_id, book_category, accession_no, call_number, book_condition, available) 
                               VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);

                if ($stmt === false) {
                    error_log("Failed to prepare insert statement: " . $conn->error);
                    $success = false;
                    break;
                }

                $stmt->bind_param("isssss", $book_id, $category, $new_accession_no, $call_number, $book_condition, $borrowable);

                if (!$stmt->execute()) {
                    error_log("Failed to insert new accession number: {$new_accession_no}. Error: " . $stmt->error);
                    $success = false;
                    break;
                }
            } elseif (!$isNew && $current_accession_no && $new_accession_no) {
                // Update existing accession
                $update_sql = "UPDATE `accession_records` SET accession_no = ?, book_condition = ?, borrowable = ? 
                               WHERE accession_no = ?";
                $stmt = $conn->prepare($update_sql);

                if ($stmt === false) {
                    error_log("Failed to prepare update statement: " . $conn->error);
                    $success = false;
                    break;
                }

                $stmt->bind_param("ssss", $new_accession_no, $book_condition, $borrowable, $current_accession_no);

                if (!$stmt->execute()) {
                    error_log("Failed to update accession number: {$current_accession_no}. Error: " . $stmt->error);
                    $success = false;
                    break;
                }
            } else {
                error_log("Invalid accession data: " . json_encode($accession));
                $success = false;
                break;
            }
        }

        if (!$success) {
            $response = ['status' => 'error', 'message' => 'Failed to update some accession records.'];
        }
    }

    // Log the received data for debugging
    $logFile = __DIR__ . '/error.txt';
    $logData = "Received Data:\n" . print_r($_POST, true);
    $logData .= "\nReceived Files:\n" . print_r($files, true);

    // Write to error.txt
    file_put_contents($logFile, $logData, FILE_APPEND);

    // Respond with final status
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
