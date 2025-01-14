<?php
include '../connection.php'; // Include your database connection
include '../connection2.php'; // Include your second database connection

// Retrieve the posted JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data was received correctly
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

// Get the current date
$returnedDate = date('Y-m-d');

// Prepare and bind the update query for the borrow record
$updateQuery = "UPDATE borrow SET status = 'returned', Over_Due_Fines = ?, Book_Fines = ?, Damage_Description = ?, Return_Date = ? WHERE walk_in_id = ? AND book_id = ? AND category = ?";

$stmt = $conn->prepare($updateQuery);

// Check if the statement preparation was successful
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn->error]);
    exit();
}

// Bind parameters (adjust data types accordingly)
$fines = $data['fines'];
$bookFines = $data['book_fines']; // Ensure you include this in the fetch data from JS
$damageDescription = $data['damage_description']; // Add damage description
$walkinId = $data['walkin_id']; // Changed to walkin_id
$bookId = $data['book_id'];
$category = $data['category'];
$accessionNo = $data['accession_no']; // Add accession_no to data

// Bind parameters to the first update query
$stmt->bind_param('ddsssss', $fines, $bookFines, $damageDescription, $returnedDate, $walkinId, $bookId, $category); // 'ddsssss' for two doubles and five strings

// Execute the first query (for borrow update)
if ($stmt->execute()) {

    // Prepare the second query to increment No_Of_Copies in the specific category table
    $bookAdditionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies + 1 WHERE id = ?";
    $bookStmt = $conn2->prepare($bookAdditionSql);

    // Check if the second query statement preparation was successful
    if (!$bookStmt) {
        echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn2->error]);
        exit();
    }

    // Bind the bookId to the second query (assuming id is an integer)
    $bookStmt->bind_param('i', $bookId); // Assuming 'id' is an integer, adjust type if necessary

    // Execute the second query (for updating No_Of_Copies)
    if ($bookStmt->execute()) {

        // Prepare the third query to update the accession_records table
        $updateAccessionQuery = "UPDATE accession_records SET status = 'available', available = 'yes' WHERE accession_no = ? AND walk_in_id = ? AND status = 'lost'";
        $stmtAccession = $conn->prepare($updateAccessionQuery);

        // Check if the third statement preparation was successful
        if (!$stmtAccession) {
            echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn->error]);
            exit();
        }

        // Bind the accession_no and walk_in_id to the third query
        $stmtAccession->bind_param('si', $accessionNo, $walkinId);

        // Execute the third query
        if ($stmtAccession->execute()) {
            // If all updates were successful
            echo json_encode(['success' => true]);
        } else {
            // If the third query fails
            echo json_encode(['success' => false, 'message' => 'Failed to update accession records: ' . $stmtAccession->error]);
        }

        // Close the third statement
        $stmtAccession->close();

    } else {
        // If the second query fails
        echo json_encode(['success' => false, 'message' => 'Failed to update book copies: ' . $bookStmt->error]);
    }

    // Close the second statement
    $bookStmt->close();

} else {
    // If the first query fails
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
}

// Close the first statement and connection
$stmt->close();
$conn->close();
$conn2->close();

?>
