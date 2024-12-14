<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../connection.php';  // Assuming you have a connection script
include '../connection2.php';  // Assuming you have a connection script for book update

$id = $_SESSION["Student_Id"];
$email = $_SESSION["email"];
$selectedDate = $_SESSION["selected_date"];
$selectedTime = $_SESSION["selected_time"];
$appointment_id = $_SESSION["appointment_id"];
$bookBag = $_SESSION['book_bag'];
$calendarUpdated = false;

if (!empty($bookBag)) {
    foreach ($bookBag as $book) {
        $book_id = $book['id'];
        $table = $book['table'];
        $role = 'Student';
        $status = 'pending';
        $way_of_borrow = 'online';

        // Step 1: Fetch the available accession number
        $selectAccessionSql = "SELECT accession_no FROM accession_records
        WHERE book_id = ? AND book_category = ? AND available = 'yes'
        LIMIT 1";
        if ($stmtSelect = $conn->prepare($selectAccessionSql)) {
            $stmtSelect->bind_param("is", $book_id, $table);
            $stmtSelect->execute();
            $stmtSelect->bind_result($accession_no);

            if ($stmtSelect->fetch()) {
                // Close the select statement before executing the next query
                $stmtSelect->close();
                // Step 2: Mark the accession as reserved
                $accessionUpdateSql = "UPDATE accession_records SET available = 'reserved', borrower_id = ?
                WHERE accession_no = ?";
                if ($stmtUpdate = $conn->prepare($accessionUpdateSql)) {
                    $stmtUpdate->bind_param("is", $id, $accession_no);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                    // Step 3: Insert borrow record
                    $insertBorrowSql = "INSERT INTO borrow (role, student_id, book_id, Category, accession_no, appointment_id, Date_to_claim, time, Way_Of_Borrow, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($stmtInsert = $conn->prepare($insertBorrowSql)) {
                        $stmtInsert->bind_param("siisssssss", $role, $id, $book_id, $table, $accession_no, $appointment_id, $selectedDate, $selectedTime, $way_of_borrow, $status);
                        $stmtInsert->execute();
                        $stmtInsert->close();
                    } else {
                        $_SESSION['error_message'] = 'Error preparing borrow insert query: ' . $conn->error;
                    }
                } else {
                    $_SESSION['error_message'] = 'Error preparing accession update query: ' . $conn->error;
                }
            } else {
                $_SESSION['error_message'] = 'No available copies for book ID: ' . $book_id;
                $stmtSelect->close();
            }
        } else {
            $_SESSION['error_message'] = 'Error preparing accession select query: ' . $conn->error;
        }
    }
    // Step 4: Update the calendar slot
    if (!$calendarUpdated) {
        $columnToUpdate = ($selectedTime === 'morning') ? 'morning' : 'afternoon';
        $calendarUpdateSql = "UPDATE calendar_appointment SET $columnToUpdate = $columnToUpdate - 1 WHERE calendar = ?";
        if ($stmtCalendar = $conn->prepare($calendarUpdateSql)) {
            $stmtCalendar->bind_param("s", $selectedDate);
            $stmtCalendar->execute();
            $stmtCalendar->close();
            $calendarUpdated = true;
        } else {
            $_SESSION['error_message'] = 'Error preparing calendar update query: ' . $conn->error;
        }
    }
} else {
    $_SESSION['error_message'] = "No books in your bag.";
}
// Clear session variables
$firstName = $_SESSION["First_Name"];
$lastName = $_SESSION["Last_Name"];
$email = $_SESSION["email"];
$phoneNo = $_SESSION["phoneNo."];
$bookBagTitles = array_map(function ($book) {
    return $book['title'] . '|' . $book['author'];
}, $_SESSION['book_bag']);
$bookBagTitlesStr = urlencode(serialize($bookBagTitles));
unset($_SESSION['book_bag']);
unset($_SESSION['selected_date']);
unset($_SESSION['selected_time']);
header("Location: success_booked.php?firstName=$firstName&lastName=$lastName&email=$email&phoneNo=$phoneNo&date=$selectedDate&time=$selectedTime&books=$bookBagTitlesStr");
exit();
?>
