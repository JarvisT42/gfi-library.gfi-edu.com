<?php
session_start();
include '../connection2.php'; // Ensure you have your database connection
include '../connection.php'; // Ensure you have your database connection

// Check if `id` and `table` (category) exist in the URL
if (isset($_GET['id']) && isset($_GET['table'])) {
    $book_id = $_GET['id'];
    $category = $_GET['table'];
} else {
    // Redirect if no ID or table is provided
    echo "<script>window.location.href='books.php';</script>";
    exit;
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Archive Book
    // Archive Book
    // if (isset($_POST['archive'])) {
    //     // Check if there are any records with available != 'reserved'
    //     $check_availability_sql = "SELECT accession_no, available FROM accession_records WHERE book_id = ? AND book_category = ? AND available != 'reserved' AND archive != 'yes'";
    //     $check_availability_stmt = $conn->prepare($check_availability_sql);
    //     $check_availability_stmt->bind_param("is", $book_id, $category);
    //     $check_availability_stmt->execute();
    //     $check_availability_result = $check_availability_stmt->get_result();

    //     // Proceed if there are available records to archive
    //     if ($check_availability_result->num_rows > 0) {
    //         // Archive the book in the main table (executes once for all matching records)
    //         $archive_sql = "UPDATE accession_records SET archive = 'yes' WHERE a AND available != 'reserved'";
    //         $archive_stmt = $conn->prepare($archive_sql);
    //         $archive_stmt->bind_param("is", $book_id, $category);
    //         $archive_stmt->execute();

    //         // Get how many rows were affected by the update
    //         $affected_rows = $archive_stmt->affected_rows;

    //         // Output how many records were updated
    //         echo "Number of records archived: " . $affected_rows;

    //         // If you want to perform additional actions for each row updated
    //         if ($affected_rows > 0) {
    //             // Loop over each record and update the corresponding No_Of_Copies in the category table
    //             while ($row = $check_availability_result->fetch_assoc()) {
    //                 $bookDeductionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies - 1 WHERE id = ?";
    //                 $deductionStmt = $conn2->prepare($bookDeductionSql);
    //                 $deductionStmt->bind_param("i", $book_id);
    //                 $deductionStmt->execute();
    //             }
    //         }
    //     } else {

    //         $sql = "UPDATE `$category` SET archive = 'yes' WHERE id = ?";
    //         $stmt = $conn2->prepare($sql);
    //         $stmt->bind_param("i", $book_id);

    //         if ($stmt->execute()) {
    //             // Archive related accession records where available is not 'reserved'
    //             $archive_sql = "UPDATE accession_records SET archive = 'yes' WHERE book_id = ? AND book_category = ? AND available != 'reserved'";
    //             $archive_stmt = $conn->prepare($archive_sql);
    //             $archive_stmt->bind_param("is", $book_id, $category);
    //             $archive_stmt->execute();

    //             // Redirect to show success message
    //             header("Location: " . $_SERVER['PHP_SELF'] . "?id=$book_id&table=$category&archive_success=1");
    //             exit;
    //         } else {
    //             echo "<script>alert('Error archiving book.');</script>";
    //         }
    //     }
    // }




    // Archive individual accession number
    if (isset($_POST['archive_accession'])) {
        $accession_no = $_POST['archive_accession'];
        $archive_sql = "UPDATE accession_records SET archive = 'yes' WHERE accession_no = ?";
        $stmt = $conn->prepare($archive_sql);
        $stmt->bind_param("s", $accession_no);

        if ($stmt->execute()) {

            $bookDeductionSql = "UPDATE `$category` SET No_Of_Copies = No_Of_Copies - 1 WHERE id = ?";
            $deductionStmt = $conn2->prepare($bookDeductionSql);
            $deductionStmt->bind_param("i", $book_id);
            $deductionStmt->execute();


            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$book_id&table=$category&archive_accession_success=1");
            exit;
        } else {
            echo "<script>alert('Error archiving accession number.');</script>";
        }
    }


    if (isset($_POST['accession_no']) && isset($_POST['available_status'])) {
        $accession_no = $_POST['accession_no'];
        $available_status = $_POST['available_status'];

        // Update the available status in the database
        $update_sql = "UPDATE accession_records SET available = ? WHERE accession_no = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ss", $available_status, $accession_no);
        $stmt->execute();

        // Redirect to the same page after the update
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=$book_id&table=$category&update_accession_success=1");
        exit;
    }


    // Update book details
    // Update book details
    // Update book details
    // if (isset($_POST['update'])) {
    //     // Fetch other fields as before
    //     $call_number = htmlspecialchars($_POST['call_number']);
    //     $isbn = htmlspecialchars($_POST['isbn']);
    //     $department = htmlspecialchars($_POST['department']);
    //     $title = htmlspecialchars($_POST['book_title']);
    //     $author = htmlspecialchars($_POST['author']);
    //     $publisher = htmlspecialchars($_POST['publisher_name']);
    //     $no_of_copies = intval($_POST['book_copies']);
    //     $date_of_publication = htmlspecialchars($_POST['date_of_publication_copyright']);
    //     $subjects = htmlspecialchars($_POST['subject']);

    //     // Fetch borrowable status (either 'yes' or 'no' for each accession number)
    //     $cover_image = null;
    //     if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    //         $cover_image = file_get_contents($_FILES['image']['tmp_name']);
    //     }

    //     // Prepare SQL query
    //     $sql = $cover_image
    //         ? "UPDATE `$category` SET isbn = ?, Call_Number = ?, Department = ?, Title = ?, Author = ?, Publisher = ?, No_Of_Copies = ?, Date_Of_Publication_Copyright = ?, Subjects = ?, record_cover = ? WHERE id = ?"
    //         : "UPDATE `$category` SET isbn = ?, Call_Number = ?, Department = ?, Title = ?, Author = ?, Publisher = ?, No_Of_Copies = ?, Date_Of_Publication_Copyright = ?, Subjects = ? WHERE id = ?";

    //     // Log the data to error.txt
    //     $log_data = "SQL Query: $sql\nData:\nISBN: $isbn\nCall Number: $call_number\nDepartment: $department\nTitle: $title\nAuthor: $author\nPublisher: $publisher\nNo. of Copies: $no_of_copies\nDate of Publication: $date_of_publication\nSubjects: $subjects\nBook ID: $book_id\n";
    //     if ($cover_image) {
    //         $log_data .= "Cover Image: [binary data]\n";
    //     }

    //     // Log the borrowable status and accession numbers
    //     if (isset($_POST['accession_no'])) {
    //         foreach ($_POST['accession_no'] as $index => $accession_no) {
    //             $accession_no = htmlspecialchars(trim($accession_no));
    //             $borrowable_status = isset($_POST['borrowable_status'][$index]) && $_POST['borrowable_status'][$index] === 'yes' ? 'Yes' : 'No';
    //             $log_data .= "Accession No: $accession_no, Borrowable: $borrowable_status\n";
    //         }
    //     }

    //     // Write log to error.txt
    //     error_log($log_data, 3, "error.txt");

    //     // Prepare the SQL statement
    //     $stmt = $conn2->prepare($sql);
    //     if ($stmt) {
    //         $bind_params = $cover_image
    //             ? [$isbn, $call_number, $department, $title, $author, $publisher, $no_of_copies, $date_of_publication, $subjects, $cover_image, $book_id]
    //             : [$isbn, $call_number, $department, $title, $author, $publisher, $no_of_copies, $date_of_publication, $subjects, $book_id];

    //         $stmt->bind_param(str_repeat("s", count($bind_params)), ...$bind_params);
    //         if ($stmt->execute()) {
    //             // Save new accession numbers and handle "borrowable" status
    //             if (isset($_POST['accession_no'])) {
    //                 foreach ($_POST['accession_no'] as $index => $accession_no) {
    //                     $accession_no = htmlspecialchars(trim($accession_no));

    //                     // Correctly map the borrowable status for each accession number
    //                     $borrowable_status = isset($_POST['borrowable_status'][$index]) && $_POST['borrowable_status'][$index] === 'yes' ? 'Yes' : 'No';

    //                     // Check if accession number already exists in the database
    //                     $check_sql = "SELECT * FROM accession_records WHERE accession_no = ?";
    //                     $check_stmt = $conn->prepare($check_sql);
    //                     $check_stmt->bind_param("s", $accession_no);
    //                     $check_stmt->execute();
    //                     $check_result = $check_stmt->get_result();

    //                     if ($check_result->num_rows == 0 && !empty($accession_no)) {
    //                         // Insert new accession number
    //                         $insert_sql = "INSERT INTO accession_records (accession_no, call_number, book_id, book_category, archive, available) VALUES (?, ?, ?, ?, 'no', ?)";
    //                         $insert_stmt = $conn->prepare($insert_sql);
    //                         $insert_stmt->bind_param("ssiss", $accession_no, $call_number, $book_id, $category, $borrowable_status);
    //                         if (!$insert_stmt->execute()) {
    //                             // Log error if insert fails
    //                             error_log("Error inserting accession number: $accession_no\n" . $insert_stmt->error . "\n", 3, "error.txt");
    //                         }
    //                     } else {
    //                         // Update existing accession record with the borrowable status
    //                         $update_sql = "UPDATE accession_records SET available = ? WHERE accession_no = ?";
    //                         $update_stmt = $conn->prepare($update_sql);
    //                         $update_stmt->bind_param("ss", $borrowable_status, $accession_no);
    //                         if (!$update_stmt->execute()) {
    //                             // Log error if update fails
    //                             error_log("Error updating accession record: $accession_no\n" . $update_stmt->error . "\n", 3, "error.txt");
    //                         }
    //                     }
    //                 }
    //             }

    //             // Redirect to the same page with success message
    //             header("Location: " . $_SERVER['PHP_SELF'] . "?id=$book_id&table=$category&update_success=1");
    //             exit;
    //         } else {
    //             // Log error if update fails
    //             error_log("Error updating book details for book ID: $book_id\n" . $stmt->error . "\n", 3, "error.txt");
    //             echo "<script>alert('Error updating book details.');</script>";
    //         }
    //     } else {
    //         // Log error if prepare fails
    //         error_log("Error preparing SQL statement for book update.\n" . $conn2->error . "\n", 3, "error.txt");
    //     }
    // }
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

                <?php if (isset($_GET['update_success']) && $_GET['update_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Update successful!
                    </div>
                <?php endif; ?>


                <?php if (isset($_GET['duplicate']) && $_GET['duplicate'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        duplicate then what accession number duplicate
                    </div>
                <?php endif; ?>


                <?php if (isset($_GET['archive_success']) && $_GET['archive_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Book archived successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['archive_accession_success']) && $_GET['archive_accession_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Accession number archived successfully!
                    </div>
                <?php endif; ?>





                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <ul class="flex flex-wrap gap-2 p-5 border border-dashed rounded-md w-full">
                        <li><a class="px-4 py-2" href="books.php">All</a></li>
                        <br>
                        <li><a class="px-4 py-2" href="add_books.php">Add Books</a></li> <br>

                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="edit_records.php">Edit Records</a></li>
                        <br>
                        <li><a class="px-4 py-2" href="damage.php">Damage Books</a></li>
                        <br>
                        <li><a class="px-4 py-2 " href="subject_for_replacement.php">Subject For Replacement</a></li>


                        <br>


                    </ul>
                </div>

                <!-- Main Content Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 ">
                    <div class="w-full max-w-5xl mx-auto border border-black  rounded-t-lg">
                        <div class="bg-red-800 text-white rounded-t-lg">
                            <h2 class="text-lg font-semibold p-4">Edit Book Details</h2>
                        </div>

                        <?php
                        // Your database query to check the book's availability
                        $accession_sql = "SELECT accession_no, available FROM accession_records WHERE book_id = ? AND book_category = ? AND archive != 'yes'";
                        $accession_stmt = $conn->prepare($accession_sql);
                        $accession_stmt->bind_param("is", $book_id, $category);
                        $accession_stmt->execute();
                        $accession_stmt->store_result(); // Store result for checking availability

                        // Assuming that you fetch the result here
                        $accession_stmt->bind_result($accession_no, $available);
                        $accession_stmt->fetch();

                        // Check if the book is available or reserved
                        if ($available === 'reserved' || $available === 'borrowed') {
                            // If the book is reserved or borrowed, display the message
                            echo '<div class="p-6 bg-red-200  shadow-md">
                                                <p>This book is currently borrowed and cannot be edited at the moment.</p>
                                            </div>';
                        } else {
                            // If the book is available for editing, proceed with the normal logic
                            // Add your form or book editing logic here
                        }
                        ?>


                        <div class="p-6 bg-white rounded-b-lg shadow-md">

                            <?php
                            // Fetch the book details if the record exists
                            $sql = "SELECT * FROM `$category` WHERE id = ? AND archive != 'yes'";
                            $stmt = $conn2->prepare($sql);
                            $stmt->bind_param("i", $book_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $accession_sql = "SELECT available FROM accession_records WHERE book_id = ? AND book_category = ? AND archive != 'yes'";
                            $accession_stmt = $conn->prepare($accession_sql);
                            $accession_stmt->bind_param("is", $book_id, $category);
                            $accession_stmt->execute();
                            $accession_result = $accession_stmt->get_result();

                            // Assuming that the book is reserved if any of the accession records have "reserved" as availability
                            $is_reserved = false;
                            if ($accession_result->num_rows > 0) {
                                while ($row = $accession_result->fetch_assoc()) {
                                    if ($row['available'] === 'reserved' || $row['available'] === 'borrowed') {
                                        $is_reserved = true;
                                        break;
                                    }
                                }
                            }

                            if ($result->num_rows > 0) {
                                $book = $result->fetch_assoc();
                                $isbn = $book['isbn'];
                                $call_number = $book['Call_Number'];
                                $department = $book['Department'];
                                $title = $book['Title'];
                                $author = $book['Author'];
                                $publisher = $book['Publisher'];
                                $no_of_copies = $book['No_Of_Copies'];
                                $date_of_publication = $book['Date_Of_Publication_Copyright'];
                                $date_encoded = $book['Date_Encoded'];
                                $subjects = $book['Subjects'];
                                $image_path = $book['image_path'];
                                $available_to_borrow = $book['Available_To_Borrow'];
                            } else {
                                echo "<script> window.location.href='books.php';</script>";
                                exit;
                            }
                            ?>
                            <form id="editBookForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                                <!-- Category -->

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="category" class="text-left">Category</label>
                                    <input id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" class="col-span-2 border rounded px-3 py-2 bg-gray-300 cursor-not-allowed" readonly />
                                </div>


                                <!-- Tracking ID -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="isbn" class="text-left">ISBN</label>
                                    <input id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>

                                <!-- Call Number -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="call_number" class="text-left">Call Number</label>
                                    <input id="call_number" name="call_number" value="<?php echo htmlspecialchars($call_number); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>

                                <!-- Department -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="department" class="text-left">Department</label>
                                    <input id="department" name="department" value="<?php echo htmlspecialchars($department); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>
                                <!-- Book Title -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="book_title" class="text-left">Book Title</label>
                                    <input id="book_title" name="book_title" value="<?php echo htmlspecialchars($title); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>

                                <!-- Author -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="author" class="text-left">Author</label>
                                    <input id="author" name="author" value="<?php echo htmlspecialchars($author); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>

                                <!-- Date of Publication -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="date_of_publication_copyright" class="text-left">Year of Publication (Copyright)</label>
                                    <select id="date_of_publication_copyright" name="date_of_publication_copyright" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'disabled' : ''; ?>>
                                        <option value="">Select Year</option>
                                        <?php
                                        $current_year = date("Y"); // Get the current year
                                        for ($year = $current_year; $year >= 2000; $year--) {
                                            // Check if the current year is selected
                                            $selected = ($year == $date_of_publication) ? 'selected' : '';
                                            echo "<option value=\"$year\" $selected>$year</option>";
                                        }
                                        ?>
                                    </select>


                                </div>





                                <!-- Number of Copies -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="book_copies" class="text-left">BOOK COPIES:</label>
                                    <div class="col-span-2 flex items-center gap-2">
                                        <!-- Minus Button -->
                                        <button id="decrementBtn" type="button" class="bg-blue-500 text-white p-2 rounded">
                                            <i class="fas fa-minus"></i> <!-- Font Awesome Minus Icon -->
                                        </button>

                                        <!-- Plus Button -->
                                        <button id="incrementBtn" type="button" class="bg-blue-500 text-white p-2 rounded">
                                            <i class="fas fa-plus"></i> <!-- Font Awesome Plus Icon -->
                                        </button>

                                        <!-- Display Book Copies (Read-only) -->
                                        <input id="book_copies" name="book_copies" type="text" value="<?php echo htmlspecialchars($no_of_copies); ?>" class="border rounded px-3 py-2 text-center" readonly />
                                    </div>
                                </div>

                                <!-- Accession Numbers with Archive Button -->
                                <!-- Accession Numbers -->
                                <div class="grid grid-cols-3 items-start gap-4">
                                    <label for="accession_no" class="text-left">ACCESSION NUMBERS:</label>
                                    <div class="col-span-2 border rounded px-3 py-2 bg-gray-50 space-y-2" id="accessionNumberContainer">
                                        <?php
                                        include '../connection.php';

                                        // Query to fetch existing accession numbers and their availability status
                                        $accession_sql = "SELECT accession_no, available FROM accession_records WHERE book_id = ? AND book_category = ? AND archive != 'yes'";
                                        $accession_stmt = $conn->prepare($accession_sql);
                                        $accession_stmt->bind_param("is", $book_id, $category);
                                        $accession_stmt->execute();
                                        $accession_result = $accession_stmt->get_result();

                                        $non_reserved_accessions = []; // Array to hold non-reserved accession numbers

                                        if ($accession_result->num_rows > 0) {
                                            while ($accession_row = $accession_result->fetch_assoc()) {
                                                $accession_no = htmlspecialchars($accession_row['accession_no']);
                                                $available = htmlspecialchars($accession_row['available']);

                                                // Check if the book is reserved (borrowed)
                                                $is_borrowed = ($available == 'reserved' || $available == 'borrowed') ? true : false;

                                                // Add non-borrowed (available) accession numbers to the array
                                                if (!$is_borrowed) {
                                                    $non_reserved_accessions[] = $accession_no;
                                                }

                                                echo "<div class='flex gap-2'>";

                                                // Input field with conditional readonly
                                                $readonly = $is_borrowed ? 'readonly' : '';
                                                echo "<input type='hidden' name='original_accession_no[]' value='$accession_no' />";
                                                echo "<input type='text' name='accession_no[]' value='$accession_no' class='w-full border rounded px-2 py-1' $readonly />";

                                                // Disable buttons if the book is borrowed (reserved)
                                                $button_disabled = $is_borrowed ? 'disabled' : '';

                                                // Archive button
                                                echo "<button type='button' onclick='archiveAccession(\"$accession_no\")' class='px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600' $button_disabled>Archive</button>";

                                                // 'To Borrow' button
                                                if ($available == 'yes') {
                                                    // Enable the checkbox only if available is 'yes' and check it by default
                                                    echo "<input type='checkbox' id='borrowable_$accession_no' name='borrowable[]' value='$accession_no' onclick='markAsBorrowed(\"$accession_no\")' class='mr-2' checked />";
                                                    echo "<label for='borrowable_$accession_no' class='text-blue-500 hover:text-blue-600'>Set as Borrowable</label>";
                                                }
                                                // 'To Borrow' button

                                                else if ($available == 'borrowed' ||  $available == 'reserved') {
                                                    // Disable the checkbox and keep it checked for borrowed books
                                                    echo "<input type='checkbox' id='borrowable_$accession_no' name='borrowable[]' value='$accession_no' disabled checked class='mr-2' />";
                                                    echo "<label for='borrowable_$accession_no' class='text-gray-400 cursor-not-allowed'>Set as Borrowable</label>";
                                                } else {
                                                    // Checkbox for books that are not available
                                                    echo "<input type='checkbox' id='borrowable_$accession_no' name='borrowable[]' value='$accession_no' onclick='markAsBorrowed(\"$accession_no\", this.checked)' class='mr-2' />";
                                                    echo "<label for='borrowable_$accession_no' class='text-gray-400 cursor-not-allowed'>Set as Borrowable</label>";
                                                }






                                                echo "</div>";

                                                // Display "This book is currently borrowed" message if borrowed
                                                if ($is_borrowed) {
                                                    echo "<p class='text-red-600'>This book is currently borrowed</p>";
                                                }
                                            }
                                        } else {
                                            echo "<p class='text-gray-500'>No accession numbers available.</p>";
                                        }
                                        ?>

                                        <!-- Hidden JSON variable containing non-borrowed accession numbers -->
                                        <script>
                                            var nonReservedAccessions = <?php echo json_encode($non_reserved_accessions); ?>;
                                        </script>
                                    </div>
                                </div>




                                <div id="accessionNumberContainer" class="space-y-2"></div>
                                <div id="warningContainer" class="text-red-600 mt-2"></div>
                                <div id="warningContainer" class="text-red-600 hidden"></div>

                                <!-- Publisher Name -->




                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="publisher_name" class="text-left">PUBLISHER NAME:</label>
                                    <input id="publisher_name" name="publisher_name" value="<?php echo htmlspecialchars($publisher); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>

                                <!-- Subject -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="subject" class="text-left">SUBJECT:</label>
                                    <input id="subject" name="subject" value="<?php echo htmlspecialchars($subjects); ?>" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'readonly' : ''; ?> />
                                </div>

                                <!-- Image Upload -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="image_preview" class="text-left">CURRENT IMAGE:</label>
                                    <div class="col-span-2 border rounded px-3 py-2">
                                        <img id="imagePreview" src="<?php echo htmlspecialchars($image_path); ?>" alt="Book Cover" class="w-28 h-40 border-2 border-gray-400 rounded-lg object-cover" style="max-width: 100%; max-height: 200px;" />




                                    </div>
                                </div>
                                <!-- Upload New Image Section -->
                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="image" class="text-left">UPLOAD NEW IMAGE:</label>
                                    <input type="file" id="image" name="image" accept="image/*" class="col-span-2 border rounded px-3 py-2 <?php echo ($is_reserved) ? 'bg-gray-300 cursor-not-allowed' : ''; ?>" <?php echo ($is_reserved) ? 'disabled' : ''; ?> />
                                </div>




                                <!-- Status -->


                                <!-- Submit Button -->
                                <div class="flex justify-end gap-4">

                                    <button type="submit" name="archive" value="1" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700"
                                        onclick="return confirmArchive();">
                                        Archive Book
                                    </button>



                                    <!-- Save Button -->
                                    <button type="submit" name="update" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" id="saveBtn">
                                        Save Changes
                                    </button>
                                </div>
                            </form>


                            <script>
                                // Initialize the initial value of book copies
                                const initialCopies = parseInt(document.getElementById("book_copies").value, 10) || 0;

                                // Increment Book Copies when plus button is clicked
                                document.getElementById("incrementBtn").addEventListener("click", function() {
                                    const bookCopiesInput = document.getElementById("book_copies");
                                    let currentValue = parseInt(bookCopiesInput.value) || 0; // Get current value or 0 if NaN
                                    currentValue += 1; // Increment the value
                                    bookCopiesInput.value = currentValue; // Set the new value

                                    // Trigger input event to add/remove accession fields
                                    updateAccessionFields(currentValue);
                                });

                                // Decrement Book Copies when minus button is clicked (but not below initial value)
                                document.getElementById("decrementBtn").addEventListener("click", function() {
                                    const bookCopiesInput = document.getElementById("book_copies");
                                    let currentValue = parseInt(bookCopiesInput.value) || 0; // Get current value or 0 if NaN
                                    if (currentValue > initialCopies) {
                                        currentValue -= 1; // Decrement the value
                                        bookCopiesInput.value = currentValue; // Set the new value

                                        // Trigger input event to add/remove accession fields
                                        updateAccessionFields(currentValue);
                                    }
                                });

                                // Function to update Accession Number inputs based on Book Copies value
                                document.getElementById("book_copies").addEventListener("input", function() {
                                    const requiredCount = parseInt(this.value, 10) || 0; // Value of Book Copies
                                    updateAccessionFields(requiredCount);
                                });

                                // Function to update the accession fields dynamically
                                function updateAccessionFields(requiredCount) {
                                    const accessionContainer = document.getElementById("accessionNumberContainer");
                                    const existingInputs = accessionContainer.querySelectorAll("input[name='accession_no[]']");
                                    const currentCount = existingInputs.length; // Current number of inputs

                                    // Add new fields if needed
                                    if (requiredCount > currentCount) {
                                        for (let i = currentCount + 1; i <= requiredCount; i++) {
                                            const accessionDiv = document.createElement("div");
                                            accessionDiv.classList.add("flex", "gap-2");

                                            // Accession Number Input
                                            const input = document.createElement("input");
                                            input.type = "text";
                                            input.name = "accession_no[]";
                                            input.placeholder = `Accession Number ${i}`;
                                            input.classList.add("w-full", "border", "rounded", "px-2", "py-1");

                                            // Hidden Original Accession Input (for new inputs, set empty)
                                            const hiddenOriginalInput = document.createElement("input");
                                            hiddenOriginalInput.type = "hidden";
                                            hiddenOriginalInput.name = "original_accession_no[]";
                                            hiddenOriginalInput.value = ""; // Default to empty for new fields

                                            // Borrowable Checkbox
                                            const checkboxDiv = document.createElement("div");
                                            checkboxDiv.classList.add("flex", "items-center");

                                            const checkbox = document.createElement("input");
                                            checkbox.type = "checkbox";
                                            checkbox.name = "borrowable[]"; // To submit the checkbox values as an array
                                            checkbox.id = `borrowable_${i}`;
                                            checkbox.classList.add("mr-2");

                                            const checkboxLabel = document.createElement("label");
                                            checkboxLabel.setAttribute("for", `borrowable_${i}`);
                                            checkboxLabel.classList.add("text-blue-500", "hover:text-blue-600");
                                            checkboxLabel.textContent = "Set as Borrowable";

                                            checkboxDiv.appendChild(checkbox);
                                            checkboxDiv.appendChild(checkboxLabel);

                                            // Append the inputs to the container
                                            accessionDiv.appendChild(hiddenOriginalInput); // Hidden input for original value
                                            accessionDiv.appendChild(input); // Visible input for new value
                                            accessionDiv.appendChild(checkboxDiv);
                                            accessionContainer.appendChild(accessionDiv);
                                        }
                                    }
                                    // Remove excess fields if needed
                                    else if (requiredCount < currentCount) {
                                        for (let i = currentCount; i > requiredCount; i--) {
                                            accessionContainer.removeChild(accessionContainer.lastChild);
                                        }
                                    }
                                }

                                // Initialize the inputs on page load based on the current value of book_copies
                                document.addEventListener("DOMContentLoaded", function() {
                                    updateAccessionFields(initialCopies);
                                });

                                // Save Button Click Event
                                document.getElementById("saveBtn").addEventListener("click", function(event) {
                                    event.preventDefault(); // Prevent default form submission

                                    const accessionInputs = document.querySelectorAll("input[name='accession_no[]']");
                                    const originalAccessionInputs = document.querySelectorAll("input[name='original_accession_no[]']");
                                    const borrowableCheckboxes = document.querySelectorAll("input[name='borrowable[]']");
                                    let allFieldsFilled = true;

                                    // Check if all accession fields are filled
                                    accessionInputs.forEach(input => {
                                        if (input.value.trim() === "") {
                                            allFieldsFilled = false;
                                        }
                                    });

                                    if (!allFieldsFilled) {
                                        alert("Please input accession number for all fields.");
                                        return;
                                    }

                                    // Prepare the data as an object
                                    const data = {
                                        book_id: bookId,
                                        category: category,
                                        isbn: document.getElementById("isbn").value.trim(),
                                        call_number: document.getElementById("call_number").value.trim(),
                                        department: document.getElementById("department").value.trim(),
                                        book_title: document.getElementById("book_title").value.trim(),
                                        author: document.getElementById("author").value.trim(),
                                        date_of_publication_copyright: document.getElementById("date_of_publication_copyright").value.trim(),
                                        book_copies: document.getElementById("book_copies").value.trim(),
                                        publisher_name: document.getElementById("publisher_name").value.trim(),
                                        subject: document.getElementById("subject").value.trim(),
                                        accession_data: []
                                    };

                                    // Add accession data
                                    accessionInputs.forEach((input, index) => {
                                        const accessionNo = input.value.trim();
                                        const originalAccessionNo = originalAccessionInputs[index]?.value.trim() || "";
                                        const borrowableStatus = borrowableCheckboxes[index]?.checked ? 'yes' : 'no';
                                        data.accession_data.push({
                                            original_accession_no: originalAccessionNo,
                                            new_accession_no: accessionNo,
                                            borrowable: borrowableStatus
                                        });
                                    });

                                    // Create FormData for file upload
                                    const formData = new FormData();
                                    formData.append("data", JSON.stringify(data)); // Encode the main data as JSON

                                    // Add image file if provided
                                    const imageFile = document.getElementById("image").files[0];
                                    if (imageFile) {
                                        formData.append("image", imageFile);
                                    }

                                    // Alert the data that will be sent (excluding the binary file)
                                    const imageInfo = imageFile ?
                                        `Image uploaded:\n- Name: ${imageFile.name}\n- Size: ${imageFile.size} bytes\n- Type: ${imageFile.type}` :
                                        "No image uploaded.";
                                    alert("Data to be sent:\n" + JSON.stringify(data, null, 2) + "\n\n" + imageInfo);

                                    // Send data via Fetch API
                                    fetch('edit_books_update2.php', {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(response => response.json())
                                        .then(responseData => {
                                            if (responseData.success) {
                                                alert("Book information updated successfully.");
                                            } else {
                                                alert("Error updating book information: " + responseData.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert("There was an error while updating the book information.");
                                        });
                                });
                            </script>

                            <script>
                                // JavaScript function to show the alert with non-reserved accession numbers
                                function confirmArchive() {
                                    if (nonReservedAccessions.length > 0) {
                                        var message = 'The following accession numbers are available for archiving:\n\n';
                                        message += nonReservedAccessions.join('\n');
                                        var confirmed = confirm(message + '\n\nAre you sure you want to archive these books?');
                                        if (confirmed) {
                                            // Send the non-reserved accession numbers to the backend for archiving
                                            archiveBooks(nonReservedAccessions);
                                        }
                                        return confirmed;
                                    } else {
                                        alert('No available accession numbers for archiving.');
                                        return false;
                                    }
                                }

                                // Function to send data to backend for archiving
                                const bookId = "<?php echo $book_id; ?>";
                                const category = "<?php echo $category; ?>";

                                function archiveBooks(accessionNumbers) {
                                    const data = {
                                        accession_numbers: accessionNumbers // Send array of accession numbers
                                    };

                                    // Construct the URL with 'id' and 'table' (category) parameters
                                    const url = `edit_books_update.php?id=${bookId}&table=${category}`;

                                    // Alert the Book ID and Category
                                    alert("Book ID: " + bookId + "\nCategory: " + category);

                                    // Send data to the PHP backend using fetch
                                    fetch(url, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify(data), // Convert data object to JSON
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                // Log the response if not OK
                                                return Promise.reject('Failed to connect to the server: ' + response.statusText);
                                            }
                                            return response.json();
                                        }) // Expecting a JSON response from PHP
                                        .then(result => {
                                            if (result.success) {
                                                alert('Books archived successfully');
                                                location.reload(); // Reload the page or redirect as needed
                                            } else {
                                                alert('Error: ' + result.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('An error occurred. Please try again.');
                                        });
                                }
                            </script>
                            <script>
                                // Disable Archive Button and Show Borrowed Message
                                document.querySelectorAll('.accession-item').forEach(function(item) {
                                    var availableStatus = item.dataset.available;
                                    var archiveButton = item.querySelector('.archive-button');
                                    var borrowedMessage = item.querySelector('.borrowed-message');

                                    if (availableStatus === 'reserved') {
                                        archiveButton.disabled = true;
                                        borrowedMessage.style.display = 'block'; // Show "This book is currently borrowed"
                                    }
                                });




                                // Function to preview the image after selection
                                function previewImage(event) {
                                    const file = event.target.files[0];
                                    const reader = new FileReader();

                                    reader.onload = function() {
                                        // Set the image preview source to the loaded file
                                        const imagePreview = document.getElementById('imagePreview');
                                        imagePreview.src = reader.result;
                                    };

                                    if (file) {
                                        reader.readAsDataURL(file);
                                    }
                                }
                            </script>

                            <script>
                                document.addEventListener("input", function(event) {
                                    if (event.target.name === "accession_no[]") {
                                        const accessionNo = event.target.value.trim();
                                        const warningContainer = document.getElementById("warningContainer");

                                        if (accessionNo) {
                                            // Make an AJAX request to validate the accession number
                                            fetch("check_duplicate_accession.php", {
                                                    method: "POST",
                                                    headers: {
                                                        "Content-Type": "application/x-www-form-urlencoded",
                                                    },
                                                    body: `accession_no=${encodeURIComponent(accessionNo)}`,
                                                })
                                                .then((response) => response.json())
                                                .then((data) => {
                                                    if (data.exists) {
                                                        // Display a warning if the accession number exists
                                                        warningContainer.textContent = `Accession number ${accessionNo} already exists.`;
                                                        warningContainer.classList.remove("hidden");
                                                    } else {
                                                        // Clear the warning if the accession number is unique
                                                        warningContainer.textContent = "";
                                                        warningContainer.classList.add("hidden");
                                                    }
                                                })
                                                .catch((error) => {
                                                    console.error("Error validating accession number:", error);
                                                });
                                        } else {
                                            // Clear the warning if the input is empty
                                            warningContainer.textContent = "";
                                            warningContainer.classList.add("hidden");
                                        }
                                    }
                                });
                            </script>
                            <script>
                                function markAsBorrowed(accession_no, isChecked) {
                                    const availableStatus = isChecked ? 'yes' : 'no';

                                    // Ask for confirmation before updating
                                    if (confirm(`Are you sure you want to set this book as ${availableStatus}?`)) {
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = ''; // Current page

                                        const inputAccession = document.createElement('input');
                                        inputAccession.type = 'hidden';
                                        inputAccession.name = 'accession_no';
                                        inputAccession.value = accession_no;

                                        const inputStatus = document.createElement('input');
                                        inputStatus.type = 'hidden';
                                        inputStatus.name = 'available_status';
                                        inputStatus.value = availableStatus;

                                        form.appendChild(inputAccession);
                                        form.appendChild(inputStatus);
                                        document.body.appendChild(form);
                                        form.submit();
                                    } else {
                                        // If the user clicks "Cancel", reset the checkbox state to its original state
                                        document.getElementById(`borrowable_${accession_no}`).checked = !isChecked;
                                    }
                                }
                            </script>


                            <script>
                                function archiveAccession(accessionNo) {
                                    if (confirm('Are you sure you want to archive this accession number?')) {
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = '';

                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'archive_accession';
                                        input.value = accessionNo;

                                        form.appendChild(input);
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                }
                            </script>
                            <script>

                            </script>
                            <?php
                            // PHP code to handle the form submission

                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="./src/components/header.js"></script>

    <script>
        // Set a timeout to hide the alert after 3 seconds (3000 ms)s
        setTimeout(function() {
            var alertElement = document.getElementById('alert');
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        }, 4000);
    </script>

</body>

</html>