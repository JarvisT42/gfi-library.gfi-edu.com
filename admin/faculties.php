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
        .active-students {
            background-color: #f0f0f0;
            /* Example for light mode */
            color: #000;
            /* Example for light moasdde */
        }
    </style>

</head>

<body>
    <?php include './src/components/sidebar.php'; ?>

    <main id="content" class="">


        <div class="p-4 sm:ml-64">

            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">



                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <h1 class="text-3xl font-semibold">Students</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>

                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    The Faculties page displays all currently registered faculties. This page provides administrators with an easy-to-use interface to view and manage faculty information, such as full name, email, and faculty ID. It ensures that all registered faculties are properly documented and accessible for efficient tracking and management.
                </div>


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-4 mb-4 flex items-center justify-between">
                    <ul class="flex flex-wrap gap-2 p-5 border border-dashed rounded-md w-full">


                        <li><a class="px-4 py-2 " href="students.php">Students </a></li>
                        <br>
                        <li><a class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" href="faculties.php">Faculties </a></li>


                        <!-- <li><a class="px-4 py-2 " href="subject_for_replacement.php">Subject for Replacement</a></li> -->
                    </ul> <!-- Button beside the title -->


                </div>


                <?php
                // Include database connection file
                include '../connection.php';

                $query = "SELECT * FROM faculty";

                $result = $conn->query($query);
                ?>

                <div class="overflow-x-auto max-h-screen">
                    <table id="facultyTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border border-gray-300">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/6">Name</th>
                                <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/12">Gender</th>
                                <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/4">Email</th>
                                <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/6">Contact</th>
                                <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/12">Status</th>
                                <th scope="col" class="px-6 py-3 border-b border-gray-300 w-1/6">Action</th> <!-- Removed the "Course" column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-300">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white break-words" style="max-width: 300px;">
                                            <?php echo htmlspecialchars($row['First_Name']); ?>
                                        </td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['S_Gender']); ?></td>
                                        <td class="px-6 py-4 break-words" style="max-width: 300px;"><?php echo htmlspecialchars($row['Email_Address']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td class="px-6 py-4">
                                            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" onclick="openModal('<?php echo htmlspecialchars($row['Faculty_Id']); ?>', '<?php echo htmlspecialchars($row['First_Name']); ?>', '<?php echo htmlspecialchars($row['S_Gender']); ?>', '<?php echo htmlspecialchars($row['Email_Address']); ?>', '<?php echo htmlspecialchars($row['mobile_number']); ?>', '<?php echo htmlspecialchars($row['status']); ?>')">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">No Faculties found.</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- DataTables Core JS -->
                    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

                    <!-- DataTables Core JS -->
                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

                    <!-- DataTables TailwindCSS Integration -->
                    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.tailwindcss.js"></script>

                    <script>
                        $(document).ready(function() {
                            $('#facultyTable').DataTable({
                                paging: true, // Enables pagination
                                searching: true, // Enables search functionality
                                info: true, // Displays table info
                                order: [], // Default no ordering
                                columnDefs: [{
                                    orderable: false,
                                    targets: 5 // Make the "Action" column not sortable (index is 5, as "Course" column is removed)
                                }]
                            });
                        });
                    </script>



                </div>


                <!-- jQuery -->


                <!-- DataTables Core JS -->

                <!-- DataTables TailwindCSS Integration -->






                <!-- Modal with Dark Background Overlay -->

                <!-- Dark Background (Overlay) -->

                <div id="modalOverlay" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" onclick="closeOnOutsideClickReturned(event)">

                    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4 md:mx-0">
                        <!-- Header Section -->
                        <div class="bg-red-800 text-white rounded-t-lg text-center">
                            <h2 class="text-lg font-semibold p-4">Please Enter Details Below</h2>
                        </div>

                        <!-- Content Section -->
                        <div class="p-6 text-gray-800">
                            <form id="facultyForm" class="space-y-4" method="POST">
                                <div class="grid grid-cols-3 items-center gap-4 mt-3">
                                    <label for="name" class="text-left">Name:</label>
                                    <input id="name" name="name" class="col-span-2 border rounded px-3 py-2" readonly />
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="gender" class="text-left">Gender:</label>
                                    <input id="gender" name="gender" class="col-span-2 border rounded px-3 py-2" readonly />
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="email" class="text-left">Email:</label>
                                    <input id="email" name="email" class="col-span-2 border rounded px-3 py-2" readonly />
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="contact" class="text-left">Contact:</label>
                                    <input id="contact" name="contact" class="col-span-2 border rounded px-3 py-2" readonly />
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="year_level" class="text-left">Year Level:</label>
                                    <input id="year_level" name="year_level" class="col-span-2 border rounded px-3 py-2" readonly />
                                </div>

                                <div class="grid grid-cols-3 items-center gap-4">
                                    <label for="status" class="text-left">Status:</label>
                                    <select id="status" name="status" class="col-span-2 border rounded px-3 py-2">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="banned">Banned</option>
                                    </select>
                                </div>


                                <!-- Hidden field for student ID -->
                                <input type="hidden" id="faculty_id" name="faculty_id" />

                                <div class="flex justify-end space-x-4">
                                    <button type="button" onclick="closeModal()" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700">
                                        Close
                                    </button>
                                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Footer Section -->

                    </div>
                </div>


                <script>
                    // Close modal if clicked outside the modal content area
                    function closeOnOutsideClickReturned(event) {
                        const modalContent = document.querySelector("#modalOverlay > div");
                        if (!modalContent.contains(event.target)) {
                            closeModal();
                        }
                    }

                    // Open the modal and populate fields with data
                    function openModal(facultyId, name, gender, email, contact, yearLevel, status) {
                        document.getElementById('faculty_id').value = facultyId;
                        document.getElementById('name').value = name;
                        document.getElementById('gender').value = gender;
                        document.getElementById('email').value = email;
                        document.getElementById('contact').value = contact;
                        document.getElementById('year_level').value = yearLevel;
                        document.getElementById('status').value = status;

                        // Show the modal
                        document.getElementById('modalOverlay').classList.remove('hidden');
                    }

                    // Close the modal
                    function closeModal() {
                        document.getElementById('modalOverlay').classList.add('hidden');
                    }

                    // Save form data when 'Save' button is clicked
                    document.getElementById('facultyForm').addEventListener('submit', function(event) {
                        event.preventDefault(); // Prevent form submission default action
                        const facultyId = document.getElementById('faculty_id').value; // Get the student ID from the hidden input

                        const formData = {
                            id: facultyId, // Include the student ID
                            status: document.getElementById('status').value // Include the status
                        };

                        // Perform the POST request to save the data
                        fetch('faculty_edit.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(formData),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Status updated successfully!');
                                    closeModal(); // Close the modal if save was successful
                                    location.reload(); // Reload the page to reflect changes
                                } else {
                                    alert('Failed to update status');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while saving the status');
                            });
                    });
                </script>





















            </div>
        </div>

    </main>



    <script src="./src/components/header.js"></script>
    <!-- jQuery -->


</body>

</html>