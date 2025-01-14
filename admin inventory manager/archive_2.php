<?php
session_start();
if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}
include '../connection2.php'; // Ensure you have your database connection
include '../connection.php'; // Ensure you have your database connection

// Retrieve the book_id and category from the URL parameters
if (isset($_GET['id']) && isset($_GET['table'])) {
    $book_id = $_GET['id'];
    $category = $_GET['table'];
} else {
    // Redirect or show error if parameters are missing
    echo "Error: Missing book ID or category.";
    exit;
}


if (isset($_POST['unarchive'])) {
    // Get data from the POST request
    $book_id = $_POST['book_id'];
    $category = $_POST['category'];
    $accession_no = $_POST['accession_no'];

    // Archive the book in the main table
    $sql = "UPDATE `$category` SET archive = 'no' WHERE id = ?";
    $stmt = $conn2->prepare($sql);
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        // Archive related accession records
        $archive_sql = "UPDATE accession_records SET archive = 'no' WHERE accession_no = ? AND book_id = ? AND book_category = ?";
        $archive_stmt = $conn->prepare($archive_sql);
        $archive_stmt->bind_param("sis", $accession_no, $book_id, $category);
        $archive_stmt->execute();

        $bookDeductionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies + 1 WHERE id = ?";
        $deductionStmt = $conn2->prepare($bookDeductionSql);
        $deductionStmt->bind_param("i", $book_id);
        $deductionStmt->execute();
    
        // Return success response
        echo 'Success';
    } else {
        // Return error message
        echo 'Error archiving book.';
    }
} else {
    // Return error if 'archive' is not set
    echo 'Invalid request.';
}


?>

<!DOCTYPE html>
<html lang="en">
<?php include 'admin_header.php'; ?>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <!-- Title Box -->
                <?php include './src/components/books.php'; ?>

                <?php if (isset($_GET['archive_success']) && $_GET['archive_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Book archived successfully!
                    </div>
                <?php endif; ?>

                <!-- Form for displaying accession numbers only -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <div class="w-full max-w-2xl mx-auto border border-black rounded-t-lg">
                        <div class="bg-red-800 text-white rounded-t-lg">
                            <h2 class="text-lg font-semibold p-4">Accession Numbers</h2>
                        </div>
                        <div class="p-6 bg-white rounded-b-lg shadow-md">



                            <form id="accessionNumbersForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                                <div class="grid grid-cols-3 items-start gap-4">
                                    <label for="accession_no" class="text-left">ACCESSION NUMBERS:</label>
                                    <div class="col-span-2 border rounded px-3 py-2 bg-gray-50 space-y-2">
                                        <?php
                                        // Query to fetch accession numbers based on book_id and category
                                        $accession_sql = "SELECT accession_no FROM accession_records WHERE book_id = ? AND book_category = ? AND archive != 'no'";
                                        $accession_stmt = $conn->prepare($accession_sql);

                                        if ($accession_stmt) {
                                            $accession_stmt->bind_param("is", $book_id, $category);
                                            $accession_stmt->execute();
                                            $accession_result = $accession_stmt->get_result();

                                            if ($accession_result->num_rows > 0) {
                                                // Display each accession number with an archive button
                                                while ($accession_row = $accession_result->fetch_assoc()) {
                                                    $accession_no = htmlspecialchars($accession_row['accession_no']);
                                                    echo "<div class='flex gap-2'>";
                                                    echo "<input type='text' name='accession_no[]' value='$accession_no' class='w-full border rounded px-2 py-1' readonly />";
                                                    echo "<button type='button' onclick='unarchiveAccession(\"$accession_no\")' class='px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600'>Unarchive</button>";
                                                    echo "</div>";
                                                }
                                            } else {
                                                echo "<p class='text-gray-500'>No accession numbers available.</p>";
                                            }
                                        } else {
                                            echo "<p class='text-red-500'>Error fetching accession numbers.</p>";
                                        }

                                        $accession_stmt->close();
                                        ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="./src/components/header.js"></script>

    <script>
    // Set a timeout to hide the alert after 4 seconds
    setTimeout(function() {
        var alertElement = document.getElementById('alert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 4000);

    // JavaScript function to handle the unarchive button
    function unarchiveAccession(accessionNo) {
        if (confirm('Are you sure you want to unarchive this accession number?')) {
            // Perform the AJAX request to unarchive the book and its related accession records
            var bookId = <?php echo json_encode($book_id); ?>; // Use PHP to get the current book_id
            var category = <?php echo json_encode($category); ?>; // Use PHP to get the current category

            // Create an XMLHttpRequest object
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Prepare the data to send
            var data = 'unarchive=true&book_id=' + bookId + '&category=' + category + '&accession_no=' + accessionNo;

            // Send the data
            xhr.send(data);

            // Handle the response
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Redirect to the current page with success message
                    window.location.href = window.location.href + '&unarchive_success=1';
                } else {
                    alert('Error unarchiving accession number.');
                }
            };
        }
    }
</script>



</body>
</html>
