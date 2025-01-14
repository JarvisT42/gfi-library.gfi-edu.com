<?php
session_start();
include '../connection.php'; // Ensure you have your database connection
include '../connection2.php'; // Ensure you have your database connection









if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}



// Check if student_id or faculty_id is set in the query parameters


// Check if student_id or faculty_id is set in the query parameters
if (isset($_GET['student_id']) || isset($_GET['faculty_id'])) {
    $isStudent = isset($_GET['student_id']);
    $user_id = $isStudent ? htmlspecialchars($_GET['student_id']) : htmlspecialchars($_GET['faculty_id']);
    $user_type = $isStudent ? 'student' : 'faculty';



    // Fetch the category, book_id, and user details based on student_id or faculty_id
    $categoryQuery = "
        SELECT a.Category, a.book_id, a.Date_To_Claim
        FROM borrow AS a
        WHERE " . ($isStudent ? "a.student_id" : "a.faculty_id") . " = ? AND status = 'pending'";

    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param('i', $user_id); // Assuming user_id is an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC) ?: [];

    // Fetch user details for full name
    $userQuery = "
        SELECT First_Name, Middle_Initial, Last_Name
        FROM " . ($isStudent ? "students" : "faculty") . "
        WHERE " . ($isStudent ? "Student_Id" : "Faculty_Id") . " = ?";

    $stmtUser = $conn->prepare($userQuery);
    $stmtUser->bind_param('i', $user_id);
    $stmtUser->execute();
    $userResult = $stmtUser->get_result();

    if ($userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        $fullName = $userRow['First_Name'] . ' ' . $userRow['Middle_Initial'] . ' ' . $userRow['Last_Name'];
    } else {
        $fullName = 'Unknown ' . ucfirst($user_type); // Fallback if no user found
    }
    $stmtUser->close();
    $stmt->close();
} else {
    echo "No student or faculty ID provided.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ready_to_claim'])) {
    // Capture submitted form data
    $selected_books = $_POST['selected_books'] ?? [];
    $accession_numbers = $_POST['accession_no'] ?? [];
    $user_type = $_POST['user_type'] ?? ''; // "student" or "faculty"
    $user_id = $_POST['user_id'] ?? null;

    // Validate inputs
    if (empty($selected_books) || empty($user_id)) {
        echo "<script>alert('No books were selected or no user ID provided.');</script>";
        exit();
    }

    // Fetch the latest fines_id from the library_fines table
    $sql_fines = "SELECT fines_id FROM library_fines ORDER BY fines_id DESC LIMIT 1";
    $result_fines = $conn->query($sql_fines);

    if ($result_fines->num_rows > 0) {
        $fines = $result_fines->fetch_assoc();
        $fines_id = $fines['fines_id']; // Get the latest fines_id
    } else {
        echo "<script>alert('No fines record found.');</script>";
        exit();
    }

    $issued_date = date('Y-m-d'); // Get the current date for issuance
    $due_date = date('Y-m-d', strtotime('+1 week')); // Set the due date to 1 week from now

    // SQL query to update the borrow table
    $update_borrow_query = "UPDATE borrow
        SET status = 'ready_to_claim', accession_no = ?, Issued_Date = ?, due_date = ?, fines_id = ?
        WHERE {$user_type}_id = ? AND book_id = ? AND Category = ? AND status = 'pending'";

    // Prepare the statement
    if (!$stmt = $conn->prepare($update_borrow_query)) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    // Loop through selected books
    foreach ($selected_books as $book_info) {
        list($book_id, $category) = explode('|', $book_info);
        $book_id = (int) $book_id; // Ensure book_id is an integer
        $category = htmlspecialchars($category); // Sanitize category

        // Get the corresponding accession number
        $accession_no = $accession_numbers[$book_id] ?? null;

        if ($accession_no) {
            // Bind parameters and execute the query
// Bind parameters and execute the query
$stmt->bind_param("sssssss", $accession_no, $issued_date, $due_date, $fines_id, $user_id, $book_id, $category);
            if (!$stmt->execute()) {
                echo "Error updating borrow table: " . $stmt->error;
            }
        } else {
            echo "<script>alert('Accession number not found for book ID $book_id');</script>";
        }
    }

    // Close the statement
    $stmt->close();

    // Success message and redirect
    header("Location: " . $_SERVER['PHP_SELF'] . "?{$user_type}_id={$user_id}&update_success=1");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reason_action']) && $_POST['reason_action'] === 'not_available') {
    // Decode JSON data from form
    $selected_books = json_decode($_POST['selected_books'], true) ?? [];
    $accession_numbers = json_decode($_POST['accession_numbers'], true) ?? [];
    $reason = htmlspecialchars($_POST['reason'] ?? '');

    // Retrieve user information
    $userId = $_POST['user_id'] ?? null;
    $userColumn = $_POST['user_type'] ?? ''; // E.g., 'student_id' or 'faculty_id'

    // Validate inputs
    if (empty($selected_books) || empty($userId) || empty($userColumn)) {
        echo "<script>alert('No books were selected or no user ID provided.');</script>";
        exit();
    }

    foreach ($selected_books as $book_info) {
        // Parse book information (book_id and category)
        if (strpos($book_info, '|') === false) {
            continue; // Skip invalid data
        }

        list($book_id, $category) = explode('|', $book_info);
        $book_id = (int)$book_id; // Ensure book_id is an integer
        $category = htmlspecialchars($category); // Sanitize category

        // Update borrow table
        $update_borrow_query = "
            UPDATE borrow
            SET status = 'failed-to-claim', reason_for_failed_to_claim = ?
            WHERE {$user_type}_id = ?  AND book_id = ? AND Category = ? AND status = 'pending'";

        if ($stmt = $conn->prepare($update_borrow_query)) {
            $stmt->bind_param("siss", $reason, $userId, $book_id, $category);
            if (!$stmt->execute()) {
                echo "<script>alert('Error updating borrow table: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing borrow table query.');</script>";
        }

        // Update accession_records table if accession number exists
        if (isset($accession_numbers[$book_id])) {
            $accession_no = $accession_numbers[$book_id];
            $update_accession_query = "
                UPDATE accession_records
                SET status = 'failed-to-claim', available = 'yes'
                WHERE accession_no = ? AND borrower_id = ? AND available = 'reserved'";

            if ($stmt = $conn->prepare($update_accession_query)) {
                $stmt->bind_param("ss", $accession_no, $userId);
                if (!$stmt->execute()) {
                    echo "<script>alert('Error updating accession records: " . $stmt->error . "');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Error preparing accession records query.');</script>";
            }
        }
    }

   // Success message and redirect
   header("Location: " . $_SERVER['PHP_SELF'] . "?{$user_type}_id={$user_id}&update_success=1");
   exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Request</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>

    <style>
        .active-book-request {
            background-color: #f0f0f0;
            color: #000;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>
    <main id="content">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="bg-gray-100 p-6 w-full mx-auto">
                    <div class="bg-white p-4 shadow-sm rounded-lg mb-2">


                <?php if (isset($_GET['update_success']) && $_GET['update_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Update successful!
                    </div>
                <?php endif; ?>




                        <div class="bg-gray-100 p-2 flex justify-between items-center">
                            <div class="pl-10">
                                <h1 class="m-0 "><?php echo ucfirst($user_type); ?> Name: <?php echo $fullName; ?></h1>
                            </div>

                        </div>

                        <?php if (!empty($books)): ?>
                            <?php
                            // Group books by Date_To_Claim
                            $grouped_books = [];
                            foreach ($books as $book) {
                                $date_to_claim = htmlspecialchars($book['Date_To_Claim']);
                                $grouped_books[$date_to_claim][] = $book;
                            }
                            ?>

                            <form id="book-request-form" class="space-y-6" method="POST" action="" onsubmit="return validateDueDate()">

                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">

                                <?php foreach ($grouped_books as $date => $books_group): ?>
                                    <div class="bg-blue-200 p-4 rounded-lg">
                                        <div class="bg-blue-200 rounded-lg flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-white">Date to Claim: <?php echo $date; ?></h3>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="select-all-<?php echo $date; ?>" class="select-all-checkbox ml-2" onclick="toggleSelectAll('<?php echo $date; ?>')">
                                                <label for="select-all-<?php echo $date; ?>" class="ml-1 text-sm">Select All</label>
                                            </div>
                                        </div>

                                        <?php foreach ($books_group as $index => $book): ?>
                                            <?php
                                            $category = $book['Category'];
                                            $book_id = $book['book_id'];
                                            $titleQuery = "SELECT * FROM `$category` WHERE id = ?";
                                            $stmt2 = $conn2->prepare($titleQuery);
                                            $stmt2->bind_param('i', $book_id);
                                            $stmt2->execute();
                                            $result = $stmt2->get_result();

                                            $title = 'Unknown Title';
                                            $author = 'Unknown Author';
                                            $publication_date = 'Unknown Publication Date';
                                            $record_cover = null;

                                            if ($row = $result->fetch_assoc()) {
                                                $title = $row['title'];
                                                $author = $row['author'];
                                                $publication_date = $row['date_of_publication_copyright'];
                                                $record_cover = $row['image_path'];
                                            }
                                            $stmt2->close();
                                            ?>

                                            <li class="p-4 bg-white flex flex-col md:flex-row items-start border-b-2 border-black">
                                                <div class="flex flex-col md:flex-row items-start w-full space-y-4 md:space-y-0 md:space-x-6">
                                                    <div class="flex-1 w-full md:w-auto">
                                                        <h2 class="text-lg font-semibold mb-2">
                                                            <a href="#" class="text-blue-600 hover:underline max-w-xs break-words">
                                                                <?php echo $title; ?>
                                                            </a>
                                                            <div class="mt-2">
                                                                <label class="text-sm font-medium text-gray-700">Accession Numbers:</label>
                                                                <div class="ml-2 border border-gray-300 rounded-md p-1 inline-block max-w-xs text-sm">
                                                                    <?php
                                                                    $accessionQuery = "SELECT accession_no, book_condition FROM `accession_records` WHERE book_id = ? AND book_category = ? AND borrower_id = ? AND available = 'reserved'";
                                                                    $stmt3 = $conn->prepare($accessionQuery);

                                                                    if ($stmt3) {
                                                                        $stmt3->bind_param("isi", $book_id, $category, $user_id);
                                                                        $stmt3->execute();
                                                                        $accessionResult = $stmt3->get_result();

                                                                        if ($accessionResult->num_rows > 0) {
                                                                            while ($accessionRow = $accessionResult->fetch_assoc()) {
                                                                                $accession_no = htmlspecialchars($accessionRow['accession_no']);
                                                                                $book_condition = htmlspecialchars($accessionRow['book_condition']); // Sanitize the condition text
                                                                                echo '<div class="flex items-center space-x-2">';
                                                                                echo '<span>' . $accession_no . '</span>';
                                                                                // Hidden input to send accession_no to the server
                                                                                echo '<input type="hidden" name="accession_no[' . $book_id . ']" value="' . $accession_no . '">';
                                                                                // "View Book Condition" link
                                                                                echo '<button type="button" class="text-blue-500 underline text-sm" onclick="showBookConditionModal(`' . $book_condition . '`)">View Book Condition</button>';
                                                                                echo '</div>';
                                                                            }
                                                                        } else {
                                                                            echo '<p class="text-gray-500">No reserved accession numbers found.</p>';
                                                                        }
                                                                        $stmt3->close();
                                                                    } else {
                                                                        echo '<p class="text-red-500">Error fetching accession numbers</p>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>


                                                            <!-- Modal for Book Condition -->
                                                            <div id="bookConditionModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" onclick="closeOnOutsideClick(event)">
                                                                <div class="bg-gray-50 rounded-lg shadow-lg w-full max-w-md mx-4 md:mx-0" onclick="event.stopPropagation()">
                                                                    <!-- Header Section -->
                                                                    <div class="flex items-center justify-between p-4 rounded-t-lg bg-gray-800 text-gray-100">
                                                                        <h5 class="text-lg font-bold">Book Condition</h5>
                                                                        <button type="button" class="text-gray-300 hover:text-gray-200" onclick="closeModal()">
                                                                            <span class="text-2xl font-bold">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <!-- Content Section -->
                                                                    <div class="p-6 text-gray-800">
                                                                        <p class="font-semibold text-gray-700">Condition:</p>
                                                                        <p id="modalBookConditionText" class="pl-2 text-gray-900">N/A</p>
                                                                    </div>

                                                                    <!-- Footer Section -->
                                                                    <div class="flex justify-end p-4 rounded-b-lg bg-gray-800">
                                                                        <button type="button" class="bg-gray-600 text-gray-100 font-semibold px-4 py-2 rounded-lg hover:bg-gray-500" onclick="closeModal()">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <script>
                                                                // Function to show the modal with book condition text
                                                                function showBookConditionModal(condition) {
                                                                    document.getElementById('modalBookConditionText').textContent = condition;
                                                                    document.getElementById('bookConditionModal').classList.remove('hidden');
                                                                }

                                                                // Function to close the modal
                                                                function closeModal() {
                                                                    document.getElementById('bookConditionModal').classList.add('hidden');
                                                                }

                                                                // Function to close the modal on outside click
                                                                function closeOnOutsideClick(event) {
                                                                    if (event.target.id === 'bookConditionModal') {
                                                                        closeModal();
                                                                    }
                                                                }
                                                            </script>






                                                        </h2>

                                                        <!-- Display other book information -->
                                                        <div class="mt-4">
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 text-sm text-gray-600">
                                                                <div class="font-medium bg-gray-200 p-2">Main Author:</div>
                                                                <div class="bg-gray-100 p-2"><?php echo $author; ?></div>
                                                                <div class="font-medium bg-gray-100 p-2">Published:</div>
                                                                <div class="bg-gray-200 p-2"><?php echo $publication_date; ?></div>
                                                                <div class="font-medium bg-gray-200 p-2">Table:</div>
                                                                <div class="bg-gray-100 p-2"><?php echo htmlspecialchars($book['Category']); ?></div>
                                                                <div class="font-medium bg-gray-100 p-2">Copies:</div>
                                                                <div class="bg-gray-100 p-2"><?php echo htmlspecialchars($book['book_id']); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Book cover image and selection checkbox -->
                                                    <div class="flex-shrink-0">
                                                     
                                                        <img src="<?php echo $record_cover; ?>" alt="Book Cover" class="w-36 h-56 border-2 border-gray-400 rounded-lg object-cover transition-transform duration-200 transform hover:scale-105">
                                                    </div>

                                                    <div class="flex-shrink-0 ml-2">
                                                        <input type="checkbox"
                                                            id="book-checkbox-<?php echo $date . '-' . $index; ?>"
                                                            name="selected_books[]"
                                                            value="<?php echo $book['book_id'] . '|' . htmlspecialchars($book['Category']); ?>"
                                                            class="book-checkbox-<?php echo $date; ?> mr-1">
                                                        <label for="book-checkbox-<?php echo $date . '-' . $index; ?>" class="text-sm text-gray-600">Select</label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>

                                <div class="flex items-center justify-end space-x-4">
                                    <button type="submit" name="not-available" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Not Available</button>
                                    <button type="submit" name="ready_to_claim" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Ready to Book Claim</button>
                                </div>



                            </form>

                            <div id="modalOverlay" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" onclick="closeOnOutsideClickReturned(event)">
                                <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4 md:mx-0" onclick="event.stopPropagation()">
                                    <!-- Modal Header -->
                                    <div class="flex items-center justify-between p-4 rounded-t-lg bg-gray-800 text-white">
                                        <h5 class="text-lg font-bold">Provide a Reason</h5>
                                        <button type="button" class="text-white hover:text-gray-300" onclick="closeModalReturned()">
                                            <span class="text-2xl font-bold">&times;</span>
                                        </button>
                                    </div>
                                    <!-- Modal Content -->
                                    <div class="p-6">
                                    <form id="reasonForm" method="POST">
    <!-- Hidden Inputs -->
    <input type="hidden" name="reason_action" value="not_available">
    <input type="hidden" id="modalUserId" name="user_id" value="">
    <input type="hidden" id="modalUserType" name="user_type" value=""> <!-- Hidden input for user type -->
    <input type="hidden" id="modalAccessionNumbers" name="accession_numbers" value="">
    <input type="hidden" id="modalSelectedBooks" name="selected_books" value="">

    <!-- Reason Textarea -->
    <div class="mb-4">
        <label for="reason" class="block text-gray-700 font-medium mb-2">Reason:</label>
        <textarea id="reason" name="reason" rows="4" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Provide your reason here..."></textarea>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Submit</button>
    </div>
</form>

                                    </div>
                                </div>
                            </div>

                            <script>
                                const modalOverlay = document.getElementById('modalOverlay');
                                const modalUserId = document.getElementById('modalUserId');
                                const modalSelectedBooks = document.getElementById('modalSelectedBooks');
                                const bookCheckboxes = document.querySelectorAll('input[name="selected_books[]"]');
                                const modalUserType = document.getElementById('modalUserType'); // Reference to the user_type hidden input in the modal

                                // Open modal when "Not Available" is clicked
                                document.querySelector('button[name="not-available"]').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent form submission

    const selectedBooks = [];
    const accessionNumbers = {};

    // Collect selected books and their accession numbers
    document.querySelectorAll('input[name="selected_books[]"]:checked').forEach((checkbox) => {
        const bookValue = checkbox.value; // Format: book_id|category
        selectedBooks.push(bookValue);

        const [bookId] = bookValue.split('|');
        const accessionInput = document.querySelector(`input[name="accession_no[${bookId}]"]`);
        if (accessionInput) {
            accessionNumbers[bookId] = accessionInput.value;
        }
    });

    if (selectedBooks.length === 0) {
        alert('Please select at least one book.');
        return;
    }

    // Get user ID and user type dynamically
    const userIdInput = document.querySelector('input[name="user_id"]');
    const userTypeInput = document.querySelector('input[name="user_type"]');

    const userId = userIdInput ? userIdInput.value : null;
    const userType = userTypeInput ? userTypeInput.value : null;

    if (!userId || !userType) {
        alert('User ID or User Type is missing. Please check the form setup.');
        return;
    }

    // Populate hidden inputs in the modal
    modalUserId.value = userId;
    modalUserType.value = userType; // Add user type to modal
    modalSelectedBooks.value = JSON.stringify(selectedBooks);
    modalAccessionNumbers.value = JSON.stringify(accessionNumbers);

    // Show modal
    modalOverlay.classList.remove('hidden');
});


                                // Close modal
                                function closeModalReturned() {
                                    modalOverlay.classList.add('hidden');
                                }

                                // Close modal on outside click
                                function closeOnOutsideClickReturned(event) {
                                    if (event.target.id === 'modalOverlay') {
                                        closeModalReturned();
                                    }
                                }
                            </script>





                            <script>
                                function toggleSelectAll(date) {
                                    const selectAllCheckbox = document.getElementById('select-all-' + date);
                                    const bookCheckboxes = document.querySelectorAll('.book-checkbox-' + date);
                                    bookCheckboxes.forEach(function(checkbox) {
                                        checkbox.checked = selectAllCheckbox.checked;
                                    });
                                }
                            </script>
                        <?php else: ?>
                            <div class="p-4 bg-white flex items-center border-b-2 border-black">
                                <div class="text-gray-600">No books found for this <?php echo ucfirst($user_type); ?>.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    </main>
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