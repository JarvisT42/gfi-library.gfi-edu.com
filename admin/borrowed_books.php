<?php
# Initialize the session
session_start();
if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>
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
                        <div class="flex items-center space-x-4">




                            <div class="flex space-x-6">
                                <!-- First Radio Option -->
                                <!-- First Radio Option: Pending -->
                                <div class="flex items-center">
                                    <input checked id="inline-checked-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-checked-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        Pending
                                    </label>
                                </div>

                                <!-- Second Radio Option: History -->
                                <div class="flex items-center">
                                    <input id="inline-radio" type="radio" name="inline-radio-group" class="hidden peer">
                                    <label for="inline-radio" class="ms-2 cursor-pointer text-sm font-semibold px-6 py-3 rounded-lg bg-gray-100 text-gray-900 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 peer-checked:underline peer-checked:bg-blue-500 peer-checked:text-white shadow-md transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        History
                                    </label>
                                </div>

                            </div>







                            <!-- Dropdown and Button -->



                        </div>

                        <!-- Search Input -->

                    </div>





                    <div id="table1" class="overflow-x-auto">
                        <div class="scrollable-table-container relative overflow-x-auto shadow-md sm:rounded-lg pt-2 pr-6 pb-2 pl-4 border border-gray-300">
                            <table id="borrowed-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                                <thead class="text-xs text-gray-700 uppercase bg-blue-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Student Name</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">User Category</th>

                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Way of Borrow</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/3">Course</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/12">Number of Books Borrowed</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/5">Issued Date</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/6">Due Date</th>
                                        <th scope="col" class="px-6 py-3 border border-gray-300 w-1/6">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="borrowed-table-body">
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
                                    WHERE b.status = 'borrowed'
                                    GROUP BY b.student_id, b.faculty_id, b.Way_Of_Borrow, b.walk_in_id, b.role, s.course_id, c.course, b.Due_Date, b.Issued_Date";








                                    $borrowData = $conn->query($sql);
                                    ?>
                                    <?php if ($borrowData && $borrowData->num_rows > 0): ?>
                                        <?php while ($row = $borrowData->fetch_assoc()): ?>
                                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">


                                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white break-words student-name" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($row['First_Name']); ?> </td>
                                                <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($row['role']); ?></td>

                                                <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($row['Way_Of_Borrow']); ?></td>




                                                <td class="px-6 py-4 break-words border border-gray-300" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($row['course']); ?> </td>
                                                <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($row['borrow_count']); ?></td>
                                                <td class="px-6 py-4 border border-gray-300">
                                                    <?php
                                                    // Format the nearest date
                                                    $nearestDate = new DateTime($row['Issued_Date']);
                                                    // Format the date as 'F j, Y' and get the day of the week
                                                    $formattedDate = $nearestDate->format('F j, Y') . ' - ' . $nearestDate->format('l');

                                                    // Output the formatted date along with the Time value if available
                                                    echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($row['Time']);
                                                    ?>
                                                </td>


                                                <td class="px-6 py-4 border border-gray-300">
                                                    <?php
                                                    // Format the nearest date
                                                    $nearestDate = new DateTime($row['nearest_date']);
                                                    // Format the date as 'F j, Y' and get the day of the week
                                                    $formattedDate = $nearestDate->format('F j, Y') . ' - ' . $nearestDate->format('l');

                                                    // Output the formatted date along with the Time value if available
                                                    echo htmlspecialchars($formattedDate) . ' ' . htmlspecialchars($row['Time']);
                                                    ?>
                                                </td>




                                                <td class="px-6 py-4 border border-gray-300">
                                                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                                        onclick="redirectToBookRequest('<?php echo htmlspecialchars($row['role']); ?>', '<?php echo htmlspecialchars($row['Way_Of_Borrow']); ?>', '<?php echo htmlspecialchars($row['walk_in_id'] ?? ''); ?>', '<?php echo htmlspecialchars($row['student_id'] ?? ''); ?>', '<?php echo htmlspecialchars($row['faculty_id'] ?? ''); ?>')">
                                                        Next
                                                    </button>
                                                </td>




                                            </tr>



                                        <?php endwhile; ?>

                                    <?php endif; ?>
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
                                            targets: 7 // Target the 6th column (zero-based index)
                                        }]
                                    });
                                });
                            </script>

                            <script>
                                function redirectToBookRequest(role, wayOfBorrow, walkInId, studentId, facultyId) {
                                    let url = 'borrowed_books_2.php?';

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





                    <div id="table2-container" class="hidden">

                        <div id="table2" class="overflow-x-auto ">
                            <div class="scrollable-table-container relative overflow-x-auto shadow-md sm:rounded-lg pt-2 pr-6 pb-2 pl-4 border border-gray-300">

                                <table id="borrowed-table2" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600">

                                    <thead class="text-xs text-gray-700 uppercase bg-blue-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Student Name</th>
                                            <th scope="col" class="px-6 py-3 border border-gray-300 w-1/4">Way of Borrow</th>
                                            <th scope="col" class="px-6 py-3 border border-gray-300 w-1/12">Course</th>
                                            <th scope="col" class="px-6 py-3 border border-gray-300 w-1/5">Issued Date</th>
                                            <th scope="col" class="px-6 py-3 border border-gray-300 w-1/6">Return Date</th>
                                            <th scope="col" class="px-6 py-3 border border-gray-300 w-1/6">Handled By</th>

                                            <!-- <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/6">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="borrowed-table-body">
                                        <?php
                                        include '../connection.php';  // Ensure you have your database connection

                                        // Get today's date
                                        $today = date('Y-m-d');

                                        // Fetch book_id and category for entries that exceed 3 days
                                        $sqlReturned = "
                                        SELECT
                                            b.student_id,
                                            b.faculty_id,
                                            b.Way_Of_Borrow,
                                            b.walk_in_id,
                                            b.role,
                                            s.course_id,
                                            c.course,
                                            a.Full_Name,
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
                                            b.Return_Date
                                           
                                         
                                   
                                        FROM borrow b
                                        LEFT JOIN students s ON b.student_id = s.Student_Id
                                        LEFT JOIN faculty f ON b.faculty_id = f.Faculty_Id
                                        LEFT JOIN walk_in_borrowers w ON b.walk_in_id = w.walk_in_id
                                        LEFT JOIN course c ON s.course_id = c.course_id
                                        LEFT JOIN admin_account a ON b.admin_id = a.admin_id
                                        
                                        WHERE b.status = 'returned' or b.status = 'replaced'
                                        ORDER BY b.Return_Date DESC

                                        ";


                                        $returnedData = $conn->query($sqlReturned);
                                        ?>


                                        <?php if ($returnedData && $returnedData->num_rows > 0): ?>
                                            <?php while ($rowReturned = $returnedData->fetch_assoc()): ?>
                                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">
                                                    <td class="px-6 py-4 student-name border border-gray-300"><?php echo htmlspecialchars($rowReturned['First_Name']); ?></td>
                                                    <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($rowReturned['Way_Of_Borrow']); ?></td>
                                                    <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($rowReturned['Course']); ?></td>

                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <?php
                                                        // Convert Issued_Date tos a DateTime object
                                                        $issuedDate = new DateTime($rowReturned['Issued_Date']);
                                                        // Format the date as 'October 23, 2024 - Wednesday'
                                                        echo $issuedDate->format('F j, Y - l');
                                                        ?>
                                                    </td>
                                                    <td class="px-6 py-4 border border-gray-300">
                                                        <?php
                                                        // Convert Issued_Date to a DateTime object
                                                        $issuedDate = new DateTime($rowReturned['Return_Date']);
                                                        // Format the date as 'October 23, 2024 - Wednesday'
                                                        echo $issuedDate->format('F j, Y - l');
                                                        ?>
                                                    </td>
                                                    <td class="px-6 py-4 border border-gray-300"><?php echo htmlspecialchars($rowReturned['Full_Name']); ?></td>

                                                    <!-- <td class="px-6 py-4">
                                                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">View</button>
                                                    </td> -->
                                                </tr>
                                            <?php endwhile; ?>

                                        <?php endif; ?>
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