<?php
session_start();
include '../connection.php';

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : '';
    $fullName = isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '';
    $dueDate = isset($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : '';

    if (isset($_SESSION['book_bag']) && count($_SESSION['book_bag']) > 0) {
        $bookBag = $_SESSION['book_bag'];
        $issued_date = date('Y-m-d');

        // Retrieve and increment the walk_in_id
        $sqlMaxId = "SELECT MAX(walk_in_id) AS max_id FROM borrow";
        $result = $conn->query($sqlMaxId);
        if ($result) {
            $row = $result->fetch_assoc();
            $walk_in_id = $row['max_id'] ? $row['max_id'] + 1 : 1;
        } else {
            die("Error fetching walk_in_id: " . $conn->error);
        }

        $successfulInserts = 0;
        foreach ($bookBag as $book) {
            $bookId = htmlspecialchars($book['id']);
            $table = htmlspecialchars($book['table']);
            $wayOfBorrow = 'walk-in';
            $status = 'borrowed';

            // Retrieve the selected accession number for this book from POST data
            $accessionNo = isset($_POST['accession_no'][$bookId]) ? htmlspecialchars($_POST['accession_no'][$bookId]) : null;

            // Insert the borrow record
            $sql = "INSERT INTO borrow (role, walk_in_id, Full_Name, accession_no, book_id, Category, Date_To_Claim, Issued_Date, Due_Date, Way_Of_Borrow, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sisssssssss", $role, $walk_in_id, $fullName,  $accessionNo, $bookId, $table, $issued_date, $issued_date, $dueDate, $wayOfBorrow, $status);
                
                if ($stmt->execute()) {
                    include '../connection2.php';

                    // Update the No_Of_Copies in the book's category table
                    $bookDeduction = "UPDATE `$table` SET No_Of_Copies = No_Of_Copies - 1 WHERE id = ?";
                    if ($stmt2 = $conn2->prepare($bookDeduction)) {
                        $stmt2->bind_param("i", $bookId);
                        $stmt2->execute();
                        $stmt2->close();

                        // Update the most_borrowed_books table
                        $insert_most_borrowed = "INSERT INTO GFI_Library_Database.most_borrowed_books (book_id, category, date)
                                                 VALUES (?, ?, ?)";
                        if ($stmt3 = $conn2->prepare($insert_most_borrowed)) {
                            $stmt3->bind_param("iss", $bookId, $table, $issued_date);
                            $stmt3->execute();
                            $stmt3->close();
                        }

                        // Update the accession_records table with the selected accession number
                        if ($accessionNo) {
                            $update_accession_query = "UPDATE accession_records SET status = 'borrowed', walk_in_id = ?, user_role = ? WHERE accession_no = ?";
                            if ($stmtAcc = $conn->prepare($update_accession_query)) {
                                $stmtAcc->bind_param("iss", $walk_in_id, $role, $accessionNo);
                                $stmtAcc->execute();
                                $stmtAcc->close();
                            }
                        }

                        $successfulInserts++;
                    }
                }
                $stmt->close();
            }
        }

        // Success message if books are borrowed successfully
        if ($successfulInserts > 0) {
            $_SESSION['book_bag'] = [];
            echo json_encode(['status' => 'success', 'message' => "$successfulInserts books borrowed successfully."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "No books were borrowed."]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "Book bag is empty!"]);
    }
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request method!"]);
}
?>
