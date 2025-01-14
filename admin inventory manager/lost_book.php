<?php
session_start();
include '../connection.php'; // Ensure you have your database connection
include '../connection2.php'; // Ensure you have your database connection

if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}



if (isset($_GET['student_id']) || isset($_GET['faculty_id']) || isset($_GET['walk_in_id'])) {
    $user_type = ''; // Initialize user type

    // Determine which ID is present and set the corresponding user type and ID
    if (isset($_GET['student_id'])) {
        $user_type = 'student';
        $user_id = htmlspecialchars($_GET['student_id']);
    } elseif (isset($_GET['faculty_id'])) {
        $user_type = 'faculty';
        $user_id = htmlspecialchars($_GET['faculty_id']);
    } elseif (isset($_GET['walk_in_id'])) {
        $user_type = 'walk_in';
        $user_id = htmlspecialchars($_GET['walk_in_id']);
    }

    // Fetch the category, book_id, issued date, and due date based on the user type
    $categoryQuery = "
        SELECT a.Category, a.book_id, a.Issued_Date, a.Due_Date, a.role, a.Way_Of_Borrow, a.accession_no, a.expected_replacement_date
        FROM borrow AS a
        WHERE " . ($user_type === 'student' ? "a.student_id" : ($user_type === 'faculty' ? "a.faculty_id" : "a.walk_in_id")) . " = ? 
        AND status = 'lost'";

    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param('i', $user_id); // Assuming user_id is an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC) ?: [];

    // Fetch user details for full name based on the user type
    if ($user_type === 'student') {
        // For students
        $userQuery = "
            SELECT First_Name, Middle_Initial, Last_Name 
            FROM students
            WHERE Student_Id = ?";
        $stmtUser = $conn->prepare($userQuery);
        $stmtUser->bind_param('i', $user_id);
        $stmtUser->execute();
        $userResult = $stmtUser->get_result();

        if ($userResult->num_rows > 0) {
            $userRow = $userResult->fetch_assoc();
            $fullName = $userRow['First_Name'] . ' ' . $userRow['Middle_Initial'] . ' ' . $userRow['Last_Name'];
            $displayRole = "Student";
        } else {
            $fullName = 'Unknown Student';
            $displayRole = "Student";
        }
        $stmtUser->close();
    } elseif ($user_type === 'faculty') {
        // For faculty, include employment_status
        $userQuery = "
            SELECT First_Name, Middle_Initial, Last_Name, employment_status 
            FROM faculty
            WHERE Faculty_Id = ?";
        $stmtUser = $conn->prepare($userQuery);
        $stmtUser->bind_param('i', $user_id);
        $stmtUser->execute();
        $userResult = $stmtUser->get_result();

        if ($userResult->num_rows > 0) {
            $userRow = $userResult->fetch_assoc();
            $fullName = $userRow['First_Name'] . ' ' . $userRow['Middle_Initial'] . ' ' . $userRow['Last_Name'];
            $displayRole = "Faculty (" . htmlspecialchars($userRow['employment_status']) . ")";
        } else {
            $fullName = 'Unknown Faculty';
            $displayRole = "Faculty";
        }
        $stmtUser->close();
    } else {
        // For walk-in users, get the name from the borrow table
        $fullNameQuery = "SELECT full_name, role FROM walk_in_borrowers WHERE walk_in_id = ?";
        $stmtWalkIn = $conn->prepare($fullNameQuery);
        $stmtWalkIn->bind_param('i', $user_id);
        $stmtWalkIn->execute();
        $walkInResult = $stmtWalkIn->get_result();

        if ($walkInResult->num_rows > 0) {
            $walkInRow = $walkInResult->fetch_assoc();
            $fullName = $walkInRow['full_name'];
            $displayRole = $walkInRow['role'];
        } else {
            $fullName = 'Unknown Walk-In';
            $displayRole = "Walk-In";
        }
        $stmtWalkIn->close();
    }
    $stmt->close();
} else {
    echo "No student, faculty, or walk-in ID provided.";
    exit;
}








?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>

    <style>
        .active-replacement-books {
            background-color: #f0f0f0;
            color: #000;
        }

        .active-request {
            background-color: #f0f0f0;
            color: #000;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>
    <main id="content" class="">
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
                <div class="bg-gray-100 p-6 w-full mx-auto">
                    <div class="bg-white p-4 shadow-sm rounded-lg mb-2">
                        <div class="bg-gray-100 p-2 flex justify-between items-center">
                            <h1 class="m-0">Student Name: <?php echo $fullName; ?></h1>

                        </div>
                    </div>

                    <?php if (!empty($books)): ?>
                        <?php
                        // Group books by Date_To_Claim
                        $grouped_books = [];
                        foreach ($books as $book) {
                            $date_to_claim = htmlspecialchars($book['Issued_Date']);
                            $grouped_books[$date_to_claim][] = $book;
                        }
                        ?>

                        <div id="book-request-form" class="space-y-6">

                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">

                            <?php
                            // Initialize the overall index counter
                            $overall_index = 0;
                            ?>

                            <?php foreach ($grouped_books as $date => $books_group): ?>
                                <div class="bg-blue-200 p-4 rounded-lg">
                                    <div class="bg-blue-200 rounded-lg flex items-center justify-between ">
                                        <!-- Left side: Date to Claim -->
                                        <!-- <h3 class="text-lg font-semibold text-white">Issued Date: <?php echo $date; ?></h3> -->
                                    </div>

                                    <?php foreach ($books_group as $book): ?>
                                        <?php
                                        $category = $book['Category'];
                                        $book_id = $book['book_id'];

                                        // Fetch the Title, Author, and record_cover from conn2 based on book_id
                                        $titleQuery = "SELECT * FROM `$category` WHERE id = ?";
                                        $stmt2 = $conn2->prepare($titleQuery);
                                        $stmt2->bind_param('i', $book_id);
                                        $stmt2->execute();
                                        $result = $stmt2->get_result();

                                        // Initialize variables
                                        $title = 'Unknown Title';
                                        $author = 'Unknown Author';

                                        $record_cover = null; // Initialize with null

                                        if ($row = $result->fetch_assoc()) {
                                            $title = $row['Title']; // Get the title
                                            $author = $row['Author']; // Get the author
                                            $record_cover = $row['record_cover']; // Get the record covers
                                        }

                                        $stmt2->close();





                                        $issued_date = $book['Issued_Date'];
                                        $due_date = empty($book['Due_Date']) ? date('Y-m-d', strtotime($issued_date . ' + 3 days')) : $book['Due_Date'];
                                        $current_date = date('Y-m-d');
                                        $fines_value = 5; // Replace this with your actual fine per day value

                                        if (!function_exists('countSundays')) {
                                            function countSundays($start_date, $end_date)
                                            {
                                                $start = new DateTime($start_date);
                                                $end = new DateTime($end_date);
                                                $interval = new DateInterval('P1D');
                                                $period = new DatePeriod($start, $interval, $end->add($interval));

                                                $sundays = 0;
                                                foreach ($period as $date) {
                                                    if ($date->format('w') == 0) { // Sunday
                                                        $sundays++;
                                                    }
                                                }
                                                return $sundays;
                                            }
                                        }

                                        if ($current_date > $due_date) {
                                            $overdue_days = (strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24);
                                            $overdue_days = floor($overdue_days);
                                            $sundays = countSundays($due_date, $current_date);
                                            $adjusted_days = $overdue_days - $sundays;
                                            $fine_amount = $adjusted_days * $fines_value;
                                        } else {
                                            $fine_amount = 0;
                                        }

                                        $fine_amount = round($fine_amount);



                                        ?>

                                        <li class="max-w-3xl mx-auto p-6 bg-white shadow-lg rounded-lg mb-2 flex flex-col">
                                            <div class="flex-1">
                                                <div class="flex flex-col md:flex-row justify-between mb-6">
                                                    <div class="flex-1 mb-4 md:mb-0">
                                                        <h1 class="text-2xl font-bold mb-1">Title:</h1>
                                                        <p class="text-xl mb-4"><?php echo $title; ?> </p>
                                                        <h1 class="text-2xl font-bold mb-1">Author:</h1>
                                                        <p class="text-xl mb-4"><?php echo $author; ?> </p>
                                                        <div class="mb-4">
                                                            <h2 class="text-lg font-semibold text-gray-600 mb-1">Borrow Category:</h2>
                                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($book['Category']); ?></p>
                                                            <h3 class="text-lg font-semibold text-gray-600 mb-1">Accession no:</h3>
                                                            <p id="accession_no-<?php echo $overall_index; ?>" class="text-sm text-gray-500"><?php echo htmlspecialchars($book['accession_no']); ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="w-full md:w-32 h-40 bg-gray-200 border border-gray-300 flex items-center justify-center mb-4 md:mb-0">
                                                        <?php
                                                        // Handle the image display
                                                        if (!empty($record_cover)) { // Use the fetched record_cover
                                                            $imageData = base64_encode($record_cover);
                                                            $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                                                        } else {
                                                            $imageSrc = 'path/to/default/image.jpg'; // Provide a default image source
                                                        }
                                                        ?>
                                                        <img src="<?php echo $imageSrc; ?>" alt="Book Cover" class="w-full h-full border-2 border-gray-400 rounded-lg object-cover transition-transform duration-200 transform hover:scale-105">
                                                    </div>
                                                </div>



                                                <div class="bg-blue-100 p-4 rounded-lg space-y-4">
                                                    <!-- Responsive Grid for Fines, Processing Fee, and Buttons -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                        <!-- Due Date Fines -->
                                                        <div id="dueDateFines-<?php echo $overall_index; ?>" class="border-r border-gray-300 pr-4">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <label class="text-sm font-semibold">Due Date Fines:</label>
                                                                <p class="text-sm text-gray-700"><?php echo htmlspecialchars($due_date); ?></p>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <span class="text-gray-500 mr-1">₱</span>
                                                                <input
                                                                    type="number"
                                                                    id="fine-amount-<?php echo $overall_index; ?>"
                                                                    class="border border-gray-300 rounded p-1 w-full text-sm"
                                                                    value="<?php echo htmlspecialchars($fine_amount); ?>"
                                                                    placeholder="Enter fine amount" />
                                                            </div>
                                                        </div>

                                                        <!-- Processing Fee -->
                                                        <div id="finehidden-<?php echo $overall_index; ?>" class="pl-4 border-r border-gray-300 pr-4">
                                                            <label id="fineLabel-<?php echo $overall_index; ?>" class="text-sm font-semibold block mb-1">Processing Fee:</label>
                                                            <div class="flex items-center">
                                                                <span class="text-gray-500 mr-1">₱</span>
                                                                <input
                                                                    id="fineInput-<?php echo $overall_index; ?>"
                                                                    class="border border-gray-300 rounded p-1 w-full text-sm"
                                                                    type="number"
                                                                    placeholder="Enter processing fee">
                                                            </div>
                                                        </div>

                                                        <!-- Buttons -->
                                                        <div class="flex justify-end items-center space-x-4">
                                                            <!-- Replace Button -->
                                                            <button
    class="bg-gray-300 text-gray-700 rounded px-4 py-2 text-sm hover:bg-gray-400 transition"
    data-index="<?php echo $overall_index; ?>"
    onclick="openReturnModal(
        '<?php echo htmlspecialchars($title); ?>', 
        '<?php echo htmlspecialchars($author); ?>', 
        '<?php echo htmlspecialchars($category); ?>', 
        '<?php echo htmlspecialchars($book_id); ?>', 
        '<?php echo htmlspecialchars($book['accession_no']); ?>', 
        '<?php echo $user_type; ?>', 
        '<?php echo $user_id; ?>', 
        'fine-amount-<?php echo $overall_index; ?>', 
        'fineInput-<?php echo $overall_index; ?>'
    )">
    Replace
</button>


                                                            <!-- Report Button -->
                                                            <button
                                                                class="bg-red-500 text-white rounded px-4 py-2 text-sm hover:bg-red-600 transition"
                                                                data-index="<?php echo $overall_index; ?>"
                                                                onclick="openReportModal('<?php echo htmlspecialchars($title); ?>', '<?php echo htmlspecialchars($author); ?>', '<?php echo htmlspecialchars($category); ?>', '<?php echo htmlspecialchars($book_id); ?>', '<?php echo htmlspecialchars($book['accession_no']); ?>', '<?php echo $user_type; ?>', '<?php echo $user_id; ?>')">
                                                                Report
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>









                                            </div>
                                        </li>








                                        <?php
                                        // Increment overall index for each book displayed
                                        $overall_index++;
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>
                        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                            <div id="alert" class="alert alert-success" role="alert">
                                Department added successfully!
                            </div>
                        <?php endif; ?>

                        <p>No books available.</p>
                    <?php endif; ?>


                </div>
            </div>
        </div>




        <!-- Modal Structure -->
        <!-- Modal Structure -->
        <!-- Modal Structure -->








        <div id="replaceModal" class="fixed inset-0 hidden backdrop-blur-sm bg-black bg-opacity-30 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Replace Book</h2>
        <p><strong>User Type:</strong> <span id="replacementModalUserType"></span></p>
        <p><strong>User ID:</strong> <span id="replacementModalUserId"></span></p>
        <p><strong>Book ID:</strong> <span id="replacementModalBookId"></span></p>
        <p><strong>Category:</strong> <span id="replacementModalCategory"></span></p>
        <p><strong>Title:</strong> <span id="replacementModalTitle"></span></p>
        <p><strong>Author:</strong> <span id="replacementModalAuthor"></span></p>
        <p><strong>Accession No:</strong> <span id="replacementModalAccessionNo"></span></p>
        <p><strong>Total Fee:</strong> <span id="replacementModalTotalFee" class="text-lg font-bold text-red-500"></span></p>

        <div class="flex justify-end space-x-2">
            <button onclick="closeModal()" class="bg-gray-300 text-gray-700 rounded px-4 py-2">Cancel</button>
            <button onclick="confirmReplacement()" class="bg-blue-600 text-white rounded px-4 py-2">Confirm</button>
        </div>
    </div>
</div>





        <div id="reportModal" class="fixed inset-0 hidden backdrop-blur-sm bg-black bg-opacity-30 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-2xl font-bold mb-4">Report Book</h2>
                <p><strong>Title:</strong> <span id="reportModalTitle"></span></p>
                <p><strong>Author:</strong> <span id="reportModalAuthor"></span></p>
                <p><strong>Category:</strong> <span id="reportModalCategory"></span></p>
                <p><strong>Accession No:</strong> <span id="reportModalAccessionNo"></span></p>
                <p><strong>Book ID:</strong> <span id="reportModalBookId"></span></p>
                <p><strong>User Type:</strong> <span id="reportModalUserType"></span></p>
                <p><strong>User ID:</strong> <span id="reportModalUserId"></span></p>

                <!-- Reason for Reporting -->
                <p class="mt-4">
                    <label for="reportReason" class="block font-semibold mb-1">Reason for Reporting:</label>
                    <textarea id="reportReason" class="w-full border rounded p-2" placeholder="Describe the issue here..." rows="4"></textarea>
                </p>

                <!-- Fine Input -->
                <p class="mt-4">
                    <label for="reportFine" class="block font-semibold mb-1">Fine (PHP):</label>
                    <input
                        id="reportFine"
                        type="number"
                        class="w-full border rounded p-2"
                        placeholder="Enter the fine amount (e.g., 50.00)"
                        min="0"
                        step="0.01">
                </p>

                <div class="flex justify-end space-x-2 mt-4">
                    <button onclick="closeReportModal()" class="bg-gray-300 text-gray-700 rounded px-4 py-2">Cancel</button>
                    <button onclick="confirmReport()" class="bg-red-600 text-white rounded px-4 py-2">Submit Report</button>
                </div>
            </div>
        </div>




        <script>
            // Function to open the Report Modal with dynamic content
            function openReportModal(title, author, category, bookId, accessionNo, userType, userId) {
                // Populate modal fields with dynamic content
                document.getElementById('reportModalTitle').textContent = title;
                document.getElementById('reportModalAuthor').textContent = author;
                document.getElementById('reportModalCategory').textContent = category;
                document.getElementById('reportModalAccessionNo').textContent = accessionNo;
                document.getElementById('reportModalBookId').textContent = bookId;
                document.getElementById('reportModalUserType').textContent = userType;
                document.getElementById('reportModalUserId').textContent = userId;

                // Show the modal
                document.getElementById('reportModal').classList.remove('hidden');
            }

            // Function to close the Report Modal
            function closeReportModal() {
                document.getElementById('reportModal').classList.add('hidden');
            }

            // Function to handle report confirmation and send data to the server
            function confirmReport() {
                console.log('Confirm Report function called'); // Debugging log

                const title = document.getElementById('reportModalTitle').textContent;
                const author = document.getElementById('reportModalAuthor').textContent;
                const category = document.getElementById('reportModalCategory').textContent;
                const bookId = document.getElementById('reportModalBookId').textContent;
                const accessionNo = document.getElementById('reportModalAccessionNo').textContent;
                const userType = document.getElementById('reportModalUserType').textContent;
                const userId = document.getElementById('reportModalUserId').textContent;
                const reason = document.getElementById('reportReason').value.trim();
                const fine = parseFloat(document.getElementById('reportFine').value); // Get the fine amount

                // Validate the reason and fine fields
                if (!reason) {
                    alert('Please provide a reason for reporting.');
                    return;
                }

                if (isNaN(fine) || fine < 0) {
                    alert('Please enter a valid fine amount.');
                    return;
                }

                // Prepare the data to send
                fetch('report_book_save.php', { // Adjust to the correct PHP file for handling reports
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            title: title,
                            author: author,
                            category: category,
                            book_id: bookId,
                            accession_no: accessionNo,
                            user_type: userType,
                            user_id: userId,
                            reason: reason,
                            fine: fine, // Include fine in the payload
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Report submitted successfully!');
                            closeReportModal(); // Close the modal
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }













            // Function to open the modal with dynamic content
            function openReturnModal(title, author, category, bookId, accessionNo, userType, userId, fineInputId, processingInputId) {
    // Set the modal content dynamically
    document.getElementById('replacementModalTitle').textContent = title;
    document.getElementById('replacementModalAuthor').textContent = author;
    document.getElementById('replacementModalCategory').textContent = category;
    document.getElementById('replacementModalAccessionNo').textContent = accessionNo;
    document.getElementById('replacementModalBookId').textContent = bookId;
    document.getElementById('replacementModalUserType').textContent = userType;
    document.getElementById('replacementModalUserId').textContent = userId;

    // Fetch the "Due Date Fines" and "Processing Fee"
    const fineAmountInput = document.getElementById(fineInputId);
    const processingFeeInput = document.getElementById(processingInputId);

    const dueDateFines = parseFloat(fineAmountInput.value) || 0;
    const processingFee = parseFloat(processingFeeInput.value) || 0;

    // Calculate the total fee
    const totalFee = dueDateFines + processingFee;

    // Display the total fee in the modal
    document.getElementById('replacementModalTotalFee').textContent = `₱${totalFee.toFixed(2)}`;

    // Show the modal
    document.getElementById('replaceModal').classList.remove('hidden');
}


            // Function to close the modal
            function closeModal() {
                document.getElementById('replaceModal').classList.add('hidden'); // Hide the modal
            }

            // Function to handle replacement confirmation and send data to lost_book_save.php
            function confirmReplacement() {
    console.log('Confirm Replacement function called'); // Debugging log

    const category = document.getElementById('replacementModalCategory').textContent;
    const bookId = document.getElementById('replacementModalBookId').textContent;
    const accessionNo = document.getElementById('replacementModalAccessionNo').textContent;
    const userType = document.getElementById('replacementModalUserType').textContent;
    const userId = document.getElementById('replacementModalUserId').textContent;

    // Get the total fee
    const totalFee = document.getElementById('replacementModalTotalFee').textContent.replace('₱', '').trim();

    // Prepare the data to send
    fetch('lost_book_save.php', { // Adjust to the correct PHP file
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            book_id: bookId,
            category: category,
            user_type: userType,
            user_id: userId,
            accession_no: accessionNo,
            total_fee: totalFee // Include the total fee
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Reload the page to update the status of the damaged book
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

        </script>








    </main>


    <script>
        // Set a timeout to hide the alert after 3 seconds (3000 ms)
        setTimeout(function() {
            var alertElement = document.getElementById('alert');
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        }, 4000);
    </script>
    <script src="./src/components/header.js"></script>
    <script>
        // Function to automatically show the dropdown if on book_request.php
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownRequest = document.getElementById('dropdown-request');

            // Open the dropdown menu for 'Request'
            dropdownRequest.classList.remove('hidden');
            dropdownRequest.classList.add('block'); // Make the dropdown visible

        });
    </script>
</body>

</html>