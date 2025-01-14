<?php
include '../connection.php'; // Include your database connection file

session_start();
if (!isset($_SESSION['logged_Admin_assistant']) || $_SESSION['logged_Admin_assistant'] !== true) {
    header('Location: ../index.php');

    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course-name'])) {
    // Get the course name from the form
    $courseName = trim($_POST['course-name']);

    // Validate the input
    if (!empty($courseName)) {
        // Insert the course name into the database
        $sql = "INSERT INTO course (course) VALUES (?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $courseName); // Bind the course name as a string

            // Execute the query
            if ($stmt->execute()) {

                header("Location: " . $_SERVER['PHP_SELF'] . "?added_success=1");
                exit;
            } else {
                $_SESSION['error_message'] = 'Failed to add the course: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Failed to prepare SQL statement: ' . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = 'Course name is required.';
    }

    $conn->close();
}







if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $courseId = intval($_POST['course_id']); // Ensure the course_id is sanitized and secure

    try {
        // SQL to delete the course
        $sql = "DELETE FROM course WHERE course_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $courseId);
    
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?deleted_success=1");
                exit;
            } else {
                throw new Exception($stmt->error);
            }
        } else {
            throw new Exception($conn->error);
        }
    } catch (mysqli_sql_exception $e) {
        // Check for foreign key constraint error
        if ($e->getCode() == 1451) { // MySQL error code for foreign key constraint
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=1");
            exit;
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=2");
            exit;
        }
    } catch (Exception $e) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=3");
        exit;
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
        .active-add-course {
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
                    <h1 class="text-3xl font-semibold">Add Course</h1> <!-- Adjusted text size -->
                    <!-- Button beside the title -->
                </div>
                <?php if (isset($_GET['added_success']) && $_GET['added_success'] == 1): ?>
                    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        Added successful!
                    </div>
                <?php endif; ?>


                <?php if (isset($_GET['deleted_success']) && $_GET['deleted_success'] == 1): ?>
    <div id="alert" class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        Deleted successfully!
    </div>
<?php endif; ?>



                <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <div id="alert" class="alert alert-danger" role="alert" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        You cannot delete this course because it is currently assigned to one or more students.
    </div>
<?php elseif (isset($_GET['error']) && $_GET['error'] == 2): ?>
    <div id="alert" class="alert alert-danger" role="alert" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        An unexpected error occurred while attempting to delete the course.
    </div>
<?php endif; ?>





                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                    edit </div>




                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">








                    <div class="flex items-start justify-center rounded   dark:bg-gray-800">



                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg shadow-md">

                            <div class="px-6 py-4 bg-blue-600 rounded-t-lg text-white">
                                <h2 class="text-lg font-semibold">Add Course</h2>
                                <p class="text-sm opacity-90">Provide the course details to add a new record.</p>
                            </div>

                            <!-- Form Section -->
                            <div class="p-6">
                                <form method="POST" id="addCourseForm" class="space-y-6">

                                    <!-- Course Name Input -->
                                    <div class="space-y-2">
                                        <label for="course-name" class="block text-sm font-medium text-gray-700">Course Name</label>
                                        <input type="text" id="course-name" name="course-name"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring focus:border-green-400"
                                            placeholder="Enter Course Name" required>
                                    </div>

                                    <!-- Course Code Input -->


                                    <!-- Action Buttons -->
                                    <div class="flex justify-end space-x-4">
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:bg-green-800">
                                            Add Course
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
                        $sql = "SELECT course, course_id FROM course ";
                        $result = $conn->query($sql);

                        // Initialize an empty array to hold fines
                        $courses = [];

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $courses[] = $row;
                            }
                        }

                        // Close the database connection
                        $conn->close();
                        ?>

                        <div class="w-full md:w-1/2 border border-gray-300 rounded-lg h-full shadow-md">
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table id="finesTable" class="w-full border-collapse stripe hover">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="text-left p-2">No.</th>
                                                <th class="text-left p-2">Courses</th>
                                                <th class="text-left p-2">action</th>

                                            </tr>
                                        </thead>
                                        <tbody id="finesTableBody">
                                            <?php
                                            if (!empty($courses)) {
                                                $no = 1; // Counter for the "No." column
                                                foreach ($courses as $course) {
                                            ?>
                                                    <tr class="border-b" data-course-id="<?php echo htmlspecialchars($course['course_id']); ?>">
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo $no++; ?>
                                                        </td>

                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                            <?php echo htmlspecialchars($course['course']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 break-words" style="max-width: 300px;">
                                                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this course?');">
    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['course_id']); ?>">
    <button type="submit"
        class="flex items-center px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-800">
        <i class="fas fa-trash mr-2"></i> Delete
    </button>
</form>

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