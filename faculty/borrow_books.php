<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../connection.php';  // Assuming you have a connection script
include '../connection2.php';  // Assuming you have a connection script for book update

// Get the student name, selected time, date, and email from session
$id = $_SESSION["Faculty_Id"];
$email = $_SESSION["email"];
$selectedDate = $_SESSION["selected_date"];
$selectedTime = $_SESSION["selected_time"];
$appointment_id = $_SESSION["appointment_id"];

// Save book IDs (assuming you have book IDs in your session for each book)
$bookBag = $_SESSION['book_bag']; // Books in session

$calendarUpdated = false; // Flag to track if calendar update has already occurred

if (!empty($bookBag)) {
    // Iterate through the books and check the number of copies before borrowing
    foreach ($bookBag as $book) {
        $book_id = $book['id'];  // Assuming 'id' exists in the book session data
        $table = $book['table'];  // Assuming 'table' exists in the book session data
        $role = 'Faculty';
        $status = 'pending';
        $way_of_borrow = 'online';

        // Query to check the number of copies
        
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

                $accessionUpdateSql = "UPDATE accession_records SET available = 'reserved', borrowable = 'no', borrower_id = ? 
                WHERE accession_no = ?";

                if ($stmtUpdate = $conn->prepare($accessionUpdateSql)) {
                    $stmtUpdate->bind_param("is", $id, $accession_no);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();

                    // Step 3: Insert borrow record
                    $insertBorrowSql = "INSERT INTO borrow (role, faculty_id, book_id, Category, accession_no, appointment_id, Date_to_claim, time, Way_Of_Borrow, status) 
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

    // If calendar slot hasn't been updated yet, update it now
    if (!$calendarUpdated) {
        $columnToUpdate = ($selectedTime === 'morning') ? 'morning' : 'afternoon';
        $calendarUpdateSql = "UPDATE calendar_appointment SET $columnToUpdate = $columnToUpdate - 1 WHERE calendar = ?";

        if ($stmt2 = $conn->prepare($calendarUpdateSql)) {
            $stmt2->bind_param("s", $selectedDate);
            if (!$stmt2->execute()) {
                $_SESSION['error_message'] = 'Error updating calendar slots: ' . $stmt2->error;
            }
            $stmt2->close();

            // Mark the calendar update as done
            $calendarUpdated = true;
        } else {
            $_SESSION['error_message'] = 'Error preparing calendar update query: ' . $conn->error;
        }
    }
} else {
    $_SESSION['error_message'] = "No books in your bag.";
}

// Clear the session variables but before that, store necessary data in variables
$firstName = $_SESSION["First_Name"];
$lastName = $_SESSION["Last_Name"];
$email = $_SESSION["email"];
$phoneNo = $_SESSION["phoneNo."];

$bookBagTitles = array_map(function ($book) {
    return $book['title'] . '|' . $book['author'];
}, $_SESSION['book_bag']);

// Convert book titles and authors into a query string
$bookBagTitlesStr = urlencode(serialize($bookBagTitles));

// Clear the session variables
unset($_SESSION['book_bag']);
unset($_SESSION['selected_date']);
unset($_SESSION['selected_time']);

// Redirect to the success page, passing data via URL parameters
header("Location: success_booked.php?firstName=$firstName&lastName=$lastName&email=$email&phoneNo=$phoneNo&date=$selectedDate&time=$selectedTime&books=$bookBagTitlesStr");
exit();
