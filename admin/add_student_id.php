<?php
include '../connection.php'; // Include your database connection file

session_start();
if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}

// Check if the request is a POST request and contains the 'add' parameter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    // Prepare an array to store valid student IDs
    $studentIds = [];

    // Loop through all 10 potential fields
    for ($i = 1; $i <= 10; $i++) {
        $key = "student-id-{$i}";
        if (!empty($_POST[$key])) {
            $studentIds[] = $_POST[$key];
        }
    }

    // Check if there are valid student IDs to insert
    if (count($studentIds) > 0) {
        // Prepare the SQL query dynamically
        $placeholders = array_fill(0, count($studentIds), '(?)');
        $sql = "INSERT INTO students_ids (student_id) VALUES " . implode(', ', $placeholders);

        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters dynamically
            $types = str_repeat('s', count($studentIds)); // 's' for each student_id (string)
            $stmt->bind_param($types, ...$studentIds);

            // Execute the query
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?added_success=1");

                $_SESSION['success_message'] = 'Student IDs added successfully!';
            } else {
                $_SESSION['error_message'] = 'Database insertion failed: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Failed to prepare SQL statement: ' . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = 'No valid student IDs provided.';
    }

    $conn->close();

    // Redirect back to the same page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>

    <style>
        /* If you prefer inline styles, you can include them directly */
        .active-student_id {
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
                    <h1 class="text-3xl font-semibold">Students Id</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>


                <?php if (isset($_GET['added_success']) && $_GET['added_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Added successful!
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['delete_success']) && $_GET['delete_success'] == 1): ?>
                    <div id="alert" class="alert alert-danger" role="alert" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Deleted successfully!
                    </div>
                <?php endif; ?>





                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    The Students page allows administrators to add student IDs. Administrators can easily input and assign student IDs to ensure all students are properly documented for efficient tracking and management.
                </div>



                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <ul class="flex flex-wrap gap-2 p-5 border border-dashed rounded-md w-full">


                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="#">Student Id's</a></li>
                        <br>
                        <li><a class="px-4 py-2 " href="add_faculty_id.php">Faculty Id's</a></li>


                        <!-- <li><a class="px-4 py-2 " href="subject_for_replacement.php">Subject for Replacement</a></li> -->
                    </ul> <!-- Button beside the title -->


                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">








                    <div class="flex items-start justify-center rounded   dark:bg-gray-800">



                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg shadow-md ">

                            <div class="p-4 bg-gray-50 rounded-lg">


                                <form method="POST" id="createAccountForm" enctype="multipart/form-data">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <div class="space-y-1">
                                            <label for="student-id-<?= $i ?>" class="font-medium text-sm text-gray-700">Student ID <?= $i ?></label>
                                            <div class="flex items-center border border-gray-300 rounded-md">
                                                <span class="px-2 text-gray-500 text-xs">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                                <input type="number" id="student-id-<?= $i ?>" name="student-id-<?= $i ?>"
                                                    class="w-full px-2 py-1 text-sm focus:outline-none focus:ring focus:border-blue-400 rounded-r-md"
                                                    min="0">
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                    <input type="hidden" name="add" value="1">
                                    <button type="submit"
                                        class="w-full px-3 py-2 text-xs text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:bg-blue-800">
                                        <i class="fas fa-user-plus"></i> Add Student IDs
                                    </button>
                                </form>









                            </div>

                        </div>









                    </div>




                    <div class="flex items-center justify-center rounded bg-gray-50 dark:bg-gray-800">
                        <?php
                        // Include database connection
                        include '../connection.php';

                        // Fetch users from the database, ordered by `created_at` in descending order
                        $sql = "SELECT student_id, status, created_at FROM students_ids ORDER BY created_at DESC";
                        $result = $conn->query($sql);

                        // Initialize an empty array to hold users
                        $users = [];

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $users[] = $row;
                            }
                        }

                        // Close the database connection
                        $conn->close();
                        ?>




                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg h-full shadow-md">
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table id="userTable" class="w-full border-collapse stripe hover">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-left p-2">No.</th>
                                                <th class="text-left p-2">Student Id</th>
                                                <th class="text-left p-2">Status</th>
                                                <th class="text-left p-2">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="userTableBody">
                                            <?php
                                            if (!empty($users)) {
                                                $no = 1; // Add a counter for the "No." column
                                                foreach ($users as $user) {
                                            ?>
                                                    <tr class="border-b" data-user-id="<?php echo htmlspecialchars($user['student_id']); ?>" data-status="<?php echo htmlspecialchars($user['status']); ?>">
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo $no++; ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($user['student_id']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($user['status']); ?>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <button class="text-red-600 hover:text-red-800 focus:outline-none" onclick="deleteUser(<?php echo htmlspecialchars($user['student_id']); ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="4" class="text-center p-2">No users found</td>
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
    $('#userTable').DataTable({
        paging: true, // Enables pagination
        searching: true, // Enables search functionality
        info: true, // Displays table info
        order: [], // Default no ordering
        columnDefs: [{
            orderable: false,
            targets: 3 // Make the "Action" column not sortable
        }]
    });
});

// Function to delete a user by ID
function deleteUser(userId) {
    // Find the row that contains the user ID
    var row = document.querySelector(`tr[data-user-id="${userId}"]`);
    
    // Get the status from the data-status attribute of the row
    var status = row.getAttribute('data-status');
    
    // Check if the status is 'taken' and prevent deletion
    if (status === 'Taken') {
        alert('This user cannot be deleted because their status is "taken".');
        return; // Exit the function if the status is "taken"
    }

    // Confirm before deletion
    if (confirm('Are you sure you want to delete this user?')) {
        // Send a request to delete the user from the backend
        fetch('add_student_id_delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: userId, // Send the student ID
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // If the deletion was successful, remove the row from the table
                row.remove();
                // Optionally show a success message
                window.location.href = window.location.pathname + '?delete_success=1';
                // Optionally redirect or update UI
                // window.location.href = window.location.pathname + '?delete_success=1';
            } else {
                alert('Failed to delete user: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
        });
    }
}

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