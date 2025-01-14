<?php
include '../connection.php'; // Include your database connection file

session_start();
if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fine-amount'])) {
    // Get the fine amount from the form
    $fineAmount = $_POST['fine-amount'];

    // Validate the input
    if (!empty($fineAmount) && is_numeric($fineAmount) && $fineAmount >= 0) {
        // Insert the fine amount into the database
        $sql = "INSERT INTO library_fines (amount, date) VALUES (?, NOW())"; // Automatically set the current date
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("d", $fineAmount); // Bind the fine amount as a double (float)
            
            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Fine added successfully!';
                header("Location: " . $_SERVER['PHP_SELF'] . "?added_success=1");
                exit;
            } else {
                $_SESSION['error_message'] = 'Failed to add the fine: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Failed to prepare SQL statement: ' . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = 'Invalid fine amount provided.';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>

    <style>
        /* If you prefer inline styles, you can include them directly */
        .active-edit-fines {
            background-color: #f0f0f0;
            color: #000;
        }

        .active-setting {
            background-color: #f0f0f0;
            color: #000;
        }
    </style>

    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 100%;
            width: 100%;

        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        canvas {
            margin-top: 20px;
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Edit Fines</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>


                <?php if (isset($_GET['added_success']) && $_GET['added_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Added successful!
                    </div>
                <?php endif; ?>






                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    edit </div>




                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">








                    <div class="flex items-start justify-center rounded   dark:bg-gray-800">



                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg shadow-md">

                            <div class="px-6 py-4 bg-blue-600 rounded-t-lg text-white">
                                <h2 class="text-lg font-semibold">Edit Fine</h2>
                                <p class="text-sm opacity-90">Update the fine amount for this record.</p>
                            </div>

                            <!-- Form Section -->
                            <div class="p-6">
                                <form method="POST" id="editFinesForm" class="space-y-6" >

                                    <!-- Fine Amount Input -->
                                    <div class="space-y-2">
                                        <label for="fine-amount" class="block text-sm font-medium text-gray-700">Fine Amount</label>
                                        <input type="number" id="fine-amount" name="fine-amount"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring focus:border-blue-400"
                                            placeholder="Enter Fine Amount" min="0" required>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end space-x-4">

                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:bg-blue-800">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>










                    </div>




                    <div class="flex items-center justify-center rounded bg-gray-50 dark:bg-gray-800">
                        <?php
                        // Include database connection
                        include '../connection.php';

                        // Fetch fines from the library_fines table, ordered by `date` in descending order
                        $sql = "SELECT fines_id, date, amount FROM library_fines ORDER BY date DESC";
                        $result = $conn->query($sql);

                        // Initialize an empty array to hold fines
                        $fines = [];

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $fines[] = $row;
                            }
                        }

                        // Close the database connection
                        $conn->close();
                        ?>

                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg h-full shadow-md">
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table id="finesTable" class="w-full border-collapse stripe hover">
                                        <thead >
                                            <tr class="border-b bg-blue-400">
                                                <th class="text-left p-2">No.</th>
                                                <th class="text-left p-2">Date</th>
                                                <th class="text-left p-2">Fine Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="finesTableBody">
                                            <?php
                                            if (!empty($fines)) {
                                                $no = 1; // Counter for the "No." column
                                                foreach ($fines as $fine) {
                                            ?>
                                                    <tr class="border-b" data-fine-id="<?php echo htmlspecialchars($fine['fines_id']); ?>">
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo $no++; ?>
                                                        </td>

                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($fine['date']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($fine['amount']); ?>
                                                        </td>

                                                    </tr>

                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="5" class="text-center p-2">No fines found</td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>



                        <!-- jQuery -->
                        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                        <!-- DataTables Core JS -->
                        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                        <!-- DataTables TailwindCSS Integration -->
                        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

                        <script>
                            $(document).ready(function() {
                                // Initialize DataTables
                                $('#finesTable').DataTable({
                                    paging: true, // Enables pagination
                                    searching: true, // Enables search functionality
                                    info: true, // Displays table info
                                    order: [], // Default no initial ordering
                                    responsive: true // Ensures the table is responsive
                                });

                            });

                        </script>




                    </div>
















                </div>













            </div>
        </div>

    </main>

    <script>
        // Set a timeout to hide the alert after 3 seconds (3000 ms)s
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
            const dropdownRequest = document.getElementById('dropdown-setting');

            // Open the dropdown menu for 'Request'
            dropdownRequest.classList.remove('hidden');
            dropdownRequest.classList.add('block'); // Make the dropdown visible

        });
    </script>
    <!-- jQuery -->


</body>

</html>