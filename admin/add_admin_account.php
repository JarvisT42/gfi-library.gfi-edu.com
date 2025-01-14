<?php
include '../connection.php'; // Include your database connection file

session_start();
if (!isset($_SESSION['logged_Admin']) || $_SESSION['logged_Admin'] !== true) {
    header('Location: ../index.php');

    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Sanitize and validate the input
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $roleId = 3;

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
        exit();
    }

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query
    $sql = "INSERT INTO admin_account (Full_Name, Email, Password, role_id) VALUES (?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (s = string)
        $stmt->bind_param("sssi", $fullname, $email, $hashedPassword, $roleId);

        // Execute statement
        if ($stmt->execute()) {
            echo "Record added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close connection
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'admin_header.php'; ?>

    <style>
        /* If you prefer inline styles, you can include them directly */
        .active-add-account {
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
                    <h1 class="text-3xl font-semibold">Add Admin Account </h1> <!-- Adjusted text size -->
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





                <div class="grid grid-cols-1 gap-4 mb-4">






                    <div class="flex items-center justify-center rounded bg-gray-50 dark:bg-gray-800">
                        <?php
                        // Include database connection
                        include '../connection.php';

                        // Fetch users from the database, ordered by `created_at` in descending order
                        $sql = "SELECT admin_id, Full_Name FROM admin_account ";
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
                                <!-- Add a button at the top -->
                                <button id="addUserButton" class="bg-blue-500 text-white px-4 py-2 rounded-md mb-4 hover:bg-blue-600 focus:outline-none" onclick="openModal()">Create Account</button>

                                <div class="overflow-x-auto">
                                    <table id="userTable" class="w-full border-collapse stripe hover border border-red-300">
                                        <thead>
                                            <tr class="border-b bg-blue-400 ">
                                                <th class="text-left p-2 border border-gray-300">No.</th>
                                                <th class="text-left p-2 border border-gray-300">Full Name</th>
                                                <th class="text-left p-2 border border-gray-300">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="userTableBody">
                                            <?php
                                            if (!empty($users)) {
                                                $no = 1; // Add a counter for the "No." column
                                                foreach ($users as $user) {
                                            ?>
                                                   

                                                    <tr  class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300" data-user-id="<?php echo htmlspecialchars($user['admin_id']); ?>">
                                                        <td class="px-6 py-4 break-words border border-gray-300" style="max-width: 300px;">
                                                            <?php echo $no++; ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words border border-gray-300" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($user['Full_Name']); ?>
                                                        </td>

                                                        <td class="px-6 py-4 border border-gray-300">
                                                            <button class="text-red-600 hover:text-red-800 focus:outline-none border-gray-300" onclick="deleteUser(<?php echo htmlspecialchars($user['admin_id']); ?>)">
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

                        <div id="modalOverlay" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" onclick="closeOnOutsideClick(event)">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4 md:mx-0">
                                <!-- Header Section -->
                                <div class="bg-red-800 text-white rounded-t-lg text-center">
                                    <h2 class="text-lg font-semibold p-4">Please Enter Details Below</h2>
                                </div>

                                <!-- Content Section -->
                                <div class="p-6 text-gray-800">



                                    <form id="studentForm" class="space-y-4" method="POST" action="">
                                        <div class="grid grid-cols-3 items-center gap-4">
                                            <label for="fullname" class="text-left">Full Name:</label>
                                            <input id="fullname" name="fullname" type="text" class="col-span-2 border rounded px-3 py-2" required />
                                        </div>

                                        <div class="grid grid-cols-3 items-center gap-4">
                                            <label for="email" class="text-left">Email:</label>
                                            <input id="email" name="email" type="email" class="col-span-2 border rounded px-3 py-2" required />
                                        </div>

                                        <div class="grid grid-cols-3 items-center gap-4">
                                            <label for="password" class="text-left">Password:</label>
                                            <input id="password" name="password" type="password" class="col-span-2 border rounded px-3 py-2" required />
                                        </div>

                                        <div class="grid grid-cols-3 items-center gap-4">
                                            <label for="confirm_password" class="text-left">Confirm Password:</label>
                                            <input id="confirm_password" name="confirm_password" type="password" class="col-span-2 border rounded px-3 py-2" required />
                                        </div>

                                        <!-- Hidden field for student ID -->
                                        <input type="hidden" id="student_id" name="student_id" />

                                        <div class="flex justify-end space-x-4">
                                            <button type="button" onclick="closeModal()" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700">
                                                Close
                                            </button>
                                            <button type="submit" name="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700" id="saveButton">
                                                Save
                                            </button>
                                        </div>
                                    </form>

                                    <script>
                                        // Open Modal
                                        function openModal() {
                                            document.getElementById('modalOverlay').classList.remove('hidden');
                                        }

                                        // Close Modal
                                        function closeModal() {
                                            document.getElementById('modalOverlay').classList.add('hidden');
                                        }

                                        // Close modal if clicked outside the modal content
                                        function closeOnOutsideClick(event) {
                                            if (event.target === document.getElementById('modalOverlay')) {
                                                closeModal();
                                            }
                                        }

                                        // Password validation
                                        document.getElementById('studentForm').addEventListener('submit', function(event) {
                                            const password = document.getElementById('password').value;
                                            const confirmPassword = document.getElementById('confirm_password').value;

                                            if (password !== confirmPassword) {
                                                event.preventDefault(); // Prevent form submission
                                                alert("Passwords do not match!");
                                            }
                                        });
                                    </script>




                                </div>

                                <!-- Footer Section -->
                            </div>
                        </div>


                        <!-- jQuery -->
                        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                        <!-- DataTables Core JS -->
                        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                        <!-- DataTables TailwindCSS Integration -->
                        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

                        <script>

                        </script>


                        <script>
                            $(document).ready(function() {
                                $('#userTable').DataTable({
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
                                        targets: 2 // Target the 6th column (zero-based index)
                                    }]
                                });
                            });




                            // Function to delete a user by ID
                            function deleteUser(userId) {
                                // Find the row that contains the user ID
                                var row = document.querySelector(`tr[data-user-id="${userId}"]`);

                                // Get the status from the data-status attribute of the row
                                var status = row.getAttribute('data-status');

                                // Check if the status is 'Taken' and prevent deletion
                                if (status === 'Taken') {
                                    alert('This user cannot be deleted because their status is "taken".');
                                    return; // Exit the function if the status is "taken"
                                }

                                // Confirm before deletion
                                if (confirm('Are you sure you want to delete this user?')) {
                                    // Prepare the data to be sent to the backend
                                    const dataToSend = {
                                        id: userId, // Send the user ID
                                    };

                                    // Log the data that will be sent
                                    console.log('Data to be sent to the backend:', dataToSend);

                                    // Optionally, alert the data for immediate visibility
                                    alert('Data being sent: ' + JSON.stringify(dataToSend));

                                    // Send a request to delete the user from the backend
                                    fetch('add_admin_delete.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify(dataToSend),
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