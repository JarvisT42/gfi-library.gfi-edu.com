<?php
# Initialize the session
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');

    exit;
}

include '../connection.php';  // Database connection for main DB

// Get today's date
$today = date('Y-m-d');
$studentId = intval($_SESSION["Student_Id"]);

// Prepare the SQL query to update the `borrow` table status to 'failed-to-claim2'
$updateSql = "
    UPDATE borrow
    SET status = 'failed-to-claim2'
    WHERE Date_To_Claim < DATE_SUB(?, INTERVAL 5 DAY)
    AND status = 'failed-to-claim'
    AND student_id = ?";

// Prepare the statement
$stmt = $conn->prepare($updateSql);

// Bind the parameters to the prepared statement
$stmt->bind_param('si', $today, $studentId);

// // Execute the query
// if ($stmt->execute()) {
//     echo "Status updated successfully.";
// } else {
//     echo "Error updating status: " . $stmt->error;
// }

// Close the statement
$stmt->close();






?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'user_header.php'; ?>

    <style>
        .active-activity-logs {
            background-color: #f0f0f0;
            color: #000;
        }
    </style>
    <style>
        input:checked+label {
            text-decoration: underline;
        }

        label {
            transition: color 0.3s ease, text-decoration 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        input:checked+label {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
    </style>

</head>

<body>
    <?php include './src/components/sidebar.php'; ?>
    <main id="content" class="">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Activity Log</h1> <!-- Adjusted text size -->
                </div>
                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    This feature provides a comprehensive summary of your book borrowing history. It includes a detailed log of all books you've borrowed in the past, along with a current overview of books you have on loan. </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 p-4 bg-gray-200 dark:bg-gray-900 sm:rounded-lg">


                        <div class="flex items-center space-x-4">
                            <div class="flex space-x-6">
                                <div class="flex items-center">
                                    <input checked id="inline-checked-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-checked-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        Transaction
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="inline-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        History
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>



                    <div id="table1" class="overflow-x-auto">
                        <div class="scrollable-table-container relative overflow-x-auto shadow-md sm:rounded-lg pt-2 pr-6 pb-2 pl-4 border border-gray-300">
                            <?php

                            include("../connection.php");
                            include("../connection2.php");

                            // Sanitize the faculty ID to prevent SQL injection
                            $id = intval($_SESSION["Student_Id"]);

                            // Fetch the category and book_id based on the faculty_id
                            $categoryQuery = "SELECT Category, book_id, accession_no, Date_To_Claim, Issued_Date, due_date, status, reason_for_failed_to_claim, fines_id
                            FROM borrow
                            WHERE student_id = ?
                            AND status IN ('failed-to-claim',  'borrowed', 'pending', 'lost', 'ready_to_claim')";
                            $stmt = $conn->prepare($categoryQuery);
                            $stmt->bind_param('i', $id); // Assuming faculty_id is an integer
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Prepare the SQL to fetch book_condition from `accession_records`
                            $accessionQuery = "SELECT book_condition FROM `accession_records` WHERE accession_no = ? AND borrower_id = ? AND available = 'no'";
                            $stmt3 = $conn->prepare($accessionQuery);

                            // Prepare the SQL to fetch fines amount from `library_fines`
                            $finesQuery = "SELECT amount FROM library_fines WHERE fines_id = ?";
                            $stmt4 = $conn->prepare($finesQuery);

                            // Initialize an array to hold the fetched book records
                            $books = [];

                            // Fetch all rows (there may be multiple books borrowed by the same faculty)
                            while ($row = $result->fetch_assoc()) {
                                $category = $row['Category'];
                                $bookId = $row['book_id'];
                                $accessionNo = $row['accession_no'];
                                $dateToClaim = $row['Date_To_Claim'];
                                $issuedDate = $row['Issued_Date'];
                                $dueDate = $row['due_date'];
                                $status = $row['status'];
                                $bookCondition = "N/A"; // Default value if no condition is found
                                $reasonForFailedToClaim = $row['reason_for_failed_to_claim'];
                                $finesId = $row['fines_id']; // Fetch fines_id from borrow table
                                $finesAmount = "N/A"; // Default value if no fines amount is found

                                // Fetch the book condition from `accession_records`
                                if ($stmt3) {
                                    $stmt3->bind_param('si', $accessionNo, $id); // Bind accession_no and borrower_id
                                    $stmt3->execute();
                                    $accessionResult = $stmt3->get_result();
                                    if ($accessionRow = $accessionResult->fetch_assoc()) {
                                        $bookCondition = $accessionRow['book_condition'];
                                    }
                                }

                                // Fetch the fines amount from the `library_fines` table if fines_id exists
                                if ($status === 'borrowed' && !empty($finesId)) {
                                    if ($stmt4) {
                                        $stmt4->bind_param('i', $finesId); // Bind fines_id to fetch amount
                                        $stmt4->execute();
                                        $finesResult = $stmt4->get_result();
                                        if ($finesRow = $finesResult->fetch_assoc()) {
                                            $finesAmount = $finesRow['amount'];
                                        }
                                    }
                                }

                                // Prepare the SQL to fetch book details from the category-specific table
                                $query = "SELECT Title, Author FROM `$category` WHERE id = ?";
                                $bookStmt = $conn2->prepare($query);
                                $bookStmt->bind_param('i', $bookId);
                                $bookStmt->execute();
                                $bookResult = $bookStmt->get_result();

                                // Fetch the book details and store them in the $books array
                                if ($bookRow = $bookResult->fetch_assoc()) {
                                    $books[] = [
                                        'Title' => $bookRow['Title'],
                                        'Author' => $bookRow['Author'],
                                        'Category' => $category,
                                        'Date_To_Claim' => $dateToClaim,
                                        'Issued_Date' => $issuedDate,
                                        'Due_Date' => $dueDate,
                                        'status' => $status,
                                        'book_condition' => $bookCondition,
                                        'reason_for_failed_to_claim' => $reasonForFailedToClaim,
                                        'fines_amount' => $finesAmount // Include fines amount in the array
                                    ];
                                }

                                $bookStmt->close();
                            }

                            $stmt->close();
                            $stmt3->close();
                            $stmt4->close();
                            ?>

                            <table id="borrowed-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300">

                                <thead class="text-xs text-gray-700 uppercase bg-blue-400">
                                    <tr>





                                        <th scope="col" class="px-6 py-3 border border-gray-300">Book Title</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Author</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Date To Claim</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Issued Date</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Status</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($books as $row) {
                                    ?>
                                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border border-gray-300">
                                                <?php echo htmlspecialchars($row['Title']); ?>
                                            </th>
                                            <td class="px-6 py-4 border border-gray-300">
                                                <?php echo htmlspecialchars($row['Author']); ?>
                                            </td>

                                            <td class="px-6 py-4 border border-gray-300">
                                                <?php
                                                echo htmlspecialchars(date('F j, Y - l', strtotime($row['Date_To_Claim'])));
                                                ?> </td>
                                            <td class="px-6 py-4 border border-gray-300">
                                                <?php
                                                echo htmlspecialchars(date('F j, Y - l', strtotime($row['Issued_Date'])));
                                                ?> </td>
                                            <td class="px-6 py-4 border border-gray-300">
                                                <?php
                                                if (!empty($row['status']) && $row['status'] === 'failed-to-claim') {
                                                    echo 'Failed To Claim';
                                                } elseif (!empty($row['status']) && $row['status'] === 'borrowed') {
                                                    echo 'Borrowed';
                                                } elseif (!empty($row['status']) && $row['status'] === 'pending') {
                                                    echo 'Pending';
                                                } elseif (!empty($row['status']) && $row['status'] === 'ready_to_claim') {
                                                    echo 'Ready To Claim';
                                                } else {
                                                    echo 'Lost';
                                                }
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 border border-gray-300">
                                                <button
                                                    type="button"
                                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                                    onclick="openModal(
                                                        '<?php echo htmlspecialchars($row['Title']); ?>',
                                                        '<?php echo htmlspecialchars($row['Category']); ?>',
                                                        '<?php echo htmlspecialchars($row['status']); ?>',
                                                        '<?php echo htmlspecialchars($row['Date_To_Claim']); ?>',
                                                        '<?php echo htmlspecialchars($row['Issued_Date']); ?>',
                                                        '<?php echo htmlspecialchars($row['Due_Date']); ?>',
                                                        '<?php echo htmlspecialchars($row['book_condition'] ?? 'N/A'); ?>',
                                                        '<?php echo htmlspecialchars($row['reason_for_failed_to_claim'] ?? 'N/A'); ?>',
                                                                '<?php echo htmlspecialchars($finesAmount ?? 'N/A'); ?>'

                                                    )">
                                                    View
                                                </button>

                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                            <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
                            <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

                            <script>
                                $(document).ready(function() {
                                    $('#borrowed-table').DataTable({
                                        responsive: true,
                                        paging: true,
                                        searching: true,
                                        info: true,
                                        order: [],
                                        dom: "<'flex flex-col gap-2 md:flex-row md:items-center justify-between mb-2 mt-2'<'flex items-center space-x-4 md:space-x-8 mb-2 mt-2'l><'flex items-center space-x-4 md:space-x-8 mb-2 mt-2'f>>" +
                                            "<'overflow-x-auto'tr>" +
                                            "<'flex flex-col md:flex-row justify-between items-center gap-4 mt-4'ip>",
                                        language: {
                                            search: "Search:",
                                            lengthMenu: "Show _MENU_ entries"
                                        },
                                        columnDefs: [{
                                            orderable: false, // Disable ordering for the "Action" column
                                            targets: 5 // Target the 6th column (zero-based index)
                                        }],
                                        createdRow: function(row, data, dataIndex) {
                                            // Assuming status is in the 5th column (zero-based index is 4)
                                            const statusText = data[4].trim(); // Access data directly by column index

                                            if (statusText === 'Pending') {
                                                $(row).css({
                                                    'background-color': '#ffe5e5',
                                                    'color': 'black'
                                                }); // Light red background

                                            } else if (statusText === 'Ready To Claim') {
                                                $(row).css({
                                                    'background-color': '#e6f7e6',
                                                    'color': 'black'
                                                }); // Light green background
                                            }
                                        }
                                    });
                                });
                            </script>

                        </div>
                    </div>



                    <!--  -->


                    <div id="bookModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-md" onclick="closeOnOutsideClick(event)">
                        <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 md:mx-0" onclick="event.stopPropagation()">
                            <!-- Header Section -->
                            <div class="flex items-center justify-between p-5 rounded-t-lg bg-gray-800 text-white">
                                <h5 class="text-xl font-semibold">Book Details</h5>
                                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                                    <span class="text-2xl font-bold">&times;</span>
                                </button>
                            </div>
                            <!-- Content Section -->
                            <div class="p-6 text-gray-700">
                                <div class="mb-4">
                                    <p class="font-medium text-gray-600">Title:</p>
                                    <p id="modalTitle" class="pl-2 text-gray-800">N/A</p>
                                </div>
                                <div class="mb-4">
                                    <p class="font-medium text-gray-600">Category:</p>
                                    <p id="modalCategory" class="pl-2 text-gray-800">N/A</p>
                                </div>
                                <div class="mb-4">
                                    <p class="font-medium text-gray-600">Status:</p>
                                    <p id="modalStatus" class="pl-2 text-gray-800">N/A</p>
                                </div>
                                <div class="mb-4">
                                    <p class="font-medium text-gray-600">Date to Claim:</p>
                                    <p id="modalDateToClaim" class="pl-2 text-gray-800">N/A</p>
                                </div>
                                <div id="dueDateContainer" class="mb-4 hidden">
                                    <p class="font-medium text-gray-600">Due Date:</p>
                                    <p id="modalDueDate" class="pl-2 text-gray-800">N/A</p>
                                </div>
                                <div class="mb-4 book-condition-container">
                                    <p class="font-medium text-gray-600">Book Condition:</p>
                                    <p id="modalBookCondition" class="pl-2 text-gray-800">N/A</p>
                                </div>
                                <div class="mb-4" id="finesAmountContainer" style="display: none;">
                                    <p class="font-medium text-gray-600">Due Date Fines Amount:</p>
                                    <p id="modalFinesAmount" class="pl-2 text-gray-800">N/A</p>
                                </div>

                                <div id="ReasonForFailedToClaiContainer" class="mb-4 hidden">
                                    <p class="font-medium text-gray-600">Reason for Failed to Claim:</p>
                                    <p id="modalReasonForFailedToClaim" class="pl-2 text-gray-800">N/A</p>
                                </div>
                            </div>
                            <!-- Footer Section -->
                            <div class="flex justify-end p-4 rounded-b-lg bg-gray-800">
                                <button type="button" class="bg-gray-600 text-white font-medium px-4 py-2 rounded-md hover:bg-gray-500" onclick="closeModal()">Close</button>
                            </div>
                        </div>
                    </div>




                    <script>
                        function closeOnOutsideClick(event) {
                            // Check if the click is outside the modal content
                            const modalContent = document.querySelector("#bookModal > div");
                            if (!modalContent.contains(event.target)) {
                                closeModal();
                            }
                        }

                        function closeModal() {
                            document.getElementById('bookModal').classList.add('hidden');
                        }
                        // Optional: Function to open the modal
                        function openModal(title, category, status, dateToClaim, issuedDate, dueDate, bookCondition, reasonForFailedToClaim, finesAmount) {
                            // Populate the modal with the book details
                            document.getElementById('modalTitle').textContent = title;
                            document.getElementById('modalCategory').textContent = category;
                            document.getElementById('modalStatus').textContent = status;
                            document.getElementById('modalDateToClaim').textContent = dateToClaim;

                            const dueDateContainer = document.getElementById('dueDateContainer');
                            const reasonForFailedToClaimContainer = document.getElementById('ReasonForFailedToClaiContainer');
                            const bookConditionContainer = document.querySelector('.book-condition-container');
                            const finesAmountContainer = document.getElementById('finesAmountContainer'); // Create an element in the modal to show fines amount

                            // Add a description element for pending status
                            const pendingDescriptionContainerId = "pendingDescriptionContainer";
                            let pendingDescriptionContainer = document.getElementById(pendingDescriptionContainerId);

                            // If the container does not exist, create it dynamically
                            if (!pendingDescriptionContainer) {
                                pendingDescriptionContainer = document.createElement('div');
                                pendingDescriptionContainer.id = pendingDescriptionContainerId;
                                pendingDescriptionContainer.className = "text-blue-600 font-medium text-sm mb-4";
                                const modalContent = document.querySelector(".p-6"); // Parent container for modal content
                                modalContent.prepend(pendingDescriptionContainer); // Add it at the top of the content section
                            }

                            // Reset visibility and content for all sections
                            dueDateContainer.style.display = 'none';
                            reasonForFailedToClaimContainer.style.display = 'none';
                            bookConditionContainer.style.display = 'none';
                            finesAmountContainer.style.display = 'none'; // Hide fines amount by default
                            pendingDescriptionContainer.style.display = 'none'; // Hide pending description by default

                            if (status === 'borrowed') {
                                // Show Due Date, Book Condition, and Fines Amount
                                document.getElementById('modalDueDate').textContent = dueDate || 'N/A'; // Populate Due Date
                                document.getElementById('modalBookCondition').textContent = bookCondition || 'N/A'; // Populate Book Condition
                                document.getElementById('modalFinesAmount').textContent = finesAmount || 'N/A'; // Populate Fines Amount
                                dueDateContainer.style.display = 'block'; // Show Due Date section
                                bookConditionContainer.style.display = 'block'; // Show Book Condition section
                                finesAmountContainer.style.display = 'block'; // Show Fines Amount section
                            } else if (status === 'failed-to-claim') {
                                // Show Reason for Failed to Claim
                                document.getElementById('modalReasonForFailedToClaim').textContent = reasonForFailedToClaim || 'N/A'; // Populate Reason for Failed to Claim
                                reasonForFailedToClaimContainer.style.display = 'block'; // Show Reason for Failed to Claim
                            } else if (status === 'pending') {
                                // Add the "This book is currently preparing" description
                                pendingDescriptionContainer.textContent = "This book is currently being prepared. Please wait until the admin marks it as 'Ready to Claim'.";
                                pendingDescriptionContainer.style.display = 'block'; // Show pending description
                            }

                            // Display the modal
                            document.getElementById('bookModal').classList.remove('hidden');
                        }
                    </script>



                    <div id="table2-container" class="hidden">
                        <div id="table2" class="overflow-x-auto">
                            <div class="scrollable-table-container pt-2 pr-6 pb-2 pl-4 sm:rounded-lg border border-gray-300">

                                <div class="scrollable-table-container">

                                    <?php
                                    // Query for books with status 'returned'
                                    $categoryQueryReturned = "SELECT Category, book_id, Issued_Date, Return_Date, total_fines, status FROM borrow WHERE student_id = ? AND (status = 'returned' OR status = 'failed-to-claim2' OR status = 'replaced' OR status = 'lost-pay') ORDER BY Return_Date DESC";
                                    $stmtReturned = $conn->prepare($categoryQueryReturned);
                                    $stmtReturned->bind_param('i', $id); // Assuming student_id is an integer
                                    $stmtReturned->execute();
                                    $resultReturned = $stmtReturned->get_result();
                                    // Initialize an array to hold the returned books
                                    $returnedBooks = [];
                                    // Fetch all rows for returned books
                                    while ($rowReturned = $resultReturned->fetch_assoc()) {
                                        $category = $rowReturned['Category'];
                                        $bookId = $rowReturned['book_id'];
                                        $issuedDate = $rowReturned['Issued_Date'];
                                        $returnDate = $rowReturned['Return_Date'];

                                        $totalFines = number_format((float)$rowReturned['total_fines'], 2); // Ensure proper formatting
                                        $status = $rowReturned['status'];
                                        // Prepare the SQL to fetch book details from the category-specific table
                                        $queryReturned = "SELECT Title, Author FROM `$category` WHERE id = ?";
                                        $bookStmtReturned = $conn2->prepare($queryReturned);
                                        $bookStmtReturned->bind_param('i', $bookId);
                                        $bookStmtReturned->execute();
                                        $bookResultReturned = $bookStmtReturned->get_result();
                                        // Fetch the book details and store them in the $returnedBooks array
                                        if ($bookRowReturned = $bookResultReturned->fetch_assoc()) {
                                            $returnedBooks[] = [
                                                'Title' => $bookRowReturned['Title'],
                                                'Author' => $bookRowReturned['Author'],
                                                'Issued_Date' => $issuedDate,
                                                'return_date' => $returnDate,
                                                'Total_Fines' => $totalFines,
                                                'status' => $status
                                            ];
                                        }
                                        $bookStmtReturned->close();
                                    }
                                    $stmtReturned->close();
                                    ?>


                                    <table id="borrowed-table2" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-blue-400">




                                            <tr>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Book Title</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Author</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/3">Returned Date</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/12">Total Fines</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/5">Status</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/6">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Loop through the returned books to display them in the table
                                            foreach ($returnedBooks as $rowReturned) {
                                            ?>
                                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border border-gray-300">
                                                        <?php echo htmlspecialchars($rowReturned['Title']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <?php echo htmlspecialchars($rowReturned['Author']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <?php
                                                        echo htmlspecialchars(date('F j, Y - l', strtotime($rowReturned['return_date'])));
                                                        ?>

                                                    </td>
                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <?php echo htmlspecialchars($rowReturned['Total_Fines']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <?php
                                                        if (!empty($rowReturned['status']) && $rowReturned['status'] === 'failed-to-claim2') {
                                                            echo 'Failed To Claim';
                                                        } elseif (!empty($rowReturned['status']) && $rowReturned['status'] === 'returned') {
                                                            echo 'Returned';
                                                        } elseif (!empty($rowReturned['status']) && $rowReturned['status'] === 'replaced') {
                                                            echo 'Replaced';
                                                        } elseif (!empty($rowReturned['status']) && $rowReturned['status'] === 'lost-pay') {
                                                            echo 'Lost';
                                                        }

                                                        ?>
                                                    </td>


                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <button
                                                            type="button"
                                                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                                            onclick="openModalReturned('<?php echo htmlspecialchars($rowReturned['Title']); ?>', '<?php echo htmlspecialchars($category); ?>', '<?php echo htmlspecialchars($rowReturned['status']); ?>', '<?php echo htmlspecialchars($rowReturned['Total_Fines']); ?>')">
                                                            View
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- jQuery -->
                                    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                                    <!-- DataTables Core JS -->
                                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                                    <!-- DataTables TailwindCSS Integration -->
                                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#borrowed-table2').DataTable({
                                                responsive: true,
                                                paging: true,
                                                searching: true,
                                                info: true,
                                                order: [],

                                                dom: "<'flex flex-col gap-2 md:flex-row md:items-center justify-between mb-2 mt-2'<'flex items-center space-x-4 md:space-x-8 mb-2 mt-2'l><'flex items-center space-x-4 md:space-x-8 mb-2 mt-2'f>>" +
                                                    "<'overflow-x-auto'tr>" +
                                                    "<'flex flex-col md:flex-row justify-between items-center gap-4 mt-4'ip>",

                                                language: {
                                                    search: "Search:",
                                                    lengthMenu: "Show _MENU_ entries"
                                                },
                                                columnDefs: [{
                                                    orderable: false, // Disable ordering for the "Action" column
                                                    targets: 5 // Target the 6th column (zero-based index)
                                                }]
                                            });
                                        });
                                    </script>



                                </div>
                            </div>
                        </div>
                    </div>





                    <div id="bookModalReturned" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" onclick="closeOnOutsideClickReturned(event)">
                        <div class="bg-yellow-50 rounded-lg shadow-lg w-full max-w-md mx-4 md:mx-0" onclick="event.stopPropagation()">
                            <!-- Header Section -->
                            <div class="flex items-center justify-between p-4 rounded-t-lg bg-red-700 text-yellow-100">
                                <h5 class="text-lg font-bold">Returned Book Details</h5>
                                <button type="button" class="text-yellow-200 hover:text-yellow-100" onclick="closeModalReturned()">
                                    <span class="text-2xl font-bold">&times;</span>
                                </button>
                            </div>
                            <div class="p-6 text-gray-800">
                                <div class="mb-4">
                                    <p class="font-semibold text-red-800">Title:</p>
                                    <p id="modalReturnedTitle" class="pl-2 text-red-900">N/A</p>
                                </div>
                                <div class="mb-4">
                                    <p class="font-semibold text-red-800">Category:</p>
                                    <p id="modalReturnedCategory" class="pl-2 text-red-900">N/A</p>
                                </div>
                                <div class="mb-4">
                                    <p class="font-semibold text-red-800">Status:</p>
                                    <p id="modalReturnedStatus" class="pl-2 text-red-900">N/A</p>
                                </div>
                                <div class="mb-4">
                                    <p class="font-semibold text-red-800">Total Fines:</p>
                                    <p id="modalReturnedTotalFines" class="pl-2 text-red-900">N/A</p>
                                </div>
                            </div>
                            <div class="flex justify-end p-4 rounded-b-lg bg-red-700">
                                <button type="button" class="bg-yellow-600 text-red-900 font-semibold px-4 py-2 rounded-lg hover:bg-yellow-500" onclick="closeModalReturned()">Close</button>
                            </div>
                        </div>
                    </div>
                    <script>
                        function closeOnOutsideClickReturned(event) {
                            const modalContent = document.querySelector("#bookModalReturned > div");
                            if (!modalContent.contains(event.target)) {
                                closeModalReturned();
                            }
                        }

                        function closeModalReturned() {
                            document.getElementById('bookModalReturned').classList.add('hidden');
                        }

                        function openModalReturned(title, category, status, totalFines) {
                            document.getElementById('modalReturnedTitle').textContent = title;
                            document.getElementById('modalReturnedCategory').textContent = category;
                            document.getElementById('modalReturnedStatus').textContent = status;
                            document.getElementById('modalReturnedTotalFines').textContent = totalFines;

                            document.getElementById('bookModalReturned').classList.remove('hidden');
                        }
                    </script>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('inline-radio').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('table1').classList.add('hidden');
                document.getElementById('table2-container').classList.remove('hidden');
                document.getElementById('dropdownContainer').classList.remove('hidden');
            }
        });
        document.getElementById('inline-checked-radio').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('table1').classList.remove('hidden');
                document.getElementById('table2-container').classList.add('hidden');
                document.getElementById('dropdownContainer').classList.add('hidden');
            }
        });
        document.getElementById('dropdownRadioButton').addEventListener('click', function() {
            const dropdown = document.getElementById('dropdownRadio');
            dropdown.classList.toggle('hidden');
        });
    </script>
</body>

</html>