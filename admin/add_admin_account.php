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

</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Create Admin Account</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>

                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    The Create Account page allows administrators to set up new accounts with the required privileges. This page makes it easy to add new admins, ensuring that all necessary information—such as full name, email, and password—is gathered securely. Once an admin account is created, they will have access to the system's management features, providing them with the tools they need to maintain and organize the library efficiently.
                </div>


                <div class="grid grid-cols-2 gap-4 mb-4">


                    <div class="flex items-start justify-center rounded   dark:bg-gray-800">



                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg shadow-md ">

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <form id="createAccountForm" class="space-y-4">
                                    <!-- Full Name Field -->
                                    <div class="space-y-2">
                                        <label for="fullname" class="font-medium text-sm text-gray-700">Full Name</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <span class="px-2 text-gray-500 text-xs">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" id="fullname" name="fullname" required
                                                class="w-full px-2 py-1 text-xs focus:outline-none focus:ring focus:border-blue-400 rounded-r-md">
                                        </div>
                                    </div>

                                    <!-- Username Field -->
                                    <div class="space-y-2">
                                        <label for="username" class="font-medium text-sm text-gray-700">Username</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <span class="px-2 text-gray-500 text-xs">
                                                <i class="fas fa-user-circle"></i>
                                            </span>
                                            <input type="text" id="username" name="username" required
                                                class="w-full px-2 py-1 text-xs focus:outline-none focus:ring focus:border-blue-400 rounded-r-md">
                                        </div>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="space-y-2">
                                        <label for="email" class="font-medium text-sm text-gray-700">Email</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <span class="px-2 text-gray-500 text-xs">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" id="email" name="email" required
                                                class="w-full px-2 py-1 text-xs focus:outline-none focus:ring focus:border-blue-400 rounded-r-md">
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="space-y-2">
                                        <label for="password" class="font-medium text-sm text-gray-700">Password</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <span class="px-2 text-gray-500 text-xs">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" id="password" name="password" required
                                                class="w-full px-2 py-1 text-xs focus:outline-none focus:ring focus:border-blue-400 rounded-r-md">
                                        </div>
                                    </div>

                                    <!-- Confirm Password Field -->
                                    <div class="space-y-2">
                                        <label for="confirm-password" class="font-medium text-sm text-gray-700">Confirm Password</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <span class="px-2 text-gray-500 text-xs">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" id="confirm-password" name="confirm-password" required
                                                class="w-full px-2 py-1 text-xs focus:outline-none focus:ring focus:border-blue-400 rounded-r-md">
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full px-3 py-1 text-xs text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:bg-blue-800">
                                        <i class="fas fa-user-plus"></i> Create Account
                                    </button>
                                </form>


                                <script>
                                    document.getElementById('createAccountForm').addEventListener('submit', function(e) {
                                        e.preventDefault(); // Prevent the form from submitting traditionally

                                        const password = document.getElementById('password').value;
                                        const confirmPassword = document.getElementById('confirm-password').value;

                                        // Check if passwords match
                                        if (password !== confirmPassword) {
                                            alert("Passwords do not match!");
                                            return; // Stop the form submission if passwords don't match
                                        }

                                        const data = {
                                            fullname: document.getElementById('fullname').value,
                                            username: document.getElementById('username').value,
                                            email: document.getElementById('email').value,
                                            password: password, // Send the password (already validated)
                                        };

                                        // Send data to the PHP backend using fetch
                                        fetch('add_admin_create_account.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify(data),
                                            })
                                            .then(response => response.json())
                                            .then(result => {
                                                if (result.success) {
                                                    alert('Account created successfully!');
                                                    location.reload(); // Reload the page or redirect
                                                } else {
                                                    alert('Error: ' + result.message);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                alert('An error occurred. Please try again.');
                                            });
                                    });
                                </script>



                            </div>

                        </div>



                    </div>


                    <?php
                    // Include your database connection file
                    include '../connection.php';

                    // Query to get all users
                    $query = "SELECT id, Full_Name, email FROM admin_account where Default_Account = ''";
                    $result = $conn->query($query);

                    // Initialize an empty array to hold the user data
                    $users = [];

                    if ($result->num_rows > 0) {
                        // Fetch each row from the result set
                        while ($row = $result->fetch_assoc()) {
                            // Add each user to the array
                            $users[] = $row;
                        }
                    }
                    ?>

                    <div class="flex items-center justify-center rounded bg-gray-50 dark:bg-gray-800">
                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg h-full shadow-md">
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table id="userTable" class="w-full border-collapse">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-left p-2">ID</th>
                                                <th class="text-left p-2">Full Name</th>
                                                <th class="text-left p-2">Email</th>
                                                <th class="text-left p-2">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody id="userTableBody">
                                            <?php
                                            if (!empty($users)) {
                                                // Loop through each user and display their data in the table
                                                foreach ($users as $user) {
                                            ?>
                                                    <tr class="border-b" data-user-id="<?php echo htmlspecialchars($user['id']); ?>">
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($user['id']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($user['Full_Name']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($user['email']); ?>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <button class="text-red-600 hover:text-red-800 focus:outline-none" onclick="deleteUser(<?php echo $user['id']; ?>)">
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
                    </div>


                    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                    <!-- DataTables Core JS -->
                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                    <!-- DataTables TailwindCSS Integration -->
                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#userTable').DataTable({
                                paging: true, // Enables pagination
                                searching: true, // Enables search functionality
                                info: true, // Displays table info
                                order: [], // Default no ordering
                                columnDefs: [{
                                    orderable: false,
                                    targets: 3 // Make the "Delete" column not sortable
                                }]
                            });
                        });


                        // JavaScript function to handle user deletion
                        function deleteUser(userId) {
                            if (confirm("Are you sure you want to delete this user?")) {
                                // Send the deletion request to the server using fetch
                                fetch('add_delete_addmin_account.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: new URLSearchParams({
                                            id: userId
                                        })
                                    })
                                    .then(response => response.json()) // Parse the JSON response
                                    .then(data => {
                                        if (data.success) {
                                            alert(data.message);
                                            location.reload(); // Reload the page to refresh the table
                                        } else {
                                            alert('Error: ' + data.message);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while trying to delete the user.');
                                    });
                            }
                        }
                    </script>















                </div>

            </div>
        </div>

    </main>



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