<?php
# Initialize the session
require '../connection.php';

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
        /* If you prefer inline styles, you can include them directly */
        .active-report_statement {
            background-color: #f0f0f0;
            /* Example for light mode */
            color: #000;
            /* Example for light mode */
        }
    </style>

</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">



                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Lost Book Report</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>

                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    The Students page displays all currently registered students. This page provides administrators with an easy-to-use interface to view and manage student information, such as full name, email, and student ID. It ensures that all registered students are properly documented and accessible for efficient tracking and management.
                </div>

                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300 mb-5">
                    <?php
                    // Set default date range to the current month
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // First day of current month
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'); // Last day of current month
                    ?>
                    <form method="GET" action="" class="flex flex-wrap items-center space-x-4">
                        <!-- Start Date -->
                        <div class="flex items-center space-x-2">
                            <label for="start_date" class="font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value="<?php echo htmlspecialchars($start_date); ?>"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                        </div>

                        <!-- End Date -->
                        <div class="flex items-center space-x-2">
                            <label for="end_date" class="font-medium text-gray-700 dark:text-gray-300">End Date</label>
                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                value="<?php echo htmlspecialchars($end_date); ?>"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                        </div>

                        <!-- Filter Button -->
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-600">
                            Filter
                        </button>
                    </form>
                </div>

                <?php
                // Include database connection file
                include '../connection.php';

                // Set default date range to the current month


                // Query to get report data with date filtering
                $query = "
                        SELECT 
                            r.borrower_id, 
                            r.role, 
                            r.date, 
                            r.accession_no, 
                            r.book_id, 
                            r.category, 
                            r.report_reason,
                            r.fines,
                            CASE 
                                WHEN r.role = 'student' THEN CONCAT(s.first_name, ' ', s.last_name)
                                WHEN r.role = 'faculty' THEN CONCAT(f.first_name, ' ', f.last_name)
                                WHEN r.role = 'walk_in' THEN w.full_name
                                ELSE w.full_name
                            END AS borrower_name
                        FROM report r
                        LEFT JOIN students s ON r.borrower_id = s.student_id
                        LEFT JOIN faculty f ON r.borrower_id = f.faculty_id
                        LEFT JOIN walk_in_borrowers w ON r.borrower_id = w.walk_in_id
                        WHERE r.date BETWEEN '$start_date' AND '$end_date';
                    ";

                $result = $conn->query($query);
                ?>


                <div class="overflow-x-auto max-h-screen">
                    <table id="reportTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300">
                        <thead class="bg-blue-400">
                            <tr>
                                <th class="border border-gray-300">Name</th>
                                <th class="border border-gray-300">Date</th>
                                <th class="border border-gray-300">Accession No</th>
                                <th class="border border-gray-300">Book ID</th>
                                <th class="border border-gray-300">Category</th>
                                <th class="border border-gray-300">Report Reason</th>
                                <th class="border border-gray-300">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['borrower_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['accession_no']); ?></td>
                                        <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td><?php echo htmlspecialchars($row['report_reason']); ?></td>
                                        <td>
                                        <button
    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 view-button"
    data-name="<?php echo htmlspecialchars($row['borrower_name']); ?>"
    data-date="<?php echo htmlspecialchars($row['date']); ?>"
    data-accession="<?php echo htmlspecialchars($row['accession_no']); ?>"
    data-bookid="<?php echo htmlspecialchars($row['book_id']); ?>"
    data-category="<?php echo htmlspecialchars($row['category']); ?>"
    data-reason="<?php echo htmlspecialchars($row['report_reason']); ?>"
    data-fines="<?php echo htmlspecialchars($row['fines']); ?>">

    View
</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <!-- <tr>
                    <td colspan="7" class="text-center">No reports found.</td>
                </tr> -->
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div id="modalOverlay" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" onclick="closeOnOutsideClickReturned(event)">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4 md:mx-0">
        <!-- Header Section -->
        <div class="bg-red-800 text-white rounded-t-lg text-center">
            <h2 class="text-lg font-semibold p-4">Report Details</h2>
        </div>

        <!-- Content Section -->
        <div class="p-6 text-gray-800">
            <form id="viewForm" class="space-y-4" method="POST">
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalName" class="text-left">Name:</label>
                    <input id="modalName" name="name" class="col-span-2 border rounded px-3 py-2" readonly />
                </div>
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalDate" class="text-left">Date:</label>
                    <input id="modalDate" name="date" class="col-span-2 border rounded px-3 py-2" readonly />
                </div>
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalAccession" class="text-left">Accession No:</label>
                    <input id="modalAccession" name="accession_no" class="col-span-2 border rounded px-3 py-2" readonly />
                </div>
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalBookID" class="text-left">Book ID:</label>
                    <input id="modalBookID" name="book_id" class="col-span-2 border rounded px-3 py-2" readonly />
                </div>
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalCategory" class="text-left">Category:</label>
                    <input id="modalCategory" name="category" class="col-span-2 border rounded px-3 py-2" readonly />
                </div>
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalReason" class="text-left">Report Reason:</label>
                    <textarea id="modalReason" name="report_reason" class="col-span-2 border rounded px-3 py-2" readonly></textarea>
                </div>
                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                    <label for="modalFines" class="text-left">Fines:</label>
                    <input id="modalFines" name="fines" class="col-span-2 border rounded px-3 py-2" readonly />
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700">
                        Close
                    </button>
                    <button type="button" onclick="openPrintPage()" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                        Print
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
              
<script>
    // Open a new tab with a printable table
    function openPrintPage() {
        const name = document.getElementById('modalName').value;
        const date = document.getElementById('modalDate').value;
        const accessionNo = document.getElementById('modalAccession').value;
        const bookID = document.getElementById('modalBookID').value;
        const category = document.getElementById('modalCategory').value;
        const reason = document.getElementById('modalReason').value;
        const fines = document.getElementById('modalFines').value;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print Report</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    table, th, td {
                        border: 1px solid black;
                    }
                    th, td {
                        text-align: left;
                        padding: 8px;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <h2>Report Details</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <td>${name}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>${date}</td>
                    </tr>
                    <tr>
                        <th>Accession No</th>
                        <td>${accessionNo}</td>
                    </tr>
                    <tr>
                        <th>Book ID</th>
                        <td>${bookID}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>${category}</td>
                    </tr>
                    <tr>
                        <th>Report Reason</th>
                        <td>${reason}</td>
                    </tr>
                    <tr>
                        <th>Fines</th>
                        <td>${fines}</td>
                    </tr>
                </table>
                <button onclick="window.print()">Print</button>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
<script>
    // Close modal when clicking outside the content area
    function closeOnOutsideClickReturned(event) {
        const modalContent = document.querySelector("#modalOverlay > div");
        if (!modalContent.contains(event.target)) {
            closeModal();
        }
    }

    // Open the modal and populate fields
    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modalName').value = this.dataset.name;
            document.getElementById('modalDate').value = this.dataset.date;
            document.getElementById('modalAccession').value = this.dataset.accession;
            document.getElementById('modalBookID').value = this.dataset.bookid;
            document.getElementById('modalCategory').value = this.dataset.category;
            document.getElementById('modalReason').value = this.dataset.reason;
            document.getElementById('modalFines').value = this.dataset.fines; // Correctly set fines data

            document.getElementById('modalOverlay').classList.remove('hidden');
        });
    });

    // Close the modal
    function closeModal() {
        document.getElementById('modalOverlay').classList.add('hidden');
    }
</script>
























            </div>
        </div>

    </main>



    <script src="./src/components/header.js"></script>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- DataTables Core JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    <!-- DataTables TailwindCSS Integration -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>
    <script>
        $(document).ready(function() {
            $('#reportTable').DataTable({
                paging: true, // Enables pagination
                searching: true, // Enables search functionality
                info: true, // Displays table info
                order: [], // Default no ordering
                columnDefs: [{
                    orderable: false, // Disable sorting for "Action" column
                    targets: 6, // The index of the "Action" column
                }, ],
                language: {
                    search: "Search reports:",
                    zeroRecords: "No matching reports found",
                },
            });
        });
    </script>

</body>

</html>