<?php
session_start();
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

                <?php if (isset($_GET['replace_success']) && $_GET['replace_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Update successful!
                    </div>
                <?php endif; ?>

                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    This page displays all subjects requiring replacement books. It includes a setting to track whether a lost book has been replaced, ensuring that book replacements are properly documented and updated for efficient management.
                </div>

                <!-- Form for displaying accession numbers only -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <div class="w-full max-w-4xl mx-auto border border-black rounded-t-lg">
                        <div class="bg-red-800 text-white rounded-t-lg ">
                            <h2 class="text-lg font-semibold p-4">Accession Numbers</h2>
                        </div>
                        <div class="p-6 bg-white  rounded-b-lg shadow-md">


                            <form id="accessionNumbersForm" class="space-y-4" method="POST">
                                <div class="grid grid-cols-3 items-start  ">
                                    <label for="accession_no" class="text-center">ACCESSION NUMBERS:</label>
                                    <div class="col-span-2 border rounded px-3 py-2 bg-gray-50 space-y-2">
                                        <?php
                                        // Query to fetch accession numbers and their availability
                                        $accession_sql = "SELECT accession_no, available, borrowable FROM accession_records WHERE book_id = ? AND book_category = ? AND status = 'subject-for-replacement'";

                                        $accession_stmt = $conn->prepare($accession_sql);

                                        if ($accession_stmt) {
                                            $accession_stmt->bind_param("is", $book_id, $category);
                                            $accession_stmt->execute();
                                            $accession_result = $accession_stmt->get_result();

                                            if ($accession_result->num_rows > 0) {
                                                // Display each accession number with its own "Update" button
                                                while ($accession_row = $accession_result->fetch_assoc()) {
                                                    $accession_no = htmlspecialchars($accession_row['accession_no']);
                                                    $is_available = htmlspecialchars($accession_row['borrowable']) === 'yes' ? 'checked' : '';

                                                    echo "<div class='flex items-center gap-2'>";

                                                    // Display Accession Number
                                                    echo "<input type='text' name='accession_no[]' value='$accession_no' class='w-full border rounded px-2 py-1' readonly />";

                                                    // Checkbox for availability
                                                    echo "<label class='flex items-center gap-2'>";
                                                    echo "<input type='checkbox' name='available[$accession_no]' value='yes' $is_available />";
                                                    echo "Available to Borrow?";
                                                    echo "</label>";

                                                    // Individual Update Button
                                                    echo "<button
                                type='button'
                                onclick='updateStatus(\"$accession_no\", \"$category\", \"$book_id\")'
                                class='px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700'>
                                Replaced
                              </button>";

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

    <script>
        function updateStatus(accessionNo, category, bookId) {
            // Get the checkbox state for availability
            const checkbox = document.querySelector(`input[name="available[${accessionNo}]"]`);
            const isAvailable = checkbox && checkbox.checked ? 'yes' : 'no';

            // Confirm the action
            const confirmed = confirm(`Are you sure you want to update the status for accession no: ${accessionNo}?`);
            if (!confirmed) return;

            // Perform the AJAX request to update the database
            fetch('subject_update_accession_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accession_no: accessionNo,
                        available: isAvailable,
                        book_id: bookId,
                        category: category
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Refresh the page to show updated status
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the status.');
                });
        }
    </script>
    <script src="./src/components/header.js"></script>

    <script>
        // Set a timeout to hide the alert after 4 seconds
        setTimeout(function() {
            var alertElement = document.getElementById('alert');
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        }, 4000);


        // JavaScript function to handle the "Repair" button
    </script>



</body>

</html>