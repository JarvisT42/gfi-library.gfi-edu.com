<?php
# Initialize the session
require '../connection.php';

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
        /* If you prefer inline styles, you can include them directly */
        .active-statement {
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




                <div class="overflow-x-auto max-h-screen">
                    <!-- Category Dropdown for Filtering -->
                    <div class="mb-4 flex justify-between items-center">

                        <div class="mb-4">
                            <label for="categoryFilter" class="block text-gray-700">Filter by Table:</label>
                            <select id="categoryFilter" class="p-2 border border-gray-300 rounded-md">
                                <option value="All Fields" selected>All Fields</option> <!-- Set the default value to 'All Fields' -->

                                <?php
                                // Database connection
                                require '../connection2.php';

                                if ($conn2->connect_error) {
                                    die("Connection failed: " . $conn2->connect_error);
                                }

                                // Query to fetch all table names from the database
                                $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory"; // Replace with your actual database name

                                $result = $conn2->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_array()) {
                                        $tableName = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');

                                        // Exclude "e-books" table
                                        if ($tableName !== 'e-books') {
                                            echo "<option value='$tableName'>$tableName</option>";
                                        }
                                    }
                                } else {
                                    // Handle no tables available
                                    echo "<option value=''>No tables available</option>";
                                }
                                ?>
                            </select>

                        </div>
                        <!-- Print Button -->
                        <div class="mb-4">
                            <button id="printButton" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-700">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>

                    </div>
                    <!-- Table to display fetched data -->
                    <table id="reportTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Call Number</th>

                                <th>Title</th>
                                <th>Author</th>
                                <th>Publisher</th>
                                <th>Copies</th>

                            </tr>
                        </thead>
                        <tbody id="tableData">
                            <!-- Data will be populated here via JavaScript -->
                        </tbody>
                    </table>

                    <!-- jQuery -->



                </div>


                <script src="./src/components/header.js"></script>


                <!-- jQuery -->
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                <!-- DataTables Core JS -->
                <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                <!-- DataTables TailwindCSS Integration -->
                <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>
                <script>
                    $(document).ready(function() {
                        // Initialize the DataTable
                        function initializeDataTable() {
                            $('#reportTable').DataTable({
                                paging: true, // Enables pagination
                                searching: true, // Enables search functionality
                                info: true, // Displays table info
                                order: [], // Default no ordering
                                language: {
                                    search: "Search reports:",
                                    zeroRecords: "No matching reports found",
                                },
                            });
                        }


                        // Triggered when the dropdown value changes
                        $('#categoryFilter').change(function() {
                            var selectedTable = $(this).val(); // Get selected table name

                            // Destroy existing DataTable
                            if ($.fn.dataTable.isDataTable('#reportTable')) {
                                $('#reportTable').DataTable().destroy();
                            }

                            // Check if "All Tables" is selected
                            if (selectedTable === "") {
                                // Fetch all data from all tables
                                $.ajax({
                                    url: 'statement_fetch.php', // PHP script to fetch all data from all tables
                                    type: 'POST',
                                    data: {
                                        table: 'All Fields' // Special flag for fetching all tables
                                    },
                                    success: function(response) {
                                        var data = JSON.parse(response);
                                        var tableContent = '';

                                        if (data.length > 0) {
                                            $.each(data, function(index, row) {
                                                tableContent += `<tr>
                                <td class="px-6 py-4">${index + 1}</td>
                                 <td class="px-6 py-4">${row.call_number}</td>

                                <td class="px-6 py-4">${row.title}</td>
                                <td class="px-6 py-4">${row.author}</td>
                                                           <td class="px-6 py-4">${row.publisher}</td>
                 <td class="px-6 py-4">${row.no_of_copies}</td>

                            </tr>`;
                                            });
                                        } else {
                                            tableContent = '<tr><td colspan="3">No data found</td></tr>';
                                        }

                                        $('#tableData').html(tableContent); // Populate the table body

                                        // Reinitialize the DataTable after data is loaded
                                        initializeDataTable();
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('Error fetching data:', error);
                                    }
                                });
                            } else {
                                // Fetch data based on selected table
                                $.ajax({
                                    url: 'statement_fetch.php',
                                    type: 'POST',
                                    data: {
                                        table: selectedTable
                                    },
                                    success: function(response) {
                                        var data = JSON.parse(response);
                                        var tableContent = '';

                                        if (data.length > 0) {
                                            $.each(data, function(index, row) {
                                                tableContent += `<tr>
                                <td class="px-6 py-4">${index + 1}</td>
                                                                 <td class="px-6 py-4">${row.call_number}</td>

                                <td class="px-6 py-4">${row.title}</td>
                                <td class="px-6 py-4">${row.author}</td>
                                                                <td class="px-6 py-4">${row.publisher}</td>
                 <td class="px-6 py-4">${row.no_of_copies}</td>

                            </tr>`;
                                            });
                                        } else {
                                            tableContent = '<tr><td colspan="3">No data found</td></tr>';
                                        }

                                        $('#tableData').html(tableContent); // Populate the table body

                                        // Reinitialize the DataTable after data is loaded
                                        initializeDataTable();
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('Error fetching data:', error);
                                    }
                                });
                            }
                        });

                        // Trigger the change event on page load to load data for "All Tables"
                        $('#categoryFilter').trigger('change');

                        // Print Button Click
                        $('#printButton').click(function() {
                            var selectedTable = $('#categoryFilter').val(); // Get selected table name

                            // Check if "All Fields" is selected
                            if (selectedTable) {
                                // If a specific table is selected, proceed with the print request
                                $.ajax({
                                    url: 'statement_print.php', // PHP script to handle printing
                                    type: 'POST',
                                    data: {
                                        table: selectedTable // Send the selected table name
                                    },
                                    success: function(response) {
                                        // Open the print page in a new tab
                                        var printUrl = 'statement_print.php?table=' + selectedTable; // Construct the URL with selected table
                                        window.open(printUrl, '_blank'); // Open in a new tab
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('Error sending print request:', error);
                                    }
                                });
                            } else {
                                // If no table is selected, alert the user
                                alert("Please select a table before printing.");
                            }
                        });



                    });
                </script>





            </div>
        </div>

    </main>





</body>

</html>