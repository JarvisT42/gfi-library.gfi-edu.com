<?php
session_start();
include '../connection.php'; // Ensure you have your database connection
include '../connection2.php'; // Ensure you have your database connection

if (isset($_GET['student_id'])) {
    $student_id = htmlspecialchars($_GET['student_id']);

    // Check if student_id is set in the query parameters




    // Fetch the category, book_id, and student details based on the student_id
    $categoryQuery = "
    SELECT a.Category, a.book_id, a.Issued_Date, a.Due_Date,Over_Due_Fines
    FROM GFI_Library_Database.borrow AS a
    WHERE a.student_id = ? and status ='lost'";

    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param('i', $student_id); // Assuming student_id is an integer
    $stmt->execute();

    $result = $stmt->get_result();

    // Fetch all data into an array
    $books = $result->fetch_all(MYSQLI_ASSOC) ?: []; // Use short-circuit evaluation for empty check
    // Fetch student details for full name
    $studentQuery = "
    SELECT First_Name, Middle_Initial, Last_Name 
    FROM GFI_Library_Database.students 
    WHERE student_id = ?";
    $stmtStudent = $conn->prepare($studentQuery);
    $stmtStudent->bind_param('i', $student_id);
    $stmtStudent->execute();
    $studentResult = $stmtStudent->get_result();

    if ($studentResult->num_rows > 0) {
        $studentRow = $studentResult->fetch_assoc();
        $fullName = $studentRow['First_Name'] . ' ' . $studentRow['Middle_Initial'] . ' ' . $studentRow['Last_Name'];
    } else {
        $fullName = 'Unknown Student'; // Fallback if no student found
    }
    $stmtStudent->close();

    // Group books by Date_To_Claim


    $stmt->close(); // Close the first statement


} else {
    // Handle the case where student_id is not provided
    echo "No student ID provided.";
    exit; // Stop execution if no student_id
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
                                        $overDueFines = $book['Over_Due_Fines'];

                                        // Fetch the Title, Author, and record_cover from conn2 based on book_id
                                        $titleQuery = "SELECT * FROM `$category` WHERE id = ?";
                                        $stmt2 = $conn2->prepare($titleQuery);
                                        $stmt2->bind_param('i', $book_id);
                                        $stmt2->execute();
                                        $result = $stmt2->get_result();

                                        // Initialize variables
                                        $title = 'Unknown Title';
                                        $author = 'Unknown Author';
                                        $status = 'Unknown Status';

                                        $record_cover = null; // Initialize with null

                                        if ($row = $result->fetch_assoc()) {
                                            $title = $row['Title']; // Get the title
                                            $author = $row['Author']; // Get the author
                                            $status = $row['Status']; // Get the author
                                            $record_cover = $row['record_cover']; // Get the record cover
                                        }

                                        $stmt2->close();
                                        include '../connection.php';

                                        // Get the fines value from the database
                                        $fines_value = 0;
                                        $sql = "SELECT fines FROM library_fines LIMIT 1";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $fines_value = (int)$row['fines'];
                                        }






                                        // Get the issued date from the book array
                                        $issued_date = $book['Issued_Date'];

                                        if (empty($book['Due_Date'])) {
                                            // Calculate the due date (3 days after the issued date)
                                            $due_date = date('Y-m-d', strtotime($issued_date . ' + 3 days'));
                                        } else {
                                            // Use the existing due date from $book['Due_Date']
                                            $due_date = $book['Due_Date'];
                                        }

                                        // Now $due_date has the correct value based on the conditions



                                        // Calculate the fines based on the due date
                                        $current_date = date('Y-m-d');
                                        $fine_amount = 0;

                                        if ($current_date > $due_date) {
                                            // Calculate overdue days
                                            $overdue_days = (strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24);
                                            $fine_amount = $overdue_days * $fines_value;
                                        }
                                        ?>

                                        <li class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg mb-2 flex flex-col">
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
                                                <div class="bg-blue-100 p-4 rounded-lg">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4 hidden">
    <div>
        <p class="text-sm font-semibold">Issued Date:</p>
        <p class="text-sm"><?php echo htmlspecialchars($book['Issued_Date']); ?></p>
    </div>
    <div>
        <p class="text-sm font-semibold">Due Date:</p>
        <p class="text-sm due-date" data-index="<?php echo $overall_index; ?>"><?php echo htmlspecialchars($due_date); ?></p>
    </div>
    <div>
        <p class="text-sm font-semibold">Fines: ₱ <span id="fine-amount-<?php echo $overall_index; ?>"><?php echo $overDueFines; ?></span>.00</p>
    </div>
</div>

                                                    <div class="flex justify-end space-x-2 mt-4">
                                                <!-- <button class="bg-gray-300 text-gray-700 rounded px-2 py-1 text-sm renew-button"
                                                    data-student-id="<?php echo htmlspecialchars($student_id); ?>"
                                                    data-book-id="<?php echo htmlspecialchars($book_id); ?>"
                                                    data-category="<?php echo htmlspecialchars($category); ?>"
                                                    data-due-date="<?php echo htmlspecialchars($due_date); ?>">
                                                    Renew
                                                </button> -->

                                                <button class="bg-gray-300 text-gray-700 rounded px-2 py-1 text-sm return-button"
                                                    data-index="<?php echo $overall_index; ?>"
                                                    onclick="openReturnModal('<?php echo htmlspecialchars($title); ?>', '<?php echo htmlspecialchars($author); ?>', '<?php echo htmlspecialchars($category); ?>', '<?php echo $fine_amount; ?>', 'fineInput-<?php echo $overall_index; ?>', '<?php echo htmlspecialchars($student_id); ?>', '<?php echo htmlspecialchars($book_id); ?>')">
                                                    Replace
                                                </button>
                                            </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                                        <div style="display: none;">
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
                                                        <div style="display: none;">
                                                            <p class="text-sm font-semibold">Book Status:</p>
                                                            <select id="statusSelect-<?php echo $overall_index; ?>" class="border border-gray-300 rounded p-1 mr-16" onchange="toggleFinesInput(<?php echo $overall_index; ?>)">
                                                                <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                                                                <option value="Damage">Damage</option>
                                                                <option value="Lost">Lost</option>
                                                            </select>
                                                        </div>
                                                        <div style="display: none;">
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

                                                    
                                                    <!-- Text area for damage description -->

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

                            <div class="flex items-center justify-end">




                                <button type="button" onclick="openReturnAllModal()" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Return All</button>
                            </div>
                        </div>

                    <?php else: ?>
                        <p>No books available.</p>
                    <?php endif; ?>


                </div>
            </div>
        </div>

        <!-- Modal for "Return All" -->
        <!-- Modal for "Return All" -->
        <div id="returnAllModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-11/12 md:w-1/3">
                <h2 class="text-lg font-semibold mb-4">Return All Books</h2>

                <!-- Display No. of Books Borrowed -->
                <p id="booksBorrowedCount" class="mb-4 font-semibold"></p>

                <!-- Display Total Overdue Fines -->
                <p id="totalOverdueFines" class="mb-4 font-semibold"></p>

                <div class="flex justify-end space-x-2">
                    <button id="closeReturnAllModal" class="bg-red-500 text-white py-2 px-4 rounded">Close</button>
                    <button id="confirmReturnAll" class="bg-blue-500 text-white py-2 px-4 rounded">Confirm Return All</button>
                </div>
            </div>
        </div>



        <script>
            function toggleFinesInput(index) {
                const statusSelect = document.getElementById(`statusSelect-${index}`);
                const fineInput = document.getElementById(`fineInput-${index}`);
                const returnButton = document.querySelector(`.return-button[data-index="${index}"]`);
                const damageTextArea = document.getElementById(`damageTextArea-${index}`); // Text area for damage description

                // Enable fine input for "Damage" or "Lost"
                if (statusSelect.value === "Damage" || statusSelect.value === "Lost") {
                    fineInput.disabled = false; // Enable the fine input
                    fineInput.placeholder = ""; // Clear the placeholder
                    console.log(`Enabling fine input for index: ${index}`);
                } else {
                    fineInput.disabled = true; // Disable the fine input
                    fineInput.value = ""; // Clear the input value
                    fineInput.placeholder = "Disabled"; // Reset the placeholder
                    console.log(`Disabling fine input for index: ${index}`);
                }

                // Change the button label to 'Next' if 'Lost' is selected
                if (statusSelect.value === "Lost") {
                    returnButton.innerText = "Next";
                    damageTextArea.style.display = 'none'; // Hide the text area if "Lost" is selected
                } else if (statusSelect.value === "Damage") {
                    returnButton.innerText = "Return"; // Set to 'Return'
                    damageTextArea.style.display = 'block'; // Show the text area for "Damage"
                } else {
                    returnButton.innerText = "Return"; // Reset to 'Return' for other statuses
                    damageTextArea.style.display = 'none'; // Hide the text area for other statuses
                }
            }
        </script>


        <script>
            // Function to open the 'Return All' modal
            // Function to open the 'Return All' modal

            // Function to open the 'Return All' modal
            function openReturnAllModal() {
                // Get all the book titles and fines from the displayed list
                const bookTitles = document.querySelectorAll('li .text-2xl.font-bold + p'); // Select all title elements
                const fineSpans = document.querySelectorAll('[id^="fine-amount-"]'); // Select all spans with ID starting with "fine-amount-"
                const fineInputs = document.querySelectorAll('.finesInput'); // Select all fine inputs (e.g., for damage or lost books)
                const booksBorrowedCount = document.getElementById('booksBorrowedCount'); // Element to display the book count
                const totalOverdueFines = document.getElementById('totalOverdueFines'); // Element to display the total overdue fines

                // Calculate the number of books
                const numberOfBooks = bookTitles.length;
                booksBorrowedCount.innerText = `NO. OF BOOK/S BORROWED: ${numberOfBooks}`;

                // Calculate the total fines from both span elements and fine input fields
                let totalFines = 0;

                // Add fines from the span elements (fines already displayed)
                fineSpans.forEach(fineSpan => {
                    const fineAmount = parseFloat(fineSpan.innerText) || 0; // Parse the fine value or default to 0
                    totalFines += fineAmount;
                });

                // Add fines from the input fields (for damage/lost fines)
                fineInputs.forEach(fineInput => {
                    const fineAmount = parseFloat(fineInput.value) || 0; // Parse the fine value or default to 0
                    totalFines += fineAmount;
                });

                // Display the total overdue fines in the modal
                totalOverdueFines.innerText = `TOTAL ALL FINES: ₱ ${totalFines.toFixed(2)}`;

                // Show the modal
                document.getElementById('returnAllModal').classList.remove('hidden');
            }

            // Close the modal when the "Close" button is clicked
            document.getElementById('closeReturnAllModal').onclick = function() {
                document.getElementById('returnAllModal').classList.add('hidden');
            };

            // Confirm return logic for 'Return All'
            // Confirm return logic for 'Return All'
            document.getElementById('confirmReturnAll').onclick = function() {
                // Get the student ID (assuming it's available in the page somewhere)
                const studentId = <?php echo json_encode($student_id); ?>;

                // Gather all book data: book IDs, categories, and fines
                const books = [];
                const fineSpans = document.querySelectorAll('[id^="fine-amount-"]'); // Select all fine amount spans
                const fineInputs = document.querySelectorAll('.finesInput'); // Select all fine inputs (for damage or lost)

                fineSpans.forEach((fineSpan, index) => {
                    const bookId = fineSpan.closest('li').querySelector('.renew-button').getAttribute('data-book-id'); // Get book_id
                    const category = fineSpan.closest('li').querySelector('.renew-button').getAttribute('data-category'); // Get category
                    const fineAmount = parseFloat(fineSpan.innerText) || 0; // Get fine amount from span

                    // Check if there's an additional fine from input (damage or lost)
                    const inputFine = fineInputs[index] ? parseFloat(fineInputs[index].value) || 0 : 0;
                    const totalFines = fineAmount + inputFine; // Sum both fine values

                    // Add this book's data to the books array
                    books.push({
                        book_id: bookId,
                        category: category,
                        total_fines: totalFines
                    });
                });

                // Create the data object to send
                const data = {
                    student_id: studentId,
                    books: books
                };

                // Send the data to the backend using fetch
                fetch('borrowed_books_2online_returnall.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('All books returned successfully!');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Error: ' + result.message);
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        alert('An error occurred while returning the books.');
                    });

                // Close the modal after sending the request
                document.getElementById('returnAllModal').classList.add('hidden');
            };
        </script>







        <div id="returnModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-11/12 md:w-1/3">
                <h2 class="text-lg font-semibold mb-4">Return Book</h2>
                <p id="modalBookTitle" class="mb-2"></p>
                <p id="modalBookAuthor" class="mb-2"></p>
                <p id="modalBookCategory" class="mb-2"></p>

                <!-- Original fines display (unchanged) -->
                <p id="OverDueFines" class="mb-4 hidden"></p>

                <!-- Updated fines display -->
                <p id="BookFines" class="mb-4 hidden"></p>

                <!-- Damage description (only shown if applicable) -->
                <p id="damageDescriptionLabel" class="mb-2" style="display:none;"><strong>Damage Description:</strong></p>
                <p id="damageDescription" class="mb-4" style="display:none;"></p>

                <!-- Display for student ID and book ID -->
                <p id="modalStudentId" class="mb-2 hidden"></p>
                <p id="modalBookId" class="mb-2 hidden"></p>


                <div class="flex justify-end space-x-2">
                    <button id="closeModal" class="bg-red-500 text-white py-2 px-4 rounded">Close</button>
                    <button id="confirmReturn" class="bg-blue-500 text-white py-2 px-4 rounded">Confirm Return</button>
                </div>
            </div>
        </div>



    </main>


















    <script>
        function openReturnModal(title, author, category, fines, fineInputId, walkinId, bookId) {
            // Set the book details in the modal
            document.getElementById('modalBookTitle').innerText = 'Title: ' + title;
            document.getElementById('modalBookAuthor').innerText = 'Author: ' + author;
            document.getElementById('modalBookCategory').innerText = 'Category: ' + category;

            // Display the original fines value (read-only)
            document.getElementById('OverDueFines').innerText = 'Over Due Fines: ₱ ' + fines;

            // Get the value from the fine input field and display it in the modal
            let updatedFines = document.getElementById(fineInputId).value; // Get value from the specific fines input field
            document.getElementById('BookFines').innerText = 'Book Fines: ₱ ' + (updatedFines || '0'); // Display updated fines or 0 if none

            // Display the walkin ID and book ID in the modal
            document.getElementById('modalWalkinId').innerText = 'Walkin ID: ' + walkinId; // Correct ID usage
            document.getElementById('modalBookId').innerText = 'Book ID: ' + bookId;

            // Check if the button clicked was "Next" (which means the book is marked as "Lost")
            const statusSelect = document.getElementById(`statusSelect-${fineInputId.split('-')[1]}`).value; // Get the status of the book

            // Update the label of the "Confirm Return" button to "Pay" if the status is "Lost"
            const confirmButton = document.getElementById('confirmReturn');
            if (statusSelect === "Lost") {
                confirmButton.innerText = 'Book Lost'; // Change the label to 'Pay'
            } else {
                confirmButton.innerText = 'Confirm Return'; // Reset to default
            }

            // Display the modal
            document.getElementById('returnModal').classList.remove('hidden');
        }


        function openReturnModal(title, author, category, fines, fineInputId, studentId, bookId) {
            // Set the book details in the modal
            document.getElementById('modalBookTitle').innerText = 'Title: ' + title;
            document.getElementById('modalBookAuthor').innerText = 'Author: ' + author;
            document.getElementById('modalBookCategory').innerText = 'Category: ' + category;

            // Display the original fines value (read-only)
            document.getElementById('OverDueFines').innerText = 'Over Due Fines: ₱ ' + fines;

            // Get the value from the fine input field and display it in the modal
            let updatedFines = document.getElementById(fineInputId).value; // Get value from the specific fines input field
            document.getElementById('BookFines').innerText = 'Book Fines: ₱ ' + (updatedFines || '0'); // Display updated fines or 0 if none

            // Display the student ID and book ID in the modal
            document.getElementById('modalStudentId').innerText = 'Student ID: ' + studentId;
            document.getElementById('modalBookId').innerText = 'Book ID: ' + bookId;

            // Check if the button clicked was "Next" (which means the book is marked as "Lost")
            const statusSelect = document.getElementById(`statusSelect-${fineInputId.split('-')[1]}`).value; // Get the status of the book

            // Update the label of the "Confirm Return" button to "Pay" if the status is "Lost"
            const confirmButton = document.getElementById('confirmReturn');
            if (statusSelect === "Lost") {
                confirmButton.innerText = 'Book Lost'; // Change the label to 'Pay'
                document.getElementById('damageDescriptionLabel').style.display = 'none'; // Hide damage description label
                document.getElementById('damageDescription').style.display = 'none'; // Hide damage description
            } else if (statusSelect === "Damage") {
                confirmButton.innerText = 'Confirm Return'; // Set to 'Return'

                // Show and set the damage description if applicable
                let damageDesc = document.getElementById(`damageDescription-${fineInputId.split('-')[1]}`).value; // Get the damage description
                document.getElementById('damageDescriptionLabel').style.display = 'block';
                document.getElementById('damageDescription').style.display = 'block';
                document.getElementById('damageDescription').innerText = damageDesc || 'No description provided'; // Set the damage description or fallback
            } else {
                confirmButton.innerText = 'Confirm Return'; // Reset to default
                document.getElementById('damageDescriptionLabel').style.display = 'none'; // Hide damage description label
                document.getElementById('damageDescription').style.display = 'none'; // Hide damage description
            }

            // Display the modal
            document.getElementById('returnModal').classList.remove('hidden');
        }



        // Close modal when the close button is clicked
        document.getElementById('closeModal').onclick = function() {
            document.getElementById('returnModal').classList.add('hidden');
        }

        // Confirm return or pay logic
        document.getElementById('confirmReturn').onclick = function() {
            const overdueFines = document.getElementById('OverDueFines').innerText.replace('Over Due Fines: ₱ ', ''); // Extract overdue fines
            const bookFines = document.getElementById('BookFines').innerText.replace('Book Fines: ₱ ', ''); // Correct extraction of book fines
            const studentId = document.getElementById('modalStudentId').innerText.replace('Student ID: ', '');
            const bookId = document.getElementById('modalBookId').innerText.replace('Book ID: ', '');
            const category = document.getElementById('modalBookCategory').innerText.replace('Category: ', '');
            const damageDesc = document.getElementById('damageDescription').innerText; // Extract damage description

            const data = {
                fines: parseFloat(overdueFines) || 0,
                book_fines: parseFloat(bookFines) || 0,
                student_id: studentId,
                book_id: bookId,
                category: category,
                damage_description: damageDesc // Include damage description
            };

            const confirmButton = document.getElementById('confirmReturn');
            if (confirmButton.innerText === 'Book Lost') {
                fetch('borrowed_books_2online_pay.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Payment processed successfully!');
                            location.reload(); // Reload the page after successful payment
                        } else {
                            alert('Error: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing the payment.');
                    });
            } else {
                fetch('lost_book_online_save.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Book returned successfully!');
                            location.reload(); // Reload the page
                        } else {
                            alert('Error: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while returning the book.');
                    });
            }

            // Close the modal after the action
            document.getElementById('returnModal').classList.add('hidden');
        };
    </script>










    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const renewButtons = document.querySelectorAll('.renew-button');

            renewButtons.forEach(button => {
                button.addEventListener('click', function() {

                    const studentId = this.getAttribute('data-student-id');
                    const bookId = this.getAttribute('data-book-id');
                    const category = this.getAttribute('data-category');
                    const newDueDate = this.parentElement.parentElement.querySelector('.due-date').innerText;

                    // Create the data to send
                    const data = {
                        student_id: studentId,
                        book_id: bookId, // updated from title and author
                        category: category,
                        due_date: newDueDate
                    };

                    // Make an AJAX request to renew.php
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
                                location.reload(); // Reload the page

                            } else {
                                alert('Error renewing book: ' + data.message);
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                });
            });
        });
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