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

                <?php if (isset($_GET['repair_success']) && $_GET['repair_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Update successful!
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
                                        // Query to fetch accession numbers and repair descriptions based on book_id and category
                                        $accession_sql = "SELECT accession_no, book_condition, available, borrowable FROM accession_records WHERE book_id = ? AND book_category = ? AND repaired != 'yes'";
                                        $accession_stmt = $conn->prepare($accession_sql);

                                        if ($accession_stmt) {
                                            $accession_stmt->bind_param("is", $book_id, $category);
                                            $accession_stmt->execute();
                                            $accession_result = $accession_stmt->get_result();

                                            if ($accession_result->num_rows > 0) {
                                                // Display each accession number with a "Repair" button and repair description textarea
                                                while ($accession_row = $accession_result->fetch_assoc()) {
                                                    $accession_no = htmlspecialchars($accession_row['accession_no']);
                                                    $repair_description = htmlspecialchars($accession_row['book_condition']); // Get the repair description

                                                    // Check if the book is available to borrow
                                                    $is_available = ""; // Default value
                                                    if ($accession_row['borrowable'] == 'yes') {
                                                        $is_available = "checked"; // If the book is available, pre-check the checkbox
                                                    }

                                                    echo "<div class='flex flex-col gap-2'>";

                                                    // Display Accession Number
                                                    echo "<input type='text' name='accession_no[]' value='$accession_no' class='w-full border rounded px-2 py-1' readonly />";

                                                    // Repair Description Textarea - pre-fill with the existing description if available
                                                    echo "<textarea name='repair_description[]' data-accession-no='$accession_no' placeholder='Enter repair description here...' class='w-full border rounded px-2 py-1'>$repair_description</textarea>";

                                                    // Checkbox - Added here
                                                    echo "<label class='flex items-center'>";
                                                    echo "<input type='checkbox' name='available[$accession_no]' value='yes' $is_available />";
                                                    echo "Available to Borrow?"; // Label for the checkbox
                                                    echo "</label>";

                                                    // Repair Button
                                                    echo "<button type='button' onclick='updateStatus(\"$accession_no\", \"$repair_description\", \"$category\", \"$book_id\")' class='px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600'>Repaired</button>";

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

                            <script>
    // Define book_id and category from PHP variables passed from the server
    const bookId = "<?php echo $_GET['id']; ?>"; // Render book_id dynamically
    const category = "<?php echo $_GET['table']; ?>"; // Render category dynamically

    function updateStatus(accessionNo) {
        // Get the repair description dynamically
        const repairDescriptionElement = document.querySelector(
            `textarea[name="repair_description[]"][data-accession-no="${accessionNo}"]`
        );
        const repairDescription = repairDescriptionElement ? repairDescriptionElement.value.trim() : "";

        // Get the checkbox state for availability
        const checkbox = document.querySelector(`input[name="available[${accessionNo}]"]`);
        const isAvailable = checkbox && checkbox.checked ? 'yes' : 'no';

        // Prepare the payload data
        const payload = {
            accession_no: accessionNo,
            available: isAvailable,
            repair_description: repairDescription,
            book_id: bookId, // Use the dynamically rendered book_id
            category: category // Use the dynamically rendered category
        };

        // Combine confirmation message with payload details
        const confirmed = confirm(
            `Are you sure you want to update the status for accession no: ${accessionNo}?\n\n` +
            `Details:\n` +
            `Accession No: ${payload.accession_no}\n` +
            `Available: ${payload.available}\n` +
            `Repair Description: ${payload.repair_description}\n` +
            `Book ID: ${payload.book_id}\n` +
            `Category: ${payload.category}`
        );

        if (!confirmed) return;

        // Perform the AJAX request to update the database
        fetch('damage_update_accession_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
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


        // JavaScript function to handle the "Repair" button
        function markAsRepaired(accessionNo) {
            if (confirm('Are you sure you want to mark this book as being repaired?')) {
                // Get the corresponding repair description
                const repairDescription = document.querySelector(`textarea[name='repair_description[]']`).value;

                // Perform the AJAX request to mark the book as repaired
                var bookId = <?php echo json_encode($book_id); ?>; // Use PHP to get the current book_id
                var category = <?php echo json_encode($category); ?>; // Use PHP to get the current category

                // Create an XMLHttpRequest object
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Prepare the data to send
                var data = 'mark_as_repaired=true&book_id=' + bookId + '&category=' + category + '&accession_no=' + accessionNo + '&repair_description=' + repairDescription;

                // Send the data
                xhr.send(data);

                // Handle the response
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Redirect to the current page with success message
                        window.location.href = window.location.href + '&repair_success=1';
                    } else {
                        alert('Error marking the book as repaired.');
                    }
                };
            }
        }
    </script>



</body>

</html>