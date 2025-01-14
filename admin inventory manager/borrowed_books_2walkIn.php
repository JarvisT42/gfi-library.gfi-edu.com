<?php
session_start();
include '../connection.php'; // Ensure you have your database connection
include '../connection2.php'; // Ensure you have your database connection

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
        SELECT a.Category, a.book_id, a.Issued_Date, a.Due_Date, a.role, a.Way_Of_Borrow
        FROM borrow AS a
        WHERE " . ($user_type === 'student' ? "a.student_id" : ($user_type === 'faculty' ? "a.faculty_id" : "a.walk_in_id")) . " = ? 
        AND status = 'borrowed'";

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
        $fullNameQuery = "SELECT Full_Name, role FROM borrow WHERE walk_in_id = ?";
        $stmtWalkIn = $conn->prepare($fullNameQuery);
        $stmtWalkIn->bind_param('i', $user_id);
        $stmtWalkIn->execute();
        $walkInResult = $stmtWalkIn->get_result();

        if ($walkInResult->num_rows > 0) {
            $walkInRow = $walkInResult->fetch_assoc();
            $fullName = $walkInRow['Full_Name'];
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
                                        $status = 'Unknown Status';
                                        $record_cover = null;

                                        if ($row = $result->fetch_assoc()) {
                                            $title = $row['Title'];
                                            $author = $row['Author'];
                                            $status = $row['Status'];
                                            $record_cover = $row['record_cover'];
                                        }
                                        $stmt2->close();
                                        include '../connection.php';

                                        // Fetch fine amount
                                        $fines_value = 0;
                                        $sql = "SELECT fines FROM library_fines LIMIT 1";
                                        $result = $conn->query($sql);
                                        if ($result && $result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $fines_value = (int)$row['fines'];
                                        }

                                        $issued_date = $book['Issued_Date'];
                                        $due_date = empty($book['Due_Date']) ? date('Y-m-d', strtotime($issued_date . ' + 3 days')) : $book['Due_Date'];
                                        $current_date = date('Y-m-d');

                                        // Calculate fine amount and round to the nearest whole number
                                        $fine_amount = ($current_date > $due_date) ? ((strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24)) * $fines_value : 0;
                                        $fine_amount = round($fine_amount); // Round to the nearest whole number

                                        ?>

                                        <li class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg mb-2 flex flex-col"
                                            data-book-id="<?php echo htmlspecialchars($book_id); ?>"
                                            data-category="<?php echo htmlspecialchars($category); ?>"
                                            data-fine-amount="<?php echo $fine_amount; ?>"
                                            data-index="<?php echo $overall_index; ?>">



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
                                                    </div>



                                                    <!-- Book Cover Image -->
                                                    <div class="w-full md:w-32 h-40 bg-gray-200 border border-gray-300 flex items-center justify-center mb-4 md:mb-0">
                                                        <?php
                                                        if (!empty($record_cover)) {
                                                            $imageData = base64_encode($record_cover);
                                                            $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                                                        } else {
                                                            $imageSrc = 'path/to/default/image.jpg';
                                                        }
                                                        ?>
                                                        <img src="<?php echo $imageSrc; ?>" alt="Book Cover" class="w-full h-full border-2 border-gray-400 rounded-lg object-cover transition-transform duration-200 transform hover:scale-105">
                                                    </div>
                                                </div>
                                                <!-- Book Information (Issued Date, Due Date, etc.) -->
                                                <div class="bg-blue-100 p-4 rounded-lg">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                                        <div>
                                                            <p class="text-sm font-semibold">Issued Date:</p>
                                                            <p class="text-sm"><?php echo htmlspecialchars($book['Issued_Date']); ?></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold">Due Date:</p>
                                                            <p class="text-sm due-date" data-index="<?php echo $overall_index; ?>"><?php echo htmlspecialchars($due_date); ?></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold">Fines: ₱ <span id="fine-amount-<?php echo $overall_index; ?>"><?php echo $fine_amount; ?></span></p>
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
                                                        <div>
                                                            <p class="text-sm font-semibold">Fines:</p>
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
                                                    id="returnButton-<?php echo $overall_index; ?>"
                                                    class="bg-gray-300 text-gray-700 rounded px-2 py-1 text-sm return-button"
                                                    data-user-type="<?php echo htmlspecialchars($user_type); ?>"
                                                    data-user-id="<?php echo htmlspecialchars($user_id); ?>"
                                                    data-book-id="<?php echo htmlspecialchars($book_id); ?>"
                                                    data-category="<?php echo htmlspecialchars($category); ?>"
                                                    onclick="handleReturnClick(this)">
                                                    Return
                                                </button>
                                            </div>
                                        </li>





                                        <!-- Modal Structure -->
                                        <!-- Modal Structure -->
                                        <div id="payModal" class="modal hidden">
                                            <div class="modal-content">
                                                <h2>Payment for Fines</h2>
                                                <p><strong>Name:</strong> <span id="name"></span></p>
                                                <p><strong>Date:</strong> <span id="date"></span></p>
                                                <p><strong>NO. OF BOOK/S BORROWED:</strong> <span id="books"></span></p>
                                                <p><strong>DAY/S OVERDUE:</strong> <span id="days"></span></p>
                                                <p><strong>TOTAL AMOUNT TO BE PAID:</strong> <span id="amount"></span></p>

                                                <!-- Container for individual book details -->
                                                <div id="bookDetails" class="space-y-4"></div>

                                                <!-- Buttons and other controls here -->
                                                <button onclick="closePayModal()">Close</button>
                                            </div>
                                        </div>




                                        <script>
                                            // Toggle elements based on status selection
                                            function toggleStatusActions(index) {
                                                const statusSelect = document.getElementById(`statusSelect-${index}`);
                                                const fineInput = document.getElementById(`fineInput-${index}`);
                                                const damageTextArea = document.getElementById(`damageTextArea-${index}`);
                                                const renewButton = document.getElementById(`renewButton-${index}`);
                                                const returnButton = document.getElementById(`returnButton-${index}`);

                                                if (statusSelect.value === "Damage") {
                                                    fineInput.disabled = false;
                                                    fineInput.placeholder = "";
                                                    fineInput.value = "";
                                                    damageTextArea.style.display = 'block';
                                                    renewButton.style.display = 'none';
                                                    returnButton.innerText = "Pay";
                                                    returnButton.onclick = () => openPayModal(index, "Damage");
                                                } else if (statusSelect.value === "Lost") {
                                                    fineInput.disabled = true;
                                                    fineInput.value = "";
                                                    fineInput.placeholder = "Disabled";
                                                    damageTextArea.style.display = 'none';
                                                    renewButton.style.display = 'none';
                                                    returnButton.innerText = "Replacement";

                                                    // Get the due date from the specific list item
                                                    const dueDateText = document.querySelector(`.due-date[data-index="${index}"]`).innerText;

                                                    // Open Replacement Modal for all books with the same due date
                                                    returnButton.onclick = () => openReplacementModal(dueDateText);
                                                } else {
                                                    fineInput.disabled = true;
                                                    fineInput.value = "";
                                                    fineInput.placeholder = "Disabled";
                                                    damageTextArea.style.display = 'none';
                                                    renewButton.style.display = 'inline';
                                                    returnButton.innerText = "Return";
                                                }
                                            }

                                            // Function to open the payment modal and calculate total fines for books with the same due date
                                            function openPayModal(index, status) {
    // Get the due date of the selected book
    const dueDate = document.querySelector(`.due-date[data-index="${index}"]`).innerText;
    const bookItems = document.querySelectorAll(`.due-date`); // Select all books to check for matching due dates

    let totalFines = 0;
    let bookCount = 0;
    let totalDaysOverdue = 0;
    let bookDetailsHTML = ""; // HTML content to store each book's details

    // Today's date for calculating overdue days
    const today = new Date();
    const dueDateObj = new Date(dueDate);

    // Loop through each book item and find matching due dates
    bookItems.forEach((dueDateEl, i) => {
        if (dueDateEl.innerText === dueDate) { // Only consider books with the same due date
            const bookId = dueDateEl.closest('li').getAttribute('data-book-id');
            const category = dueDateEl.closest('li').getAttribute('data-category');
            const overdueFine = parseFloat(document.getElementById(`fine-amount-${i}`).innerText) || 0;

            // Accumulate fines and book count
            totalFines += overdueFine;
            bookCount++;

            // Calculate overdue days
            if (today > dueDateObj) {
                const daysOverdue = Math.floor((today - dueDateObj) / (1000 * 60 * 60 * 24));
                totalDaysOverdue += daysOverdue;
            }

            // Add each book's details to the HTML content
            bookDetailsHTML += `
                <div class="book-info">
                    <p><strong>Book ID:</strong> ${bookId}</p>
                    <p><strong>Category:</strong> ${category}</p>
                    <hr>
                </div>
            `;
        }
    });

    // Populate modal fields with calculated values and each book's details
    document.getElementById("name").innerText = "<?php echo $fullName; ?>";
    document.getElementById("date").innerText = today.toISOString().split('T')[0];
    document.getElementById("amount").innerText = `₱${totalFines.toFixed(2)}`;
    document.getElementById("books").innerText = bookCount;
    document.getElementById("days").innerText = totalDaysOverdue;

    // Insert book details into the modal
    document.getElementById("bookDetails").innerHTML = bookDetailsHTML;

    // Show the modal
    document.getElementById("payModal").classList.remove("hidden");
}






                                            // Function to close the payment modal
                                            function closePayModal() {
                                                document.getElementById("payModal").classList.add("hidden");
                                            }

                                            // Function to handle payment confirmation
                                            function confirmPayment(index) {
                                                // Retrieve user type and ID from PHP variables
                                                const userType = "<?php echo $user_type; ?>";
                                                const userId = "<?php echo $user_id; ?>";

                                                // Select all li elements with the specified index
                                                const bookItems = document.querySelectorAll(`li[data-index="${index}"]`);

                                                // Prepare book data with respective fine amounts for the specific index
                                                const books = [];
                                                let message = `User Type: ${userType}\nUser ID: ${userId}\nBooks to be processed:\n\n`;

                                                bookItems.forEach((li) => {
                                                    const bookId = li.getAttribute('data-book-id');
                                                    const category = li.getAttribute('data-category');
                                                    const fineAmountElement = li.querySelector(`span[id^="fine-amount-"]`);
                                                    const totalFines = fineAmountElement ? parseFloat(fineAmountElement.innerText.replace('₱', '').trim()) || 0 : 0;

                                                    // Append each book's information to the alert message
                                                    message += `Book ID: ${bookId}\n`;
                                                    message += `  Category: ${category}\n`;
                                                    message += `  Total Fines: ₱${totalFines.toFixed(2)}\n\n`;

                                                    books.push({
                                                        book_id: bookId,
                                                        category: category,
                                                        total_fines: totalFines
                                                    });
                                                });

                                                // Display the message in an alert for verification
                                                if (books.length > 0) {
                                                    alert(message); // Shows all books in this index group in a single alert
                                                } else {
                                                    alert("No books found for the selected index.");
                                                }
                                            }







                                            // Close modal
                                            function closePayModal() {
                                                document.getElementById("payModal").classList.add("hidden");
                                            }

                                            // Handle payment confirmation
                                        </script>



                                        <?php
                                        // Increment overall index for each book displayed
                                        $overall_index++;
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="flex items-center justify-end pr-36">

                                <button type="button" onclick="openReturnAllModal()" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Return All</button>
                            </div>


                        </div>
                    <?php else: ?>
                        <p>No books available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        function handleReturnClick(button) {
            // Retrieve values from the button's data attributes
            const userType = button.getAttribute('data-user-type');
            const userId = button.getAttribute('data-user-id');
            const bookId = button.getAttribute('data-book-id');
            const category = button.getAttribute('data-category');

            // Retrieve the fine amount from the DOM based on the book's index
            const fineAmountElement = document.querySelector(`li[data-book-id="${bookId}"] span[id^="fine-amount-"]`);
            const totalFines = fineAmountElement ? parseFloat(fineAmountElement.innerText.replace('₱', '').trim()) || 0 : 0;


            // Alert the information for confirmation (optional)
            alert(`User Type: ${userType}\nUser ID: ${userId}\nBook ID: ${bookId}\nCategory: ${category}\nTotal Fines: ${totalFines}`);

            // Prepare the URL dynamically based on userType and userId
            const url = `borrowed_books_2_returnall.php?${userType}_id=${userId}`;

            // Prepare data for the AJAX request, including fines
            const data = {
                books: [{
                    book_id: bookId,
                    category: category,
                    total_fines: totalFines // Send the fine amount for this book
                }]
            };

            // Send AJAX request to process the return
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.success) {
                        alert(`Book returned successfully for ${userType}_id=${userId}`);
                        location.reload(); // Optionally reload the page to update the interface
                    } else {
                        alert('Error: ' + responseData.message);
                        console.log('Server Response:', responseData); // Log server response for more details
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while processing the return.');
                });
        }
    </script>

    <script>
        function openReturnAllModal() {

            const fineInputs = document.querySelectorAll('.due-date');
            let totalFineAmount = 0;
            let totalBookCount = 0;
            let totalDaysOverdue = 0;

            // Get today's date for calculating overdue days
            const today = new Date();

            fineInputs.forEach((dueDateEl, i) => {
                const dueDateText = dueDateEl.innerText;
                const fineAmount = parseFloat(document.getElementById(`fine-amount-${i}`).innerText) || 0;
                totalFineAmount += fineAmount;
                totalBookCount++;

                // Calculate and accumulate overdue days for each book
                const dueDate = new Date(dueDateText);
                if (today > dueDate) {
                    const daysOverdue = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));
                    totalDaysOverdue += daysOverdue;
                }
            });

            // Populate the Return All Modal fields
            document.getElementById("allBooksName").innerText = "<?php echo $fullName; ?>"; // Set user's full name
            document.getElementById("allBooksDate").innerText = today.toISOString().split('T')[0]; // Today's date
            document.getElementById("allBooksCount").innerText = totalBookCount; // Total books
            document.getElementById("totalOverdueDays").innerText = totalDaysOverdue; // Total days overdue
            document.getElementById("totalFinesAmount").innerText = `₱${totalFineAmount.toFixed(2)}`; // Display total fine amount

            // Show the Return All Modal
            document.getElementById("returnAllModal").classList.remove("hidden");
        }

        function closeReturnAllModal() {
            document.getElementById("returnAllModal").classList.add("hidden");
        }

        function confirmReturnAll() {

            const userType = "<?php echo $user_type; ?>"; // Set from PHP variable
            const userId = "<?php echo $user_id; ?>"; // Set from PHP variable


            // Prepare book data from the li elements
            const books = [];
            const bookItems = document.querySelectorAll('li[data-book-id]'); // Select all li elements with a data-book-id attribute

            bookItems.forEach((li, i) => {
                const bookId = li.getAttribute('data-book-id');
                const category = li.getAttribute('data-category');
                const totalFines = parseFloat(li.getAttribute('data-fine-amount')) || 0;

                books.push({
                    book_id: bookId,
                    category: category,
                    total_fines: totalFines
                });

                // Alert data for verification
                // alert(`Book ${i + 1} - Total Fines: ${totalFines}, Book ID: ${bookId}, Category: ${category}`);
            });

            // Prepare data for AJAX
            const data = {
                books: books
            };

            // Determine the correct user parameter based on user type
            let url = `borrowed_books_2_returnall.php?${userType}_id=${userId}`;


            // Send AJAX request
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.success) {
                        alert(`All books returned successfully for ${userType}_id=${userId}`);
                        closeReturnAllModal();
                        location.reload(); // Refresh the page if needed
                    } else {
                        alert('Error: ' + responseData.message);
                        console.log('Server Response:', responseData); // Log for more details
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while processing the return.');
                });

        }
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



</body>

</html>