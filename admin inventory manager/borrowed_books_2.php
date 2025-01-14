<?php
session_start();
include '../connection.php'; // Ensure you have your database connection
include '../connection2.php'; // Ensure you have your database connection

if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}


// Check if any of the IDs (student_id, faculty_id, walk_in_id) are set in the query parameters
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
        SELECT a.Category, a.book_id, a.Issued_Date, a.Due_Date, a.role, a.Way_Of_Borrow, a.accession_no
        FROM borrow AS a
        WHERE " . ($user_type === 'student' ? "a.student_id" : ($user_type === 'faculty' ? "a.faculty_id" : "a.walk_in_id")) . " = ?
        AND status = 'borrowed'";



    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param('s', $user_id); // Assuming user_id is an integer
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
    <!-- <link rel="stylesheet" href="path/to/your/styles.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>

    <style>
        .active-borrowed-books {
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
                            <h1 class="m-0"><?php echo ucfirst($user_type); ?> Name: <?php echo $fullName; ?></h1>
                        </div>
                    </div>

                    <?php if (!empty($books)): ?>
                        <?php
                        // Group books by Due Date
                        $grouped_books = [];
                        foreach ($books as $book) {
                            $due_date = htmlspecialchars($book['Due_Date']);
                            $grouped_books[$due_date][] = $book;
                        }
                        ?>

                        <div id="book-request-form" class="space-y-6">
                            <input type="hidden" name="<?php echo $user_type; ?>_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <?php
                            // Initialize the overall index counter
                            $overall_index = 0;
                            ?>

                            <?php foreach ($grouped_books as $date => $books_group): ?>
                                <div class="bg-blue-200 p-4 rounded-lg">
                                    <div class="bg-blue-200 rounded-lg flex items-center justify-between ">
                                        <h3 class="text-lg font-semibold text-white">Due Date: <?php echo $date; ?></h3>
                                    </div>

                                    <?php foreach ($books_group as $book): ?>
                                        <?php
                                        $category = $book['Category'];
                                        $book_id = $book['book_id'];

                                        // Fetch Title, Author, and record_cover from conn2 based on book_id
                                        $titleQuery = "SELECT * FROM `$category` WHERE id = ?";
                                        $stmt2 = $conn2->prepare($titleQuery);
                                        $stmt2->bind_param('i', $book_id);
                                        $stmt2->execute();
                                        $result = $stmt2->get_result();

                                        $title = 'Unknown Title';
                                        $author = 'Unknown Author';
                                        $record_cover = null;

                                        if ($row = $result->fetch_assoc()) {
                                            $title = $row['title'];
                                            $author = $row['author'];
                                            $record_cover = $row['image_path'];
                                        }
                                        $stmt2->close();
                                        include '../connection.php';

                                        $fines_value = 0;

                                        $sql = "
                                            SELECT l.fines_id, l.amount
                                            FROM borrow AS a
                                            JOIN library_fines AS l ON a.fines_id = l.fines_id
                                            WHERE " . ($user_type === 'student' ? "a.student_id" : ($user_type === 'faculty' ? "a.faculty_id" : "a.walk_in_id")) . " = ?
                                            AND status = 'borrowed'
                                            LIMIT 1";

                                        if ($stmt = $conn->prepare($sql)) {
                                            $stmt->bind_param("s", $user_id); // Bind the user ID dynamically based on user_type
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result && $result->num_rows > 0) {
                                                $row = $result->fetch_assoc();
                                                $fines_value = (int)$row['amount']; // Retrieve the fine amount
                                            }

                                            $stmt->close();
                                        }

                                        // Now you can use $fines_value as needed


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

                                        <li class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg mb-2 flex flex-col"
                                            data-book-id="<?php echo htmlspecialchars($book_id); ?>"
                                            data-category="<?php echo htmlspecialchars($category); ?>"
                                            data-fine-amount="<?php echo $fine_amount; ?>"
                                            data-index="<?php echo $overall_index; ?>">
                                            <div class="text-sm text-gray-500 mb-2">




                                                <?php if ($user_type === 'student'): ?>
                                                    Student ID: <?php echo htmlspecialchars($user_id); ?>
                                                <?php elseif ($user_type === 'faculty'): ?>
                                                    Faculty ID: <?php echo htmlspecialchars($user_id); ?>
                                                <?php elseif ($user_type === 'walk_in'): ?>
                                                    Walk-In ID: <?php echo htmlspecialchars($user_id); ?>
                                                <?php endif; ?>
                                            </div>


                                            <div class="flex-1">
                                                <div class="flex flex-col md:flex-row justify-between mb-6">
                                                    <!-- Book Information -->
                                                    <div class="flex-1 mb-4 md:mb-0">
                                                        <h1 class="text-2xl font-bold mb-1">Title:</h1>
                                                        <p id="bookTitle-<?php echo $overall_index; ?>" class="text-xl mb-4"><?php echo htmlspecialchars($title); ?></p>
                                                        <div class="mb-4">
                                                            <h2 class="text-lg font-semibold text-gray-600 mb-1">Borrow Category:</h2>
                                                            <p id="bookCategory-<?php echo $overall_index; ?>" class="text-sm text-gray-500"><?php echo htmlspecialchars($category); ?></p>
                                                        </div>

                                                        <h3 class="text-lg font-semibold text-gray-600 mb-1">Accession no:</h3>
                                                        <p id="accession_no-<?php echo $overall_index; ?>"
                                                            class="text-sm text-gray-500"
                                                            style="white-space: pre-wrap;"><?php echo htmlspecialchars($book['accession_no']); ?></p>


                                                    </div>



                                                    <!-- Book Cover Image -->
                                                    <div class="w-full md:w-32 h-40 bg-gray-200 border border-gray-300 flex items-center justify-center mb-4 md:mb-0">

                                                        <img src="<?php echo $record_cover; ?>" alt="Book Cover" class="w-full h-full border-2 border-gray-400 rounded-lg object-cover transition-transform duration-200 transform hover:scale-105">
                                                    </div>
                                                </div>
                                                <!-- Book Information (Issued Date, Due Date, etc.) -->
                                                <div class="bg-blue-100 p-4 rounded-lg">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                                        <!-- Issued Date -->
                                                        <div>
                                                            <p class="text-sm font-semibold">Issued Date:</p>
                                                            <p class="text-sm"><?php echo htmlspecialchars($book['Issued_Date']); ?></p>
                                                        </div>

                                                        <!-- Due Date -->
                                                        <div>
                                                            <p class="text-sm font-semibold">Due Date:</p>
                                                            <p class="text-sm due-date" data-index="<?php echo $overall_index; ?>">
                                                                <?php echo htmlspecialchars($due_date); ?>
                                                            </p>
                                                        </div>

                                                        <!-- Due Date Fines -->
                                                        <div>
                                                            <label class="text-sm font-semibold">Due Date Fines:</label>
                                                            <div class="relative">
                                                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                                                                <input
                                                                    type="text"
                                                                    id="fine-amount-<?php echo $overall_index; ?>"
                                                                    class="text-sm font-semibold border rounded-md px-6 py-1 pl-8 w-full"
                                                                    value="<?php echo htmlspecialchars($fine_amount); ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Status and Fine Information -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                                        <div>
                                                            <p class="text-sm font-semibold">Renew</p>
                                                            <select class="renew-dropdown border border-gray-300 rounded p-1 mr-16" data-index="<?php echo $overall_index; ?>" data-due-date="<?php echo htmlspecialchars($due_date); ?>">
                                                                <option value="0">0 Days</option>
                                                                <option value="3">3 Days</option>
                                                                <option value="6">6 Days</option>
                                                                <option value="9">9 Days</option>
                                                                <option value="12">12 Days</option>
                                                                <option value="15">15 Days</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold">Book Status:</p>
                                                            <select id="statusSelect-<?php echo $overall_index; ?>" class="border border-gray-300 rounded p-1 mr-16" onchange="toggleStatusActions(<?php echo $overall_index; ?>)">
                                                                <option value=""></option>
                                                                <option value="Damage">Damage</option>
                                                                <option value="Lost">Lost</option>
                                                            </select>
                                                        </div>
                                                        <div id="finehidden-<?php echo $overall_index; ?>" style="display: none;">
                                                            <p id="fineLabel-<?php echo $overall_index; ?>" class="text-sm font-semibold">Fines:</p>
                                                            <div class="flex items-center">
                                                                P:<input id="fineInput-<?php echo $overall_index; ?>" class="border border-gray-300 rounded p-1 w-32 finesInput" type="number" disabled placeholder="Disabled">
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div id="damageTextArea-<?php echo $overall_index; ?>" style="display: none;" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                                        <div class="col-span-3">
                                                            <p class="text-sm font-semibold">Describe the Damage:</p>
                                                            <textarea id="damageDescription-<?php echo $overall_index; ?>" class="border border-gray-300 rounded p-2 w-full" rows="4" placeholder="Provide a description of the damage"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex justify-end space-x-2 mt-4">
                                                <button id="renewButton-<?php echo $overall_index; ?>" class="bg-gray-300 text-gray-700 rounded px-2 py-1 text-sm renew-button"
                                                    data-role="<?php echo htmlspecialchars($book['role']); ?>"
                                                    data-way-of-borrow="<?php echo htmlspecialchars($book['Way_Of_Borrow']); ?>"
                                                    data-user-id="<?php echo htmlspecialchars($user_id); ?>"
                                                    data-book-id="<?php echo htmlspecialchars($book_id); ?>"
                                                    data-category="<?php echo htmlspecialchars($category); ?>"
                                                    data-due-date="<?php echo htmlspecialchars($due_date); ?>">
                                                    Renew
                                                </button>

                                                <button
                                                    id="payThisBookButton-<?php echo $overall_index; ?>"
                                                    class="bg-gray-300 text-gray-700 rounded px-2 py-1 text-sm payThisBook-button"
                                                    style="display: none;"
                                                    data-book-id="<?php echo htmlspecialchars($book_id, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-user-type="<?php echo htmlspecialchars($user_type, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-user-id="<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-due-date="<?php echo htmlspecialchars($due_date, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-fine-amount="<?php echo htmlspecialchars($fine_amount, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-accession-no="<?php echo htmlspecialchars($book['accession_no'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    Pay This Book
                                                </button>





                                                <button id="returnButton-<?php echo $overall_index; ?>"
                                                    class="bg-gray-300 text-gray-700 rounded px-2 py-1 text-sm return-button"
                                                    onclick="openReturnBookModal(
        '<?php echo htmlspecialchars($book_id); ?>',
        '<?php echo htmlspecialchars($user_type); ?>',
        '<?php echo htmlspecialchars($user_id); ?>',
        '<?php echo htmlspecialchars($category); ?>',
        '<?php echo htmlspecialchars($due_date); ?>',
        document.getElementById('fine-amount-<?php echo $overall_index; ?>').value,
        '<?php echo htmlspecialchars($book['accession_no']); ?>',
        'fine-amount-<?php echo $overall_index; ?>' // Pass the fine input ID
    )">
                                                    Return
                                                </button>









                                            </div>
                                        </li>



                                        <div id="payThisBookModal" class="fixed inset-0 hidden backdrop-blur-sm bg-black bg-opacity-30 z-50 flex items-center justify-center">
                                            <div class="bg-white rounded-lg p-6 w-1/3 space-y-4">
                                                <h2 class="text-2xl font-semibold mb-4">Pay for This Book</h2>
                                                <p><strong>Book ID:</strong> <span id="modalPayBookId"></span></p>
                                                <p><strong>User Type:</strong> <span id="modalPayUserType"></span></p>
                                                <p><strong>User ID:</strong> <span id="modalPayUserId"></span></p>
                                                <p><strong>Category:</strong> <span id="modalPayCategory"></span></p>
                                                <p><strong>Due Date:</strong> <span id="modalPayDueDate"></span></p>
                                                <p><strong>Fines:</strong> <span id="modalPayFineAmount"></span></p>
                                                <p><strong>Accession No:</strong> <span id="modalPayAccessionNo" style="white-space: pre-wrap;"></span></p>
                                                <div class="flex justify-end space-x-4 mt-4">
                                                    <button onclick="closePayThisBookModal()" class="bg-gray-500 text-white font-semibold py-2 px-4 rounded">Close</button>
                                                    <button onclick="processPayThisBook()" class="bg-blue-500 text-white py-2 px-4 rounded">Confirm Payment</button>
                                                </div>
                                            </div>
                                        </div>



                                        <div id="returnBookModal" class="fixed inset-0 hidden backdrop-blur-sm bg-black bg-opacity-30 z-50 flex items-center justify-center">
                                            <div class="bg-white rounded-lg p-6 w-1/3 space-y-4">
                                                <h2 class="text-2xl font-semibold mb-4">Return Book Details</h2>
                                                <p style="display: none;"><strong>Book ID:</strong> <span id="modalBookId"></span></p>
                                                <p style="display: none;"><strong>User Type:</strong> <span id="modalUserType"></span></p>
                                                <p style="display: none;"><strong>User ID:</strong> <span id="modalUserId"></span></p>

                                                <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                                                <p><strong>Due Date:</strong> <span id="modalDueDate"></span></p>
                                                <p><strong>Fines:</strong> <span id="modalFineAmount"></span></p>
                                                <p><strong>Accession No:</strong> <span id="modalAccessionNo" style="white-space: pre-wrap;"></span></p> <!-- Added Accession No here -->

                                                <div class="flex justify-end space-x-4 mt-4">
                                                    <button onclick="closeReturnBookModal()" class="bg-gray-500 text-white font-semibold py-2 px-4 rounded">Close</button>
                                                    <button onclick="processReturn()" class="bg-blue-500 text-white py-2 px-4 rounded">Confirm Return</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="damageBookModal" class="fixed inset-0 hidden backdrop-blur-sm bg-black bg-opacity-30 z-50 flex items-center justify-center">
                                            <div class="bg-white rounded-lg p-6 w-1/3 space-y-4">
                                                <h2 class="text-2xl font-semibold mb-4">Report Damage</h2>
                                                <p style="display: none;"><strong>Book ID:</strong> <span id="modalDamageBookId"></span></p>
                                                <p style="display: none;"><strong>User Type:</strong> <span id="modalDamageUserType"></span></p>
                                                <p style="display: none;"><strong>User ID:</strong> <span id="modalDamageUserId"></span></p>

                                                <p><strong>Category:</strong> <span id="modalDamageCategory"></span></p>
                                                <p><strong>Due Date:</strong> <span id="modalDamageDueDate"></span></p>
                                                <p><strong>Fines:</strong> <span id="modalDamageFineAmount"></span></p>
                                                <p><strong>Accession No:</strong> <span id="modalDamageAccessionNo" style="white-space: pre-wrap;"></span></p>
                                                <p><strong>Description of Damage:</strong></p>
                                                <textarea id="modalDamageDescription" class="border border-gray-300 rounded p-2 w-full" rows="4" readonly></textarea> <!-- Make readonly directly -->
                                                <div class="flex justify-end space-x-4 mt-4">
                                                    <button onclick="closeDamageBookModal()" class="bg-gray-500 text-white font-semibold py-2 px-4 rounded">Close</button>
                                                    <button onclick="processDamage()" class="bg-blue-500 text-white py-2 px-4 rounded">Confirm Damage</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="replacementBookModal" class="fixed inset-0 hidden backdrop-blur-sm bg-black bg-opacity-30 z-50 flex items-center justify-center">
                                            <div class="bg-white rounded-lg p-6 w-1/3 space-y-4">
                                                <h2 class="text-2xl font-semibold mb-4">Replacement Details</h2>

                                                <p style="display: none;"><strong>Book ID:</strong> <span id="replacementModalBookId"></span></p>
                                                <p style="display: none;"><strong>User Type:</strong> <span id="replacementModalUserType"></span></p>
                                                <p style="display: none;"><strong>User ID:</strong> <span id="replacementModalUserId"></span></p>
                                                <p><strong>Category:</strong> <span id="replacementModalCategory"></span></p>
                                                <p><strong>Due Date:</strong> <span id="replacementModalDueDate"></span></p>
                                                <p>
                                                    <strong>Accession No:</strong>
                                                    <span id="replacementModalAccessionNo" style="white-space: pre-wrap;"></span>
                                                </p>


                                                <div class="flex justify-end space-x-4 mt-4">
                                                    <button onclick="closeReplacementModal()" class="bg-gray-500 text-white font-semibold py-2 px-4 rounded">Close</button>
                                                    <button onclick="processReplacement()" class="bg-blue-500 text-white py-2 px-4 rounded">Confirm Replacement</button>
                                                </div>
                                            </div>
                                        </div>





                                        <script>
                                            // Open the modal and populate data
                                            function payThisBookModal(bookId, userType, userId, category, dueDate, initialFineAmount, accessionNo) {
                                                document.getElementById('modalPayBookId').innerText = bookId;
                                                document.getElementById('modalPayUserType').innerText = userType;
                                                document.getElementById('modalPayUserId').innerText = userId;
                                                document.getElementById('modalPayCategory').innerText = category;
                                                document.getElementById('modalPayDueDate').innerText = dueDate;
                                                document.getElementById('modalPayFineAmount').innerText = `₱${initialFineAmount}`;
                                                document.getElementById('modalPayAccessionNo').innerText = accessionNo;

                                                document.getElementById('payThisBookModal').classList.remove('hidden');
                                            }

                                            // Close the modal
                                            function closePayThisBookModal() {
                                                document.getElementById('payThisBookModal').classList.add('hidden');
                                            }

                                            // Process the payment
                                            function processPayThisBook() {
                                                const bookId = document.getElementById('modalPayBookId').innerText;
                                                const userType = document.getElementById('modalPayUserType').innerText.toLowerCase();
                                                const userId = document.getElementById('modalPayUserId').innerText;
                                                const category = document.getElementById('modalPayCategory').innerText;
                                                const accessionNo = document.getElementById('modalPayAccessionNo').innerText;
                                                const fineAmount = parseFloat(document.getElementById('modalPayFineAmount').innerText.replace('₱', '')) || 0;

                                                fetch('borrowed_books_2_pay.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                        },
                                                        body: JSON.stringify({
                                                            book_id: bookId,
                                                            user_type: userType,
                                                            user_id: userId,
                                                            category: category,
                                                            accession_no: accessionNo,
                                                            fine_amount: fineAmount,

                                                        }),
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            alert(data.message);
                                                            location.reload();
                                                        } else {
                                                            alert('Error: ' + data.message);
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                    });
                                            }
                                        </script>


                                        <script>
                                            function toggleStatusActions(index) {
                                                const statusSelect = document.getElementById(`statusSelect-${index}`);
                                                const damageTextArea = document.getElementById(`damageTextArea-${index}`);
                                                const fineHidden = document.getElementById(`finehidden-${index}`);


                                                const payThisBookButton = document.getElementById(`payThisBookButton-${index}`);

                                                const renewButton = document.getElementById(`renewButton-${index}`);
                                                const returnButton = document.getElementById(`returnButton-${index}`);
                                                const fineInput = document.getElementById(`fineInput-${index}`);
                                                const damageDescriptionInput = document.getElementById(`damageDescription-${index}`); // Capture the specific damage description input

                                                // Get data from the current list item
                                                const bookId = document.querySelector(`li[data-index="${index}"]`).getAttribute('data-book-id');
                                                const userType = "<?php echo htmlspecialchars($user_type); ?>";
                                                const userId = "<?php echo htmlspecialchars($user_id); ?>";

                                                const fineAmountElement = document.getElementById(`fine-amount-${index}`);
                                                const initialFineAmount = parseFloat(fineAmountElement.value) || 0;

                                                const category = "<?php echo htmlspecialchars($category); ?>";
                                                const dueDate = document.querySelector(`.due-date[data-index="${index}"]`).innerText;
                                                const accessionNo = document.getElementById(`accession_no-${index}`).innerText;
                                                const fineLabel = document.querySelector(`#fineLabel-${index}`); // Add ID to target the specific <p> tag


                                                if (statusSelect.value === "Damage") {
                                                    // Enable fine input for damage fines
                                                    fineInput.disabled = false;
                                                    fineInput.placeholder = "Enter additional fine amount";
                                                    fineInput.value = ""; // Clear any existing value
                                                    fineHidden.style.display = 'block'; // Show fine input
                                                    damageTextArea.style.display = 'block'; // Show damage description input
                                                    fineLabel.innerText = "Damage fines"; // Update label text
                                                    renewButton.style.display = 'none'; // Hide renew button
                                                    returnButton.innerText = "Pay"; // Update button text for payment
                                                    payThisBookButton.style.display = 'none'; // Hide "Pay This Book" button

                                                    // Function to calculate the total fine dynamically
                                                    const calculateTotalFine = () => {
                                                        const dueDateFine = parseFloat(fineAmountElement.value) || 0; // Get the value from "Due Date Fines"
                                                        const additionalFine = parseFloat(fineInput.value) || 0; // Get the additional fine from "Damage fines"
                                                        return dueDateFine + additionalFine; // Calculate total fine
                                                    };

                                                    // Update the `returnButton` action to dynamically compute and pass the total fine
                                                    returnButton.onclick = () => {
                                                        const totalFine = calculateTotalFine(); // Compute the total fine
                                                        openDamageBookModal(
                                                            bookId,
                                                            userType,
                                                            userId,
                                                            category,
                                                            dueDate,
                                                            totalFine, // Use the dynamically calculated total fine
                                                            accessionNo,
                                                            damageDescriptionInput.value // Pass the damage description
                                                        );
                                                    };

                                                    // Optional: Provide real-time feedback when user types additional fine
                                                    fineInput.addEventListener('input', () => {
                                                        const totalFine = calculateTotalFine();
                                                        console.log(`Total fine updated: ₱${totalFine}`); // For debugging
                                                    });
                                                } else if (statusSelect.value === "Lost") {










                                                    // Initial setup for the button with the calculated fine amount




                                                    // Enable fine input for replacement fees
                                                    fineInput.disabled = false;
                                                    fineInput.placeholder = "Enter replacement fee amount";
                                                    fineInput.value = ""; // Clear any existing value
                                                    fineHidden.style.display = 'block'; // Show fine input
                                                    damageTextArea.style.display = 'none'; // Hide damage description input
                                                    fineLabel.innerText = "Book Amount To Pay "; // Update label text
                                                    renewButton.style.display = 'none'; // Hide renew button
                                                    returnButton.innerText = "Replacement"; // Update button text for replacement
                                                    payThisBookButton.style.display = 'block'; // Show "Pay This Book" button

                                                    // Function to calculate the total fine dynamically
                                                    const calculateTotalFine = () => {
                                                        const dueDateFine = parseFloat(fineAmountElement.value) || 0; // Get the value from "Due Date Fines"
                                                        const replacementFee = parseFloat(fineInput.value) || 0; // Get the replacement fee from input
                                                        return dueDateFine + replacementFee; // Calculate total fine
                                                    };

                                                    // Update the `returnButton` action to dynamically compute and pass the total fine
                                                    returnButton.onclick = () => {
                                                        const totalFine = calculateTotalFine(); // Compute the total fine
                                                        openReplacementModal(
                                                            bookId,
                                                            userType,
                                                            userId,
                                                            category,
                                                            dueDate,
                                                            accessionNo
                                                        );
                                                    };

                                                    // Update the `payThisBookButton` action to dynamically compute and pass the total fine
                                                    payThisBookButton.onclick = () => {
                                                        const totalFine = calculateTotalFine(); // Compute the total fine
                                                        payThisBookModal(
                                                            bookId,
                                                            userType,
                                                            userId,
                                                            category,
                                                            dueDate,
                                                            totalFine, // Use the dynamically calculated total fine
                                                            accessionNo
                                                        );
                                                    };

                                                    // Optional: Provide real-time feedback when user types replacement fee
                                                    fineInput.addEventListener('input', () => {
                                                        const totalFine = calculateTotalFine();
                                                        console.log(`Total replacement fee updated: ₱${totalFine}`); // For debugging
                                                    });
                                                } else {

                                                    fineInput.disabled = true;
                                                    fineInput.value = "";
                                                    fineHidden.style.display = 'none';
                                                    payThisBookButton.style.display = 'none';

                                                    fineInput.placeholder = "Disabled";
                                                    damageTextArea.style.display = 'none';
                                                    renewButton.style.display = 'inline';
                                                    returnButton.innerText = "Return";
                                                    // Corrected onclick function to directly call openReturnBookModal with the necessary arguments
                                                    returnButton.onclick = function() {
                                                        openReturnBookModal(bookId, userType, userId, category, dueDate, initialFineAmount, accessionNo);
                                                    };
                                                }
                                            }
                                        </script>




                                        <script>
                                            function openReturnBookModal(bookId, userType, userId, category, dueDate, fineAmount) {
                                                document.getElementById('modalBookId').innerText = bookId;
                                                document.getElementById('modalUserType').innerText = userType;
                                                document.getElementById('modalUserId').innerText = userId;
                                                document.getElementById('modalCategory').innerText = category;
                                                document.getElementById('modalDueDate').innerText = dueDate;
                                                document.getElementById('modalFineAmount').innerText = `₱${fineAmount}`;

                                                document.getElementById('returnBookModal').classList.remove('hidden');
                                            }

                                            function closeReturnBookModal() {
                                                document.getElementById('returnBookModal').classList.add('hidden');
                                            }

                                            function processReturn() {
                                                const bookId = document.getElementById('modalBookId').innerText;
                                                const userType = document.getElementById('modalUserType').innerText.toLowerCase(); // Assumes userType matches `student`, `faculty`, or `walk_in`
                                                const userId = document.getElementById('modalUserId').innerText;
                                                const category = document.getElementById('modalCategory').innerText;
                                                const dueDate = document.getElementById('modalDueDate').innerText;
                                                const fineAmount = parseFloat(document.getElementById('modalFineAmount').innerText.replace('₱', '')) || 0;

                                                // Send data to PHP script for processing return
                                                fetch('borrowed_books_2_returnall.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                        },
                                                        body: JSON.stringify({
                                                            book_id: bookId,
                                                            category: category,
                                                            user_type: userType,
                                                            user_id: userId,
                                                            fineAmount: fineAmount
                                                        }),
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            alert(data.message);
                                                            location.reload(); // Reload the page to update the status of the returned book
                                                        } else {
                                                            alert('Error: ' + data.message);
                                                        }
                                                    })
                                                    .catch(error => console.error('Error:', error));
                                            }
                                        </script>






                                        <?php
                                        // Increment overall index for each book displayed
                                        $overall_index++;
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="flex items-center justify-end pr-36">
                                <button type="button" onclick="openPrintModal()" class="bg-blue-500 text-white font-bold py-2 px-4 rounded mr-8">Print Preview</button>

                                <button type="button" id="returnAllButton"
                                    class="bg-blue-500 text-white font-bold py-2 px-4 rounded mr-8"
                                    onclick="processReturnAll()">
                                    Return All
                                </button>



                                <script>
                                    function processReturnAll() {
                                        // Loop through each book and send individual requests for each
                                        document.querySelectorAll('li[data-book-id]').forEach((bookElement) => {
                                            const bookId = bookElement.getAttribute('data-book-id');
                                            const category = bookElement.getAttribute('data-category');
                                            const fineAmount = parseFloat(bookElement.getAttribute('data-fine-amount')) || 0;

                                            // Get the index to target the correct accession_no element
                                            const index = bookElement.getAttribute('data-index');
                                            const accessionNo = document.getElementById(`accession_no-${index}`).innerText;

                                            // Retrieve userType and userId from PHP
                                            const userType = "<?php echo $user_type; ?>";
                                            const userId = "<?php echo $user_id; ?>";

                                            // Send each book's data as an individual request to the server
                                            fetch('borrowed_books_2_returnall.php', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                    },
                                                    body: JSON.stringify({
                                                        book_id: bookId,
                                                        category: category,
                                                        fine_amount: fineAmount,
                                                        accession_no: accessionNo,
                                                        user_type: userType,
                                                        user_id: userId
                                                    }),
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (!data.success) {
                                                        alert(`Error for Book ID ${bookId}: ` + data.message);
                                                    }
                                                })
                                                .catch(error => console.error(`Error for Book ID ${bookId}:`, error));
                                        });

                                        // Alert the user after the loop completes
                                        alert('All book return requests have been sent.');
                                        location.reload(); // Reload to reflect changes after all requests
                                    }
                                </script>



                            </div>


                        </div>
                    <?php else: ?>
                        <p>No books available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <!-- Print Modal Structure -->





    <script>
        function openReturnBookModal(bookId, userType, userId, category, dueDate, fineAmount, accessionNo) {
            document.getElementById('modalBookId').innerText = bookId;
            document.getElementById('modalUserType').innerText = userType;
            document.getElementById('modalUserId').innerText = userId;
            document.getElementById('modalCategory').innerText = category;
            document.getElementById('modalDueDate').innerText = dueDate;
            document.getElementById('modalFineAmount').innerText = `₱${fineAmount}`;
            document.getElementById('modalAccessionNo').innerText = accessionNo; // Set accession number

            document.getElementById('returnBookModal').classList.remove('hidden');
        }


        function processReturn() {
            const bookId = document.getElementById('modalBookId').innerText;
            const userType = document.getElementById('modalUserType').innerText.toLowerCase(); // Assumes userType matches `student`, `faculty`, or `walk_in`
            const userId = document.getElementById('modalUserId').innerText;
            const category = document.getElementById('modalCategory').innerText;
            const dueDate = document.getElementById('modalDueDate').innerText;
            const fineAmount = parseFloat(document.getElementById('modalFineAmount').innerText.replace('₱', '')) || 0;
            const accessionNo = document.getElementById('modalAccessionNo').innerText; // Get the accession number from the modal

            // Send data to PHP script for processing return
            fetch('borrowed_books_2_returnall.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        category: category,
                        user_type: userType,
                        user_id: userId,
                        fineAmount: fineAmount, // Send the fine amount if applicable
                        accession_no: accessionNo // Include the accession number
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Reload the page to update the status of the returned book
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>



    <script>
        function openDamageBookModal(bookId, userType, userId, category, dueDate, fineAmount, accessionNo, damageDescription) {
            document.getElementById('modalDamageBookId').innerText = bookId;
            document.getElementById('modalDamageUserType').innerText = userType;
            document.getElementById('modalDamageUserId').innerText = userId;
            document.getElementById('modalDamageCategory').innerText = category;
            document.getElementById('modalDamageDueDate').innerText = dueDate;
            document.getElementById('modalDamageFineAmount').innerText = `₱${fineAmount.toFixed(2)}`;
            document.getElementById('modalDamageAccessionNo').innerText = accessionNo;

            // Display the damage description in a read-only textarea in the modal
            const descriptionDisplay = document.getElementById('modalDamageDescription');
            descriptionDisplay.value = damageDescription;
            descriptionDisplay.setAttribute("readonly", true); // Make the textarea read-only

            // Show the damage modal
            document.getElementById('damageBookModal').classList.remove('hidden');

            // Add click event to the "Pay" button to alert the description
            const confirmButton = document.querySelector('.confirm-damage-button');
            confirmButton.onclick = () => {
                alert(`Damage Description: ${damageDescription}`);
            };
        }



        function closeDamageBookModal() {
            document.getElementById('damageBookModal').classList.add('hidden');
        }


        function processDamage() {
            const bookId = document.getElementById('modalDamageBookId').innerText;
            const userType = document.getElementById('modalDamageUserType').innerText.toLowerCase();
            const userId = document.getElementById('modalDamageUserId').innerText;
            const accessionNo = document.getElementById('modalDamageAccessionNo').innerText;
            const fineAmount = parseFloat(document.getElementById('modalDamageFineAmount').innerText.replace('₱', '')) || 0;
            const category = document.getElementById('modalDamageCategory').innerText;
            const damageDescription = document.getElementById('modalDamageDescription').value;

            fetch('borrowed_books_2_damage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        user_type: userType,
                        user_id: userId,
                        accession_no: accessionNo,
                        fine_amount: fineAmount,
                        category: category,
                        damage_description: damageDescription
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        // Alert the detailed error message
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    // Catch network or fetch-specific errors
                    alert('Network or Fetch Error: ' + error.message);
                });
        }
    </script>


    <script>
        function openReplacementModal(bookId, userType, userId, category, dueDate, accessionNo) {
            document.getElementById('replacementModalBookId').innerText = bookId;
            document.getElementById('replacementModalUserType').innerText = userType;
            document.getElementById('replacementModalUserId').innerText = userId;
            document.getElementById('replacementModalCategory').innerText = category;
            document.getElementById('replacementModalDueDate').innerText = dueDate;
            document.getElementById('replacementModalAccessionNo').innerText = accessionNo;

            document.getElementById('replacementBookModal').classList.remove('hidden');
        }

        function closeReplacementModal() {
            document.getElementById('replacementBookModal').classList.add('hidden');
        }

        function processReplacement() {
            const bookId = document.getElementById('replacementModalBookId').innerText;
            const userType = document.getElementById('replacementModalUserType').innerText.toLowerCase();
            const userId = document.getElementById('replacementModalUserId').innerText;
            const category = document.getElementById('replacementModalCategory').innerText;
            const accessionNo = document.getElementById('replacementModalAccessionNo').innerText;

            fetch('borrowed_books_2_replace.php', {
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
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Success message
                        location.reload();
                    } else {
                        // Display detailed error message
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    // Catch network or fetch-specific errors
                    alert('Network or Fetch Error: ' + error.message);
                });
        }
    </script>


    <!-- Print Modal Structure with Blur Effect -->
    <div id="printModal" class="fixed inset-0 flex items-center justify-center hidden backdrop-blur-sm bg-black bg-opacity-30 z-50">
        <div id="modalContent" class="bg-white rounded-lg p-6 w-1/3 space-y-4">
            <h2 class="text-2xl font-semibold mb-4">Print Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="allBooksName">NAME:</label>
                    <p id="allBooksName" class="w-full border border-gray-300 rounded p-2 bg-gray-100"></p>
                </div>
                <div class="space-y-2">
                    <label for="allBooksDate">DATE:</label>
                    <p id="allBooksDate" class="w-full border border-gray-300 rounded p-2 bg-gray-100"></p>
                </div>
            </div>
            <div class="space-y-2">
                <label for="allBooksCount">NO. OF BOOKS BORROWED:</label>
                <p id="allBooksCount" class="w-full border border-gray-300 rounded p-2 bg-gray-100"></p>
            </div>
            <div class="space-y-2">
                <label for="totalOverdueDays">TOTAL DAYS OVERDUE:</label>
                <p id="totalOverdueDays" class="w-full border border-gray-300 rounded p-2 bg-gray-100"></p>
            </div>
            <div class="space-y-2">
                <label for="totalFinesAmount">TOTAL AMOUNT TO BE PAID:</label>
                <p id="totalFinesAmount" class="w-full border border-gray-300 rounded p-2 bg-gray-100"></p>
            </div>

            <!-- Checkbox List for Books -->
            <div class="space-y-2">
                <h3>Select Books to Include in Total Fines:</h3>
                <div id="bookCheckboxList"></div> <!-- This div will be populated by JavaScript -->
            </div>

            <div class="flex justify-end space-x-4">
                <button onclick="closePrintModal()" class="bg-gray-500 text-white font-semibold py-2 px-4 rounded">Close</button>
                <button type="button" onclick="PrintPage()" class="bg-blue-500 text-white py-2 px-4 rounded">Print</button>

            </div>
        </div>
    </div>









    <script>
        function PrintPage() {
            // Get data directly from the Replacement Modal fields
            const name = document.getElementById('allBooksName').innerText;
            const date = document.getElementById('allBooksDate').innerText;
            const books = document.getElementById('allBooksCount').innerText;
            const days = document.getElementById('totalOverdueDays').innerText;
            const amount = document.getElementById('totalFinesAmount').innerText;

            // Create URL with parameters



            const url = `a4.php?name=${encodeURIComponent(name)}&date=${encodeURIComponent(date)}&books=${encodeURIComponent(books)}&days=${encodeURIComponent(days)}&amount=${encodeURIComponent(amount)}`;

            // Open a4.php with parameters in a new tab
            window.open(url, '_blank');
        }
    </script>

    <script>
        // Function to open the Print Modal
        // Function to open the Print Modal
        function openPrintModal() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("allBooksName").innerText = "<?php echo $fullName; ?>";
            document.getElementById("allBooksDate").innerText = today;
            document.getElementById("allBooksCount").innerText = document.querySelectorAll('li[data-book-id]').length;

            // Generate checkboxes for each book with overdue days
            const bookCheckboxList = document.getElementById("bookCheckboxList");
            bookCheckboxList.innerHTML = ""; // Clear existing checkboxes

            document.querySelectorAll('li[data-book-id]').forEach((bookElement, index) => {
                const bookId = bookElement.getAttribute('data-book-id');
                const fineAmount = parseFloat(bookElement.querySelector(`input[id^="fine-amount-"]`).value) || 0;


                // Calculate overdue days for each book
                const dueDateStr = bookElement.querySelector('.due-date').innerText;
                const dueDate = new Date(dueDateStr);
                const today = new Date();
                const overdueDays = (today > dueDate) ? Math.floor((today - dueDate) / (1000 * 60 * 60 * 24)) : 0;

                // Create checkbox for each book
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = `bookCheckbox-${index}`;
                checkbox.value = fineAmount;
                checkbox.dataset.overdueDays = overdueDays; // Store overdue days in a custom data attribute
                checkbox.checked = true; // Default to checked

                // Add an event listener to update the fine and overdue day calculations in real-time
                checkbox.addEventListener('change', () => {
                    calculateTotalFineAmount();
                    calculateTotalOverdueDays();
                });

                // Get the additional fine amount from the fine input, if available
                const fineInput = document.getElementById(`fineInput-${index}`);
                const additionalFine = fineInput && !fineInput.disabled && fineInput.value ? parseFloat(fineInput.value) : 0;

                // Create a label for the checkbox
                const label = document.createElement('label');
                label.htmlFor = `bookCheckbox-${index}`;
                label.innerText = `Book ${index + 1} - Fine: ₱${fineAmount}` +
                    (additionalFine > 0 ? `, Additional Fine: ₱${additionalFine}` : '') +
                    `, Overdue Days: ${overdueDays}`;

                // Container for checkbox and label
                const container = document.createElement('div');
                container.appendChild(checkbox);
                container.appendChild(label);

                // Add the checkbox container to the modal
                bookCheckboxList.appendChild(container);
            });

            // Initialize the total fine amount and overdue days calculation
            calculateTotalFineAmount();
            calculateTotalOverdueDays();

            // Display the modal
            document.getElementById("printModal").classList.remove("hidden");
        }


        // Function to close the Print Modal
        function closePrintModal() {
            document.getElementById("printModal").classList.add("hidden");
        }

        // Function to calculate the total overdue days
        function calculateTotalOverdueDays() {
            let totalOverdueDays = 0;

            // Iterate through each checkbox to add overdue days only for selected books
            document.querySelectorAll('input[id^="bookCheckbox-"]').forEach((checkbox) => {
                if (checkbox.checked) {
                    totalOverdueDays += parseInt(checkbox.dataset.overdueDays) || 0;
                }
            });

            // Display the updated overdue days in the modal
            document.getElementById("totalOverdueDays").innerText = totalOverdueDays;
        }

        // Function to calculate the total fine amount
        function calculateTotalFineAmount() {
            let totalFineAmount = 0;

            // Iterate through each book checkbox to calculate total fines
            document.querySelectorAll('input[id^="bookCheckbox-"]').forEach((checkbox, index) => {
                if (checkbox.checked) {
                    // Get default fine amount from the checkbox value
                    let fineAmount = parseFloat(checkbox.value) || 0;

                    // Check if there is a custom fine input for this item (if "Damage" is selected)
                    const fineInput = document.getElementById(`fineInput-${index}`);
                    if (fineInput && !fineInput.disabled && fineInput.value) {
                        // Add the custom fine to the total if entered
                        fineAmount += parseFloat(fineInput.value) || 0;
                    }

                    // Accumulate the fine for this book into the total
                    totalFineAmount += fineAmount;
                }
            });

            // Display the updated fine amount in the modal
            document.getElementById("totalFinesAmount").innerText = `₱${totalFineAmount.toFixed(2)}`;
        }

        document.getElementById("printModal").addEventListener("click", function(event) {
            const modalContent = document.getElementById("modalContent");
            if (!modalContent.contains(event.target)) {
                closePrintModal();
            }
        });
    </script>











    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const renewButtons = document.querySelectorAll('.renew-button');

            renewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const bookId = this.getAttribute('data-book-id');
                    const category = this.getAttribute('data-category');
                    const role = this.getAttribute('data-role');
                    const wayOfBorrow = this.getAttribute('data-way-of-borrow');
                    const newDueDate = this.parentElement.parentElement.querySelector('.due-date').innerText;

                    const studentId = role === 'Student' && wayOfBorrow === 'online' ? userId : null;
                    const facultyId = role === 'Faculty' && wayOfBorrow === 'online' ? userId : null;
                    const walkInId = wayOfBorrow === 'walk-in' ? userId : null;

                    const formattedDueDate = new Date(newDueDate).toISOString().split('T')[0]; // Format YYYY-MM-DD

                    // Prepare data based on wayOfBorrow and role
                    const data = {
                        way_of_borrow: wayOfBorrow,
                        role: role,
                        due_date: formattedDueDate,
                        book_id: bookId,
                        category: category
                    };

                    // Add the specific user ID based on role
                    if (role === 'Student' && wayOfBorrow === 'online') data.student_id = studentId;
                    if (role === 'Faculty' && wayOfBorrow === 'online') data.faculty_id = facultyId;
                    if (wayOfBorrow === 'walk-in') data.walk_in_id = walkInId;

                    console.log("Data sent to server:", data); // Log data for debugging

                    // Send data to server
                    fetch('borrowed_books_2_save.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Renewed successfully!');
                                location.reload();
                            } else {
                                alert('Error updating due date: ' + data.message);
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                });
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {





            const form = document.getElementById('book-request-form');

            form.addEventListener('change', function(event) {
                // Handle renewal dropdown changes
                if (event.target.classList.contains('renew-dropdown')) {
                    const renewalDropdown = event.target;
                    const renewalDays = parseInt(renewalDropdown.value, 10);
                    const dueDateStr = renewalDropdown.getAttribute('data-due-date');
                    const finesValue = <?php echo $fines_value; ?>; // Get the fines value from PHP

                    // Parse the due date
                    const currentDueDate = new Date(dueDateStr + 'T00:00:00');

                    // Calculate the new due date
                    const newDueDate = new Date(currentDueDate);
                    newDueDate.setDate(currentDueDate.getDate() + renewalDays);

                    const options = {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    };
                    const formattedDueDate = newDueDate.toLocaleDateString('en-CA', options);

                    // Get the index and update the due date display
                    const index = renewalDropdown.getAttribute('data-index');
                    const dueDateElement = form.querySelector(`.due-date[data-index="${index}"]`);

                    if (dueDateElement) {
                        dueDateElement.innerText = formattedDueDate;
                    }

                    // Calculate the fine amount based on the new due date
                    const currentDate = new Date();
                    let fineAmount = 0;

                    if (currentDate > newDueDate) {
                        // Calculate overdue days
                        const overdueDays = Math.floor((currentDate - newDueDate) / (1000 * 60 * 60 * 24));
                        fineAmount = overdueDays * finesValue;
                    }

                    // Update the fine amount display, rounding to the nearest integer
                    const fineAmountElement = document.getElementById(`fine-amount-${index}`);
                    if (fineAmountElement) {
                        fineAmountElement.innerText = Math.round(fineAmount); // Use Math.round for nearest integer
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select the "Return All" button element
            const returnAllButton = document.getElementById('returnAllButton');

            // Select all status dropdowns
            const statusDropdowns = document.querySelectorAll('select[id^="statusSelect-"]');

            // Function to check if any dropdown has selected "Damage" or "Lost"
            function checkStatusDropdowns() {
                let shouldHideReturnAll = false;

                // Loop through each dropdown to check the selected value
                statusDropdowns.forEach((dropdown) => {
                    if (dropdown.value === "Damage" || dropdown.value === "Lost") {
                        shouldHideReturnAll = true;
                    }
                });

                // Hide or show the "Return All" button based on the condition
                returnAllButton.style.display = shouldHideReturnAll ? 'none' : 'inline-block';
            }

            // Attach event listener to each dropdown to monitor changes
            statusDropdowns.forEach((dropdown) => {
                dropdown.addEventListener('change', checkStatusDropdowns);
            });

            // Initial check in case any dropdown is already set to "Damage" or "Lost"
            checkStatusDropdowns();
        });
    </script>

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