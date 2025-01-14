<?php
include '../connection.php';
include '../connection2.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['returnall'])) {
    if (isset($_POST['books']) && isset($_POST['user_id']) && isset($_POST['user_role'])) {


        $books = json_decode($_POST['books'], true);
        $userId = htmlspecialchars($_POST['user_id']);
        $userRole = htmlspecialchars($_POST['user_role']);
        $userColumn = ($userRole === 'student') ? 'student_id' : (($userRole === 'faculty') ? 'faculty_id' : 'walk_in_id');
        $success = true;
        $returnedDate = date('Y-m-d');

        $updateQuery = "UPDATE borrow 
                    SET status = 'returned', Return_Date = ? 
                    WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'lost'";
        $stmt = $conn->prepare($updateQuery);

        $updateAccessionQuery = "UPDATE accession_records 
                             SET status = 'available', borr = ?, user_id = NULL 
                             WHERE accession_no = ? AND " . ($userRole === 'walk-in' ? "walk_in_id" : "user_id") . " = ? AND status = 'lost'";

        $updateAccessionQuery = "
UPDATE accession_records 
SET status = 'available', available = 'yes' 
WHERE accession_no = ? AND borrower_id = ? AND book_id = ? AND status = 'borrowed'
";

        $stmtAccession = $conn->prepare($updateAccessionQuery);

        foreach ($books as $book) {
            $book_id = $book['book_id'];
            $category = $book['category'];
            $accession_no = $book['accession_no'];

            if (!$stmt->bind_param('sisi', $returnedDate, $userId, $book_id, $category) || !$stmt->execute()) {
                $success = false;
                break;
            }

            if (!$stmtAccession->bind_param('si', $accession_no, $userId) || !$stmtAccession->execute()) {
                $success = false;
                break;
            }

            $bookAdditionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies + 1 WHERE id = ?";
            $stmtBookAddition = $conn2->prepare($bookAdditionSql);
            if (!$stmtBookAddition->bind_param('i', $book_id) || !$stmtBookAddition->execute()) {
                $success = false;
                break;
            }
            $stmtBookAddition->close();
        }

        $stmt->close();
        $stmtAccession->close();

        if ($success) {
            echo 'Books returned and accession records updated successfully.';
        } else {
            echo 'Failed to return all books and update accession records.';
        }
    } else {
        echo 'Invalid request data.';
    }
} else {
    echo 'No action performed.';
}
?>


// <?php
    // include '../connection.php';
    // include '../connection2.php'; 
    //  Get data from the POST request
    // $data = json_decode(file_get_contents('php://input'), true);

    // if (!empty($data['books']) && (isset($_GET['student_id']) || isset($_GET['faculty_id']) || isset($_GET['walk_in_id']))) {
    //     $books = $data['books'];
    //     $success = true;
    //     $returnedDate = date('Y-m-d'); // Current date for return

    //     // Determine user type and ID based on URL parameter
    //     if (isset($_GET['student_id'])) {
    //         $userId = $_GET['student_id'];
    //         $userColumn = 'student_id';
    //         $userRole = 'student';
    //     } elseif (isset($_GET['faculty_id'])) {
    //         $userId = $_GET['faculty_id'];
    //         $userColumn = 'faculty_id';
    //         $userRole = 'faculty';
    //     } elseif (isset($_GET['walk_in_id'])) {
    //         $userId = $_GET['walk_in_id'];
    //         $userColumn = 'walk_in_id';
    //         $userRole = 'walk-in';
    //     } else {
    //         echo json_encode(['success' => false, 'message' => 'User ID not specified.']);
    //         exit;
    //     }

    //     // Prepare the borrow update query
    //     $updateQuery = "UPDATE borrow 
    //                     SET status = 'returned', Return_Date = ? 
    //                     WHERE $userColumn = ? AND book_id = ? AND category = ? AND status = 'lost'";
    //     $stmt = $conn->prepare($updateQuery);

    //     // Prepare the accession_records update query
    //     $updateAccessionQuery = "UPDATE accession_records 
    //                              SET status = 'available', walk_in_id = NULL, user_id = NULL 
    //                              WHERE accession_no = ? AND " . ($userRole === 'walk-in' ? "walk_in_id" : "user_id") . " = ? AND status = 'lost'";
    //     $stmtAccession = $conn->prepare($updateAccessionQuery);

    //     foreach ($books as $book) {
    //         $book_id = $book['book_id'];
    //         $category = $book['category'];
    //         $accession_no = $book['accession_no'];

    //         // Update the borrow table
    //         if (!$stmt->bind_param('sisi', $returnedDate, $userId, $book_id, $category) || !$stmt->execute()) {
    //             $success = false;
    //             break;
    //         }

    //         // Update the accession_records table
    //         if (!$stmtAccession->bind_param('si', $accession_no, $userId) || !$stmtAccession->execute()) {
    //             $success = false;
    //             break;
    //         }

    //         // Increment No_Of_Copies in the book's specific category table
    //         $bookAdditionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies + 1 WHERE id = ?";
    //         $stmtBookAddition = $conn2->prepare($bookAdditionSql);
    //         if (!$stmtBookAddition->bind_param('i', $book_id) || !$stmtBookAddition->execute()) {
    //             $success = false;
    //             break;
    //         }
    //         $stmtBookAddition->close(); // Close to reset for each iteration
    //     }

    //     // Close the prepared statements
    //     $stmt->close();
    //     $stmtAccession->close();

    //     if ($success) {
    //         echo json_encode(['success' => true, 'message' => 'Books returned and accession records updated successfully.']);
    //     } else {
    //         echo json_encode(['success' => false, 'message' => 'Failed to return all books and update accession records.']);
    //     }
    // } else {
    //     echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    // }
    // 
    ?>