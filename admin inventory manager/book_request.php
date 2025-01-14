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
        .active-book-request {
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
                    <h1 class="text-3xl font-semibold">Activity Log</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>



                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    This feature provides a comprehensive summary of your book borrowing history. It includes a detailed log of all books you've borrowed in the past, along with a current overview of books you have on loan. </div>
                <!-- Main Content Box -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 p-4 bg-gray-200 dark:bg-gray-900 sm:rounded-lg">

                        <div class="flex items-center space-x-4 ">




                            <div class="flex space-x-6 ">
                                <!-- First Radio Option -->
                                <!-- First Radio Option: Pending -->
                                <div class="flex items-center">
                                    <input checked id="inline-checked-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-checked-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        Borrower Request
                                    </label>
                                </div>

                                <!-- Second Radio Option: History -->
                                <div class="flex items-center">
                                    <input id="inline-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        ready to claim
                                    </label>
                                </div>

                            </div>







                            <!-- Dropdown and Button -->


                        </div>

                        <!-- Search Input -->

                    </div>







                    <div id="table1" class="overflow-x-auto">
                        <div class="scrollable-table-container relative overflow-x-auto shadow-md sm:rounded-lg pt-2 pr-6 pb-2 pl-4  border border-gray-300">

                            <table id="borrowed-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300">

                                <thead class="text-xs text-gray-700 uppercase bg-blue-400 ">
                                 
                                    <tr>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Full Name</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">User Category</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Course</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Quantity And Accession No.</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Date To Claim</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300">Action</th>
                                    </tr>
                              
                                </thead>
                                <tbody id="borrowed-table-body">
                                    <?php

                                    include '../connection.php';  // Database connection for main DB
                                    include '../connection2.php';  // Additional connection if needed

                                    // Get today's date
                                    $today = date('Y-m-d');

                                    // Query to fetch accession_no of records that need to be updated
                                    $accessionQuery = "SELECT accession_no
                                    FROM borrow
                                    WHERE Date_To_Claim < DATE_SUB('$today', INTERVAL 3 DAY) AND status = 'pending'";
                                    $accessionResult = $conn->query($accessionQuery);

                                    // Update the `borrow` table status to 'failed-to-claim'
                                    $updateSql = "UPDATE borrow
                                    SET status = 'failed-to-claim'
                                    WHERE Date_To_Claim < DATE_SUB('$today', INTERVAL 3 DAY) AND status = 'pending'";
                                    $conn->query($updateSql);

                                    // Update the `accession_records` table for affected rows
                                    if ($accessionResult->num_rows > 0) {
                                        while ($row = $accessionResult->fetch_assoc()) {
                                            $accessionNo = $row['accession_no'];
                                            $updateAccessionSql = "UPDATE accession_records
                                            SET available = 'yes', borrower_id = NULL
                                            WHERE accession_no = ?";
                                            $stmt = $conn->prepare($updateAccessionSql);
                                            $stmt->bind_param('s', $accessionNo);
                                            $stmt->execute();
                                            $stmt->close();
                                        }
                                    }

                                    // Query to get student records
                                    $studentSql = "SELECT
                                    s.First_Name,
                                    s.Middle_Initial,
                                    s.Last_Name,
                                    c.course,
                                    b.student_id,
                                    b.role,

                                    MIN(b.Time) AS Time,
                                    COUNT(b.student_id) AS borrow_count,
                                    MIN(b.Date_To_Claim) AS nearest_date
                                FROM borrow b
                                JOIN students s ON b.student_id = s.Student_Id
                                JOIN course c ON s.course_id = c.course_id
                                WHERE b.status = 'pending'
                                GROUP BY b.student_id, s.First_Name, s.Middle_Initial, s.Last_Name, c.course, b.role";

                                    $studentResult = $conn->query($studentSql);

                                    // Query to get faculty records
                                    $facultySql = "SELECT
                                        f.First_Name,
                                        f.Middle_Initial,
                                        f.Last_Name,
                                        b.faculty_id,
                                        b.role,
                                        

                                        MIN(b.Time) AS Time,
                                        COUNT(b.faculty_id) AS borrow_count,
                                        MIN(b.Date_To_Claim) AS nearest_date
                                    FROM borrow b
                                    JOIN faculty f ON b.faculty_id = f.Faculty_Id
                                    WHERE b.status = 'pending'
                                    GROUP BY b.faculty_id, f.First_Name, f.Middle_Initial, f.Last_Name, b.role";
                                    $facultyResult = $conn->query($facultySql);

                                    // Array to store combined records
                                    $records = [];
                                    if ($studentResult->num_rows > 0) {
                                        while ($row = $studentResult->fetch_assoc()) {
                                            $row['user_type'] = 'student'; // Mark as student
                                            $records[] = $row;
                                        }
                                    }
                                    if ($facultyResult->num_rows > 0) {
                                        while ($row = $facultyResult->fetch_assoc()) {
                                            $row['user_type'] = 'faculty'; // Mark as faculty
                                            $records[] = $row;
                                        }
                                    }


                                    ?>
                                    <?php if (!empty($records)): ?>
                                        <?php foreach ($records as $record): ?>
                                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">
                                                <td class="px-6 py-4 border border-gray-300"> <?php echo htmlspecialchars($record['First_Name'] . ' ' . $record['Middle_Initial'] . ' ' . $record['Last_Name']); ?></td>
                                                <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($record['role']); ?></td>
                                                <td class="px-6 py-4 border border-gray-300"> <?php
                                                                                                // Display S_Course only if it exists (for students)
                                                                                                echo $record['user_type'] === 'student' ? htmlspecialchars($record['course']) : 'N/A';
                                                                                                ?></td>
                                                <td class="px-6 py-4 border border-gray-300">
                                                    <?php
                                                    // Display quantity (borrow count)
                                                    echo htmlspecialchars($record['borrow_count']);

                                                    // Fetch all the accession numbers for the current record
                                                    $accessionQuery = "SELECT accession_no FROM borrow WHERE student_id = ? AND status = 'pending' ";
                                                    if ($record['user_type'] === 'faculty') {
                                                        $accessionQuery = "SELECT accession_no FROM borrow WHERE faculty_id = ? AND status = 'pending' ";
                                                    }

                                                    $stmt = $conn->prepare($accessionQuery);
                                                    if ($record['user_type'] === 'student') {
                                                        $stmt->bind_param('i', $record['student_id']);
                                                    } else {
                                                        $stmt->bind_param('i', $record['faculty_id']);
                                                    }
                                                    $stmt->execute();
                                                    $accessionResult = $stmt->get_result();
                                                    $accessionNumbers = [];
                                                    while ($accessionRow = $accessionResult->fetch_assoc()) {
                                                        $accessionNumbers[] = $accessionRow['accession_no'];
                                                    }
                                                    $stmt->close();

                                                    // Display the accession numbers next to the quantity
                                                    if (!empty($accessionNumbers)) {
                                                        echo '<br>Accessions: ' . implode(', ', $accessionNumbers);
                                                    }
                                                    ?>
                                                </td>
                                                <td class="px-6 py-4 border border-gray-300"> <?php
                                                                                                // Format the nearest date
                                                                                                $nearestDate = new DateTime($record['nearest_date']);
                                                                                                // Format the date as 'F j, Y' and get the day of the week
                                                                                                $formattedDate = $nearestDate->format('F j, Y') . ' - ' . $nearestDate->format('l');

                                                                                                // Output the formatted date along with the Time value if available
                                                                                                echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($record['Time']);
                                                                                                ?></td>
                                                <td class="px-6 py-4 border-r border-gray-300">
                                                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                        onclick="redirectToBookRequest('<?php echo htmlspecialchars($record['user_type']); ?>', '<?php echo htmlspecialchars($record['user_type'] === 'student' ? $record['student_id'] : $record['faculty_id']); ?>')">
                                                        Next
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php endif; ?>
                                </tbody>
                            </table>



                            <!-- <script>
                                $(document).ready(function() {
                                    $('#borrowed-table').DataTable({
                                        responsive: true,
                                        paging: true,
                                        searching: true,
                                        info: true,
                                        order: [],
                                        columnDefs: [{
                                            orderable: false,
                                            targets: 5 // "Action" column
                                        }],
                                    });
                                });
                            </script> -->
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
                                        }]
                                    });
                                });
                            </script>


                            <script>
                                function redirectToBookRequest(userType, userId) {
                                    // Redirect to book_request_2.php with appropriate ID parameter based on user type
                                    const param = userType === 'student' ? 'student_id' : 'faculty_id';
                                    window.location.href = 'book_request_2.php?' + param + '=' + userId;
                                }
                            </script>



                        </div>
                    </div>

                    <!-- Returned (History) Table -->
                    <div id="table2-container" class="hidden">
                        <div id="table2" class="overflow-x-auto">
                            <div class="scrollable-table-container  pt-2 pr-6 pb-2 pl-4 sm:rounded-lg border border-gray-300">

                                <div class="scrollable-table-container ">
                                    <table id="borrowed-table2" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300">
                                        <thead class="text-xs text-gray-700 uppercase bg-blue-400 ">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Full Name</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">User Category</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/3">Course</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/12">Quantity and Accession No.</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/5">Date To Claim</th>
                                                <th scope="col" class="px-6 py-3 border border-gray-300 w-1/6">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="borrowed-table-body">
                                            <?php

                                            include '../connection.php';  // Database connection for main DB
                                            include '../connection2.php';  // Additional connection if needed

                                            // Get today's date
                                            $today = date('Y-m-d');

                                            // Query to fetch accession_no of records that need to be updated
                                            $accessionQuery = "SELECT accession_no
FROM borrow
WHERE Date_To_Claim < DATE_SUB('$today', INTERVAL 3 DAY) AND status = 'pending'";
                                            $accessionResult = $conn->query($accessionQuery);

                                            // Update the `borrow` table status to 'failed-to-claim'
                                            $updateSql = "UPDATE borrow
SET status = 'failed-to-claim'
WHERE Date_To_Claim < DATE_SUB('$today', INTERVAL 3 DAY) AND status = 'pending'";
                                            $conn->query($updateSql);

                                            // Update the `accession_records` table for affected rows
                                            if ($accessionResult->num_rows > 0) {
                                                while ($row = $accessionResult->fetch_assoc()) {
                                                    $accessionNo = $row['accession_no'];
                                                    $updateAccessionSql = "UPDATE accession_records
        SET available = 'yes', borrower_id = NULL
        WHERE accession_no = ?";
                                                    $stmt = $conn->prepare($updateAccessionSql);
                                                    $stmt->bind_param('s', $accessionNo);
                                                    $stmt->execute();
                                                    $stmt->close();
                                                }
                                            }

                                            // Query to get student records
                                            $studentSql = "SELECT
    s.First_Name,
    s.Middle_Initial,
    s.Last_Name,
    c.course,
    b.student_id,
    b.role,

    MIN(b.Time) AS Time,
    COUNT(b.student_id) AS borrow_count,
    MIN(b.Date_To_Claim) AS nearest_date
FROM borrow b
JOIN students s ON b.student_id = s.Student_Id
JOIN course c ON s.course_id = c.course_id
WHERE b.status = 'ready_to_claim'
GROUP BY b.student_id, s.First_Name, s.Middle_Initial, s.Last_Name, c.course, b.role";
                                            $studentResult = $conn->query($studentSql);

                                            // Query to get faculty records
                                            $facultySql = "SELECT
    f.First_Name,
    f.Middle_Initial,
    f.Last_Name,
    b.faculty_id,
    b.role,

    MIN(b.Time) AS Time,
    COUNT(b.faculty_id) AS borrow_count,
    MIN(b.Date_To_Claim) AS nearest_date
FROM borrow b
JOIN faculty f ON b.faculty_id = f.Faculty_Id
WHERE b.status = 'ready_to_claim'
GROUP BY b.faculty_id, f.First_Name, f.Middle_Initial, f.Last_Name, b.role";
                                            $facultyResult = $conn->query($facultySql);

                                            // Array to store combined records
                                            $records = [];
                                            if ($studentResult->num_rows > 0) {
                                                while ($row = $studentResult->fetch_assoc()) {
                                                    $row['user_type'] = 'student'; // Mark as student
                                                    $records[] = $row;
                                                }
                                            }
                                            if ($facultyResult->num_rows > 0) {
                                                while ($row = $facultyResult->fetch_assoc()) {
                                                    $row['user_type'] = 'faculty'; // Mark as faculty
                                                    $records[] = $row;
                                                }
                                            }


                                            ?>
                                            <?php if (!empty($records)): ?>
                                                <?php foreach ($records as $record): ?>
                                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">
                                                        <td class="px-6 py-4 border border-gray-300"> <?php echo htmlspecialchars($record['First_Name'] . ' ' . $record['Middle_Initial'] . ' ' . $record['Last_Name']); ?></td>
                                                        <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($record['role']); ?></td>
                                                        <td class="px-6 py-4 border border-gray-300"> <?php
                                                                                                        // Display S_Course only if it exists (for students)
                                                                                                        echo $record['user_type'] === 'student' ? htmlspecialchars($record['course']) : 'N/A';
                                                                                                        ?></td>
                                                        <td class="px-6 py-4 border border-gray-300">
                                                            <?php
                                                            // Display quantity (borrow count)
                                                            echo htmlspecialchars($record['borrow_count']);

                                                            // Fetch all the accession numbers for the current record
                                                            $accessionQuery = "SELECT accession_no FROM borrow WHERE student_id = ? AND status = 'ready_to_claim' ";
                                                            if ($record['user_type'] === 'faculty') {
                                                                $accessionQuery = "SELECT accession_no FROM borrow WHERE faculty_id = ? AND status = 'ready_to_claim' ";
                                                            }

                                                            $stmt = $conn->prepare($accessionQuery);
                                                            if ($record['user_type'] === 'student') {
                                                                $stmt->bind_param('i', $record['student_id']);
                                                            } else {
                                                                $stmt->bind_param('i', $record['faculty_id']);
                                                            }
                                                            $stmt->execute();
                                                            $accessionResult = $stmt->get_result();
                                                            $accessionNumbers = [];
                                                            while ($accessionRow = $accessionResult->fetch_assoc()) {
                                                                $accessionNumbers[] = $accessionRow['accession_no'];
                                                            }
                                                            $stmt->close();

                                                            // Display the accession numbers next to the quantity
                                                            if (!empty($accessionNumbers)) {
                                                                echo '<br>Accessions: ' . implode(', ', $accessionNumbers);
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="px-6 py-4 border border-gray-300"> <?php
                                                                                                        // Format the nearest date
                                                                                                        $nearestDate = new DateTime($record['nearest_date']);
                                                                                                        // Format the date as 'F j, Y' and get the day of the week
                                                                                                        $formattedDate = $nearestDate->format('F j, Y') . ' - ' . $nearestDate->format('l');

                                                                                                        // Output the formatted date along with the Time value if available
                                                                                                        echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($record['Time']);
                                                                                                        ?></td>
                                                        <td class="px-6 py-4 border border-gray-300">
                                                            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                                onclick="redirectToBookRequest2('<?php echo htmlspecialchars($record['user_type']); ?>', '<?php echo htmlspecialchars($record['user_type'] === 'student' ? $record['student_id'] : $record['faculty_id']); ?>')">
                                                                Next
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>


                                            <?php endif; ?>
                                        </tbody>
                                    </table>


                                    <!-- jQuery -->
                                    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                                    <!-- DataTables Core JS -->
                                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                                    <!-- DataTables TailwindCSS Integration -->
                                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>
                                    <!-- <script>
                                    $(document).ready(function() {
                                        $('#borrowed-table2').DataTable({
                                            responsive: true,
                                            paging: true,
                                            searching: true,
                                            info: true,
                                            order: [],
                                            columnDefs: [{
                                                orderable: false,
                                                targets: 5 // "Action" column
                                            }],
                                        });
                                    });
                                </script> -->
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


                                    <script>
                                        function redirectToBookRequest2(userType, userId) {
                                            // Redirect to book_request_2.php with appropriate ID parameter based on user type
                                            const param = userType === 'student' ? 'student_id' : 'faculty_id';
                                            window.location.href = 'book_request_3.php?' + param + '=' + userId;
                                        }
                                    </script>




                                </div>
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
                // Hide the Pending table (table1)
                document.getElementById('table1').classList.add('hidden');
                // Show the History table (table2-container)
                document.getElementById('table2-container').classList.remove('hidden');
                // Show the dropdown menu
                document.getElementById('dropdownContainer').classList.remove('hidden');
            }
        });

        // Event listener for the second radio button
        document.getElementById('inline-checked-radio').addEventListener('change', function() {
            if (this.checked) {
                // Show the Pending table (table1)
                document.getElementById('table1').classList.remove('hidden');
                // Hide the History table (table2-container)
                document.getElementById('table2-container').classList.add('hidden');
                // Hide the dropdown menu
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