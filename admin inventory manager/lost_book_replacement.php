<?php
# Initialize the session
session_start();
if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>
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
    <style>
        /* Force underline when the peer is checked */
        input:checked+label {
            text-decoration: underline;
        }

        /* Custom shadow and smooth transition */
        label {
            transition: color 0.3s ease, text-decoration 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Radio button appearance customization */
        input:checked+label {
            background-color: #3b82f6;
            /* Blue background for checked option */
            color: white;
            /* White text for checked option */
            border-color: #3b82f6;
        }
    </style>
    <style>
        .scrollable-table-container {
            overflow-y: auto;
            height: 560px;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">

                <!-- Description Box -->


                <!-- Title Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Lost Book Pending Replacement</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>



                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    This feature provides a comprehensive summary of your book borrowing history. It includes a detailed log of all books you've borrowed in the past, along with a current overview of books you have on loan. </div>
                <!-- Main Content Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div class="flex items-center space-x-4">




                            <div class="flex space-x-6">
                                <!-- First Radio Option -->
                                <div class="flex items-center">
                                    <input checked id="inline-checked-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-checked-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        Pending
                                    </label>
                                </div>

                                
                            </div>







                            <!-- Dropdown and Button -->
                            <div class="relative hidden" id="dropdownContainer">
                                <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                    <svg class="w-3 h-3 text-gray-500 dark:text-gray-400 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z" />
                                    </svg>
                                    Last 7 days
                                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>
                                <!-- Dropdown menu -->
                                <div id="dropdownRadio" class="z-20 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">

                                        <li>
                                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input checked="" id="filter-radio-example-2" type="radio" value="" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="filter-radio-example-2" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Last 7 days</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input id="filter-radio-example-3" type="radio" value="" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="filter-radio-example-3" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Last 30 days</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input id="filter-radio-example-4" type="radio" value="" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="filter-radio-example-4" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Last month</label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input id="filter-radio-example-5" type="radio" value="" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="filter-radio-example-5" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Last year</label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                        </div>

                        <!-- Search Input -->
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="text" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for student's name" onkeyup="filterTables()">
                        </div>
                    </div>







                    <div id="table1" class="overflow-x-auto">
                        <div class="scrollable-table-container relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
                            <table id="borrowed-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/4">Student Name</th>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/4">User Category</th>

                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/4">Way of Borrow</th>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/3">Course</th>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/12">Number of Books Borrowed</th>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/5">Issued Date</th>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/6">Due Date</th>
                                        <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/6">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="borrowed-table-body">
                                    <!-- Borrowed books data will be displayed here -->
                                    <?php
                                    include '../connection.php';  // Ensure you have your database connection

                                    // Get today's date
                                    $today = date('Y-m-d');

                                    // Fetch book_id and category for entries that exceed 3 days





                            $sql = "SELECT 
    b.student_id,  
    b.faculty_id,  
    b.Way_Of_Borrow,
    b.walk_in_id,
    b.role,
    s.course_id,  -- Course ID from the student table
    c.course,     -- Course name from the course table
    CASE 
        WHEN b.Way_Of_Borrow = 'online' AND b.role = 'Student' THEN CONCAT(s.First_Name, ' ', s.Last_Name)
        WHEN b.Way_Of_Borrow = 'online' AND b.role = 'Faculty' THEN CONCAT(f.First_Name, ' ', f.Last_Name)
        WHEN b.Way_Of_Borrow = 'walk-in' THEN w.full_name
        ELSE '' 
    END AS First_Name,
    CASE 
        WHEN b.Way_Of_Borrow = 'online' AND b.role = 'Student' THEN c.course
        WHEN b.Way_Of_Borrow = 'online' AND b.role = 'Faculty' THEN 'n/a'
        WHEN b.Way_Of_Borrow = 'walk-in' THEN 'n/a'
        ELSE '' 
    END AS Course,
    b.Due_Date,
    b.Issued_Date,
    
    -- Calculate total borrow count by summing individual counts
    (COUNT(b.student_id) + COUNT(b.faculty_id) + COUNT(b.walk_in_id)) AS borrow_count,

    MIN(CASE 
        WHEN b.Due_Date IS NULL THEN DATE_ADD(b.Issued_Date, INTERVAL 3 DAY)
        ELSE b.Due_Date
    END) AS nearest_date,
    MIN(b.Time) AS Time  
FROM borrow b
LEFT JOIN students s ON b.student_id = s.Student_Id
LEFT JOIN faculty f ON b.faculty_id = f.Faculty_Id
LEFT JOIN walk_in_borrowers w ON b.walk_in_id = w.walk_in_id
LEFT JOIN course c ON s.course_id = c.course_id
WHERE b.status = 'lost'
GROUP BY b.Way_Of_Borrow, b.student_id, b.faculty_id, b.role, s.course_id, b.walk_in_id, b.Due_Date, b.Issued_Date";







                                    $borrowData = $conn->query($sql);
                                    ?>
                                    <?php if ($borrowData && $borrowData->num_rows > 0): ?>
                                        <?php while ($row = $borrowData->fetch_assoc()): ?>
                                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">


                                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white break-words student-name" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($row['First_Name']); ?> </td>
                                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['role']); ?></td>

                                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['Way_Of_Borrow']); ?></td>




                                                <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($row['Course']); ?> </td>
                                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['borrow_count']); ?></td>
                                                <td class="px-6 py-4">
                                                    <?php
                                                    // Format the nearest date
                                                    $nearestDate = new DateTime($row['Issued_Date']);
                                                    // Format the date as 'F j, Y' and get the day of the week
                                                    $formattedDate = $nearestDate->format('F j, Y') . ' - ' . $nearestDate->format('l');

                                                    // Output the formatted date along with the Time value if available
                                                    echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($row['Time']);
                                                    ?>
                                                </td>


                                                <td class="px-6 py-4">
                                                    <?php
                                                    // Format the nearest date
                                                    $nearestDate = new DateTime($row['nearest_date']);
                                                    // Format the date as 'F j, Y' and get the day of the week
                                                    $formattedDate = $nearestDate->format('F j, Y') . ' - ' . $nearestDate->format('l');

                                                    // Output the formatted date along with the Time value if available
                                                    echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($row['Time']);
                                                    ?>
                                                </td>




                                                <td class="px-6 py-4">
                                                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                        onclick="redirectToBookRequest('<?php echo htmlspecialchars($row['role']); ?>', '<?php echo htmlspecialchars($row['Way_Of_Borrow']); ?>', '<?php echo htmlspecialchars($row['walk_in_id'] ?? ''); ?>', '<?php echo htmlspecialchars($row['student_id'] ?? ''); ?>', '<?php echo htmlspecialchars($row['faculty_id'] ?? ''); ?>')">
                                                        Next
                                                    </button>
                                                </td>




                                            </tr>



                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center">No records found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <script>
                                function redirectToBookRequest(role, wayOfBorrow, walkInId, studentId, facultyId) {
                                    let url = 'lost_book.php?';

                                    // Check if it's a walk-in borrow
                                    if (wayOfBorrow === 'Walk-in') {
                                        if (role === 'Faculty' && walkInId) {
                                            // For faculty walk-ins, use faculty_id
                                            url += 'walk_in_id=' + walkInId;
                                        } else if (role === 'Student' && walkInId) {
                                            // For student walk-ins, use walk_in_id
                                            url += 'walk_in_id=' + walkInId;
                                        } else {
                                            alert("Unable to determine the correct ID for walk-in user.");
                                            return;
                                        }
                                    } else {
                                        // For online borrow, use student_id or faculty_id
                                        if (role === 'Faculty' && facultyId) {
                                            url += 'faculty_id=' + facultyId;
                                        } else if (role === 'Student' && studentId) {
                                            url += 'student_id=' + studentId;
                                        } else {
                                            alert("Unable to determine the correct ID for online user.");
                                            return;
                                        }
                                    }

                                    // Redirect to the constructed URL
                                    window.location.href = url;
                                }
                            </script>


                        </div>
                    </div>

                    









                </div>
            </div>

        </div>

    </main>


    <script>
        function filterTables() {
            // Declare variables
            let input = document.getElementById("table-search");
            let filter = input.value.toLowerCase();

            // Call filtering for both tables
            filterTable("borrowed-table");
            filterTable("returned-table");

            // Filtering function for individual tables
            function filterTable(tableId) {
                let table = document.getElementById(tableId);
                let rows = table.getElementsByTagName("tr");

                // Loop through all table rows, and hide those that don't match the search query
                for (let i = 1; i < rows.length; i++) { // Skipping header row (index 0)
                    let td = rows[i].getElementsByClassName("student-name")[0]; // Look for class 'student-name'
                    if (td) {
                        let txtValue = td.textContent || td.innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            rows[i].style.display = ""; // Show row
                        } else {
                            rows[i].style.display = "none"; // Hide row
                        }
                    }
                }
            }
        }
    </script>
    <script>
        // Event listener for the first radio button
        document.getElementById('inline-radio').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('table1').classList.add('hidden');
                document.getElementById('table2-container').classList.remove('hidden');
                document.getElementById('dropdownContainer').classList.remove('hidden');
            }
        });

        // Event listener for the second radio button
        document.getElementById('inline-checked-radio').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('table1').classList.remove('hidden');
                document.getElementById('table2-container').classList.add('hidden');
                document.getElementById('dropdownContainer').classList.add('hidden');
            }
        });

        // Toggle dropdown visibility
        document.getElementById('dropdownRadioButton').addEventListener('click', function() {
            const dropdown = document.getElementById('dropdownRadio');
            dropdown.classList.toggle('hidden');
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