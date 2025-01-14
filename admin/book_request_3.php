<?php
session_start();
include '../connection.php'; // Ensure you have your database connection
include '../connection2.php'; // Ensure you have your database connection


if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}



// Check if student_id or faculty_id is set in the query parameters
if (isset($_GET['student_id']) || isset($_GET['faculty_id'])) {
    $isStudent = isset($_GET['student_id']);
    $user_id = $isStudent ? htmlspecialchars($_GET['student_id']) : htmlspecialchars($_GET['faculty_id']);
    $user_type = $isStudent ? 'student' : 'faculty';

    // Fetch the category, book_id, and user details based on student_id or faculty_id
    $categoryQuery = "
        SELECT a.Category, a.book_id, a.Date_To_Claim
        FROM borrow AS a
        WHERE " . ($isStudent ? "a.student_id" : "a.faculty_id") . " = ? AND status = 'ready_to_claim'";

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
                        <div class="bg-gray-100 p-2 flex justify-between items-center">
                            <div class="pl-10">
                                <h1 class="m-0 "><?php echo ucfirst($user_type); ?> Name: <?php echo $fullName; ?></h1>
                            </div>
                            <div class="flex items-center pr-10">
                                <label for="due_date" class="mr-2">Due Date:</label>
                                <input type="date" id="due_date" name="due_date" class="p-1 border rounded" required>
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

                            <form id="book-request-form" class="space-y-6" method="POST" action="book_request_2_save.php" onsubmit="return validateDueDate()">
                                <input type="hidden" name="<?php echo $user_type; ?>_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <input type="hidden" id="hidden_due_date" name="due_date" value="">

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
                                                                    // Query to fetch accession numbers and book conditions for the given book_id, category, and borrower_id with available set to 'reserved'
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
                                                                                echo '<input type="hidden" name="accession_no[' . $book_id . '][]" value="' . $accession_no . '">';
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

                                <div class="flex items-center justify-end">
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Issued Book</button>
                                </div>
                            </form>


                            <script>
                                function validateDueDate() {
                                    const dueDate = document.getElementById("due_date").value;
                                    if (!dueDate) {
                                        alert("Please select a due date before proceeding.");
                                        return false; // Prevent form submission
                                    }
                                    document.getElementById("hidden_due_date").value = dueDate; // Set the hidden input with the due date
                                    return true; // Allow form submission if due date is set
                                }

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
</body>

</html>