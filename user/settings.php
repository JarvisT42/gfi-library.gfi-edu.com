<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');

    exit;
}

$user_id = $_SESSION['Student_Id'];

// Fetch user profile data from the database
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = null;
    echo "No user found with this ID.";
}









include '../connection.php'; // Include your database connection file


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $course = htmlspecialchars($_POST['course']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mobileNumber = htmlspecialchars($_POST['mobile_number']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Fetch the current password hash from the database
    $sql = "SELECT Password FROM students WHERE Student_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($dbPasswordHash);
    $stmt->fetch();
    $stmt->close();

    $errorMessages = [];

    // If the user provides a current password, validate it
    if (!empty($currentPassword) && !password_verify($currentPassword, $dbPasswordHash)) {
        $errorMessages['current_password'] = "The current password is incorrect.";
    }

    // If a new password is provided, validate it and ensure it matches the confirm password
    if (!empty($newPassword) || !empty($confirmPassword)) {
        if ($newPassword !== $confirmPassword) {
            $errorMessages['password_mismatch'] = "New password and confirm password do not match.";
        }

        if (strlen($newPassword) < 8) {
            $errorMessages['password_length'] = "New password must be at least 8 characters long.";
        }
    }

    if (empty($errorMessages)) {
        // Hash the new password if provided
        $hashedPassword = !empty($newPassword) ? password_hash($newPassword, PASSWORD_DEFAULT) : $dbPasswordHash;

        // Update the student's profile in the database
        $sql = "UPDATE students 
                SET 
                    First_Name = ?, 
                    Last_Name = ?, 
                    course_id = (SELECT course_id FROM course WHERE course = ? LIMIT 1), 
                    Email_Address = ?, 
                    mobile_number = ?, 
                    Password = ?
                WHERE 
                    Student_Id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssss',
            $firstName,
            $lastName,
            $course,
            $email,
            $mobileNumber,
            $hashedPassword,
            $user_id
        );

        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!'); window.location.href='settings.php';</script>";
        } else {
            echo "Error updating profile: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        // Redirect with error messages
        $_SESSION['error_messages'] = $errorMessages;
        header("Location: " . $_SERVER['PHP_SELF']);
    }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
    <!-- Include Tailwind CSS -->
    <!-- Latest Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">

    <!-- Latest Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />

    <!-- Latest Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .active-settings {
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





                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6">
                            <h1 class="text-2xl font-bold mb-6">
                                Profile: <?php echo htmlspecialchars($user['First_Name'] . ' ' . $user['Last_Name']); ?>
                            </h1>
                            <div class="flex flex-col md:flex-row gap-8">
                                <!-- Profile Picture Section -->
                                <div class="w-30 md:w-1/3">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>"
                                        alt="Profile Picture"
                                        class="w-full h-auto rounded-lg mb-4">
                                    <label class="block">
                                        <span class="sr-only">Choose profile picture</span>
                                        <input type="file" name="profile_picture"
                                            class="block w-full text-sm text-gray-500 border rounded-lg">
                                    </label>
                                </div>
                                <!-- Profile Information Section -->
                                <div class="w-full md:w-2/3">
                                    <h2 class="text-xl font-semibold mt-8 mb-4">Profile Setting</h2>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input type="text" name="first_name"
                                                value="<?php echo htmlspecialchars($user['First_Name']); ?>"
                                                class="w-full border rounded-md px-3 py-2" placeholder="First Name">
                                            <button class="ml-2 bg-orange-500 text-white p-2 rounded-md">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="text" name="last_name"
                                                value="<?php echo htmlspecialchars($user['Last_Name']); ?>"
                                                class="w-full border rounded-md px-3 py-2" placeholder="Last Name">
                                            <button class="ml-2 bg-orange-500 text-white p-2 rounded-md">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center">
                                            <select name="course" class="w-full border rounded-md px-3 py-2">
                                                <?php
                                                $sql = "SELECT course FROM course";
                                                $result = $conn->query($sql);
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $selected = ($user['course_id'] == $row['course']) ? 'selected' : '';
                                                        echo '<option ' . $selected . '>' . htmlspecialchars($row['course'], ENT_QUOTES, 'UTF-8') . '</option>';
                                                    }
                                                } else {
                                                    echo '<option>No courses available</option>';
                                                }
                                                ?>
                                            </select>
                                            <button class="ml-2 bg-orange-500 text-white p-2 rounded-md">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <h2 class="text-xl font-semibold mt-8 mb-4">Contact Setting</h2>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input type="email" name="email"
                                                value="<?php echo htmlspecialchars($user['Email_Address']); ?>"
                                                class="w-full border rounded-md px-3 py-2" placeholder="Email Address">
                                            <button class="ml-2 bg-orange-500 text-white p-2 rounded-md">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="tel" name="mobile_number"
                                                value="<?php echo htmlspecialchars($user['mobile_number']); ?>"
                                                class="w-full border rounded-md px-3 py-2" placeholder="Mobile Phone">
                                            <button class="ml-2 bg-orange-500 text-white p-2 rounded-md">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <h2 class="text-xl font-semibold mb-4">Account Setting</h2>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input type="password" name="current_password"
                                                placeholder="Current Password"
                                                class="w-full border rounded-md px-3 py-2 <?php echo isset($errorMessages['current_password']) ? 'border-red-500' : ''; ?>">
                                        </div>
                                        <?php if (isset($errorMessages['current_password'])): ?>
                                            <p class="text-red-500 text-sm mt-1"><?php echo $errorMessages['current_password']; ?></p>
                                        <?php endif; ?>

                                        <div class="flex items-center">
                                            <input type="password" name="password"
                                                placeholder="New Password"
                                                class="w-full border rounded-md px-3 py-2 <?php echo isset($errorMessages['password_length']) ? 'border-red-500' : ''; ?>">
                                        </div>
                                        <?php if (isset($errorMessages['password_length'])): ?>
                                            <p class="text-red-500 text-sm mt-1"><?php echo $errorMessages['password_length']; ?></p>
                                        <?php endif; ?>

                                        <div class="flex items-center">
                                            <input type="password" name="confirm_password"
                                                placeholder="Confirm Password"
                                                class="w-full border rounded-md px-3 py-2 <?php echo isset($errorMessages['password_mismatch']) ? 'border-red-500' : ''; ?>">
                                        </div>
                                        <?php if (isset($errorMessages['password_mismatch'])): ?>
                                            <p class="text-red-500 text-sm mt-1"><?php echo $errorMessages['password_mismatch']; ?></p>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button type="submit" name="save" class="bg-green-600 text-white px-4 py-2 rounded-md">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>










            </div>
        </div>

    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const currentPassword = document.querySelector("input[name='current_password']");
            const newPassword = document.querySelector("input[name='password']");
            const confirmPassword = document.querySelector("input[name='confirm_password']");

            form.addEventListener("submit", function(event) {
                let errorMessage = "";

                // Validate new password and confirm password match (only if provided)
                if (newPassword.value.trim() || confirmPassword.value.trim()) {
                    if (newPassword.value.trim() !== confirmPassword.value.trim()) {
                        errorMessage += "New password and confirm password must match.\n";
                    }

                    // Validate new password is at least 8 characters long
                    if (newPassword.value.trim().length < 8) {
                        errorMessage += "New password must be at least 8 characters long.\n";
                    }
                }

                if (errorMessage) {
                    event.preventDefault();
                    alert(errorMessage);
                }
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editableFields = document.querySelectorAll("input, select");

            editableFields.forEach((field) => {
                // Exclude password fields from being readonly
                if (["current_password", "password", "confirm_password"].includes(field.name)) {
                    return; // Skip setting readonly for password fields
                }

                // Make all other fields readonly by default
                field.setAttribute("readonly", true);
                field.classList.add("bg-gray-100"); // Add a visual cue for readonly

                // Add click event listener to sibling buttons
                const editButton = field.nextElementSibling;
                if (editButton) {
                    editButton.addEventListener("click", (e) => {
                        e.preventDefault(); // Prevent form submission

                        if (field.hasAttribute("readonly")) {
                            // Enable editing
                            field.removeAttribute("readonly");
                            field.classList.remove("bg-gray-100");
                            field.classList.add("bg-white", "border-blue-500", "focus:ring-blue-500", "focus:border-blue-500");
                        } else {
                            // Disable editing
                            field.setAttribute("readonly", true);
                            field.classList.remove("bg-white", "border-blue-500", "focus:ring-blue-500", "focus:border-blue-500");
                            field.classList.add("bg-gray-100");
                        }
                    });
                }
            });
        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('alert');
            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.display = 'none';
                }, 5000); // Auto-hide after 5 seconds
            }
        });
    </script>

    <script src="./src/components/header.js"></script>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>