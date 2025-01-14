<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$loginError = false;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Include PHPMailer classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Set PHP timezone
date_default_timezone_set('Asia/Manila');

// Database connection
$conn = new mysqli("localhost", "dnllaaww_ramoza", "Ramoza@30214087695", "dnllaaww_gfi_library");

// Synchronize MySQL timezone with PHP
$conn->query("SET time_zone = '+08:00'");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot_password'])) {
    header('Content-Type: application/json'); // Set response type to JSON

    $email = $_POST["email"];
    $response = [];

    // Check if the email exists in the students or faculty table
    $stmt = $conn->prepare("
        SELECT Email_Address
        FROM (
            SELECT Email_Address FROM students
            UNION
            SELECT Email_Address FROM faculty
        ) AS combined
        WHERE Email_Address = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        try {
            // Generate a secure token
            $plain_token = bin2hex(random_bytes(32));
            $hashed_token = hash('sha256', $plain_token);
            $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes')); // Extended expiry time for usability

            // Store the hashed token in the database
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_token, $expiry);
            $stmt->execute();

            // Create the reset link
            $reset_link = "http://localhost:3000/gfi-library.gfi-edu.com/reset_password_form.php?token=" . urlencode($plain_token) . "&email=" . urlencode($email);

            // Configure PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'mail.gfi-edu.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gfcilibrary@gfi-edu.com';
            $mail->Password = '0l)^v*8UI(8;';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->addBCC('gfcilibrary@gfi-edu.com');

            $mail->setFrom('no-reply@gfi-edu.com', 'GFCI Library');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                <!-- Header -->
                <div style='background: linear-gradient(to right, #ffc107, #ff5722); padding: 20px; text-align: center;'>
                    <img src='http://gfi-library.gfi-edu.com/src/assets/images/icon-gfi-book.png'
                         alt='GFI Library Logo'
                         style='max-width: 100px;'>
                    <h2 style='margin: 10px 0 0; color: #fff;'>GFI's Online Library</h2>
                </div>

                <!-- Body -->
                <div style='padding: 20px; background-color: #f8f9fa;'>
                    <h3 style='color: #333;'>Hi,</h3>
                    <p style='font-size: 14px; line-height: 1.6; color: #555;'>
                        We received a request to reset your password. Click the link below to reset it:
                    </p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='$reset_link'
                           style='display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #ff5722; text-decoration: none; border-radius: 5px;'>
                           Reset Password
                        </a>
                    </div>
                    <p style='font-size: 14px; line-height: 1.6; color: #555;'>
                        If you didn't request this, you can ignore this email.
                    </p>
                </div>

                <!-- Footer -->
                <div style='background-color: #ffc107; padding: 10px; text-align: center; color: #fff; font-size: 12px;'>
                    <p style='margin: 0;'>© 2024 GFI Foundation College, Inc. All Rights Reserved.</p>
                    <p style='margin: 0;'>
                        <a href='https://facebook.com/yourpage' style='color: #fff; text-decoration: none; margin-right: 10px;'>Facebook</a> |
                        <a href='https://youtube.com/yourchannel' style='color: #fff; text-decoration: none;'>YouTube</a>
                    </p>
                </div>
            </div>
        ";

            // Send the email
            $mail->send();

            $response['status'] = 'success';
            $response['message'] = 'Password reset link sent to your email.';
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'An error occurred while sending the email. Please try again later.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Email not found.';
    }

    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_login'])) {
    include 'connection.php'; // Include your database connection file

    // Get form input
    $email = $_POST['email'];
    $password = $_POST['password'];
    $loginError = false; // Flag to manage error display
    $loginMessage = ""; // Variable for custom error messages

    // Check in the students table first
    $stmt = $conn->prepare("SELECT * FROM students WHERE Email_Address = ? AND status != 'inactive' AND status != 'banned'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Student found, verify password
        $row = $result->fetch_assoc();
        $hashed_password = $row['Password'];

        if (password_verify($password, $hashed_password)) {
            // Successful student login
            $_SESSION["loggedin"] = TRUE;
            $_SESSION["Student_Id"] = $row['Student_Id'];
            $_SESSION["First_Name"] = $row['First_Name'];
            $_SESSION["Middle_Initial"] = $row['Middle_Initial'];
            $_SESSION["Last_Name"] = $row['Last_Name'];
            $_SESSION['email'] = $row['Email_Address'];
            $_SESSION['phoneNo.'] = $row['mobile_number'];
            $_SESSION['first_login'] = true;
            header("Location: user/dashboard.php");
            exit();
        } else {
            $loginError = true;
            $loginMessage = "Incorrect password for student account.";
        }
    } else {
        // No student found, check in faculty table
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM faculty WHERE Email_Address = ? AND status != 'inactive' AND status != 'banned'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Faculty found, verify password
            $row = $result->fetch_assoc();
            $hashed_password = $row['Password'];
            if (password_verify($password, $hashed_password)) {
                // Successful faculty login
                $_SESSION["loggedin"] = TRUE;
                $_SESSION["Faculty_Id"] = $row['Faculty_Id'];
                $_SESSION["First_Name"] = $row['First_Name'];
                $_SESSION["Middle_Initial"] = $row['Middle_Initial'];
                $_SESSION["Last_Name"] = $row['Last_Name'];
                $_SESSION['email'] = $row['Email_Address'];
                $_SESSION['phoneNo.'] = $row['mobile_number'];
                header("Location: faculty/dashboard.php");
                exit();
            } else {
                $loginError = true;
                $loginMessage = "Incorrect password for faculty account.";
            }
        } else {
            // No faculty found, check in admin table
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM admin_account WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Admin found, verify password
                $row = $result->fetch_assoc();
                $hashed_password = $row['Password'];
                if (password_verify($password, $hashed_password)) {
                    // Successful admin login
                    $_SESSION["admin_id"] = $row['admin_id'];
                    $_SESSION["Full_Name"] = $row['Full_Name'];
                    // Check role_id in admin_account and retrieve role_name from roles table
                    $role_id = $row['role_id'];
                    $stmt->close();
                    // Fetch role_name from roles table
                    $stmt = $conn->prepare("SELECT role_name FROM roles WHERE role_id = ?");
                    $stmt->bind_param("i", $role_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $role_row = $result->fetch_assoc();
                        $role_name = $role_row['role_name'];
                        // Redirect based on role_name 

                        if ($role_name === "super-admin" || $role_name === "admin") {
                            $_SESSION["logged_Admin"] = TRUE;

                            header("Location: admin/dashboard.php");
                        } elseif ($role_name === "assistant") {
                            $_SESSION["logged_Admin_assistant"] = TRUE;

                            header("Location: admin inventory manager/dashboard.php");
                        } else {
                            // Default redirection or error handling if role_name doesn't match
                            $loginError = true;
                            $loginMessage = "Role not recognized.";
                        }
                        exit();
                    } else {
                        // Role not found in roles table
                        $loginError = true;
                        $loginMessage = "Role not found for admin account.";
                    }
                } else {
                    $loginError = true;
                    $loginMessage = "Incorrect password for admin account.";
                }
            } else {
                // No student, faculty, or admin found with that email
                $loginError = true;
                $loginMessage = "No account found with that email.";
            }
        }
    }
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_register'])) {
    include 'connection.php';
    // Get form inputs
    $validated_student_id = $conn->real_escape_string($_POST['validated_student_id'] ?? '');
    $firstName = $conn->real_escape_string($_POST['First_Name'] ?? '');
    $middleInitial = $conn->real_escape_string($_POST['Middle_Initial'] ?? '');
    $lastName = $conn->real_escape_string($_POST['Last_Name'] ?? '');
    $suffixName = $conn->real_escape_string($_POST['Suffix_Name'] ?? '');
    $email = $conn->real_escape_string($_POST['Email_Address'] ?? '');
    $gender = $conn->real_escape_string($_POST['S_Gender'] ?? '');
    $dateOfJoining = date('Y-m-d');
    $course_id = (int) ($_POST['course_id'] ?? 0); // Ensure course_id is an integer
    $idNumber = $conn->real_escape_string($_POST['Id_Number'] ?? '');
    $mobileNumber = $conn->real_escape_string($_POST['Mobile_Number'] ?? '');
    $yearLevel = $conn->real_escape_string($_POST['Year_Level'] ?? '');
    $password = $_POST['Password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    // Prepare SQL statement
    $sql = "INSERT INTO students (Student_Id, First_Name, Middle_Initial, Last_Name, Suffix_Name, Email_Address, course_id, S_Gender, date_of_joining, Mobile_Number, Year_Level, Password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssss",
        $validated_student_id,
        $firstName,
        $middleInitial,
        $lastName,
        $suffixName,
        $email,
        $course_id,
        $gender,
        $dateOfJoining,
        $mobileNumber,
        $yearLevel,
        $hashed_password
    );
    if ($stmt->execute()) {
        $updateStmt = $conn->prepare("UPDATE students_ids SET status = 'Taken' WHERE student_id = ?");
        $updateStmt->bind_param("s", $validated_student_id);

        if ($updateStmt->execute()) {
            echo "<script>alert('Student registered successfully and status updated!');</script>";
        } else {
            echo "Error updating student status: " . $updateStmt->error;
        }

        $updateStmt->close();
    } else {
        echo "Error registering student: " . $stmt->error;
    }
    // Close connections
    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['faculty_register'])) {
    include 'connection.php';
    // Capture form input and validated faculty ID
    $faculty_id = $_POST['validated_faculty_id'] ?? null; // Capture validated faculty_id
    $firstName = $conn->real_escape_string($_POST['firstname']);
    $middleInitial = $conn->real_escape_string($_POST['middlename']);
    $lastName = $conn->real_escape_string($_POST['lastname']);
    $suffixName = $conn->real_escape_string($_POST['suffix']);
    $email = $conn->real_escape_string($_POST['email']);
    $department_id = (int) ($_POST['department_id'] ?? 0); // Ensure course_id is an integer

    $mobileNumber = $conn->real_escape_string($_POST['txtEmpPhone']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $dateOfJoining = date('Y-m-d'); // Auto set to current date
    $employmentStatus = $conn->real_escape_string($_POST['employment_status']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check for matching passwords
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit();
    }
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    // Insert the data, including faculty_id, into the faculty table
    $stmt = $conn->prepare("INSERT INTO faculty (Faculty_Id, First_Name, Middle_Initial, Last_Name, Suffix_Name, Email_Address, S_Gender, date_of_joining, department_id, Mobile_Number, Password, employment_status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssss", $faculty_id, $firstName, $middleInitial, $lastName, $suffixName, $email, $gender, $dateOfJoining, $department_id, $mobileNumber, $hashed_password, $employmentStatus);

    if ($stmt->execute()) {
        // Update faculty_id table with the validated faculty ID
        $updateStmt = $conn->prepare("UPDATE faculty_ids SET status = 'Taken' WHERE faculty_id = ?");
        $updateStmt->bind_param("s", $faculty_id);
        $updateStmt->execute();

        echo "<script>alert('Faculty registered successfully!');</script>";
        $updateStmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }
    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ffe259, #ffa751);
            font-family: Arial, sans-serif;
        }

        .navbar-brand span {
            white-space: normal;
            word-wrap: break-word;
            text-align: center;
            font-size: 1.3rem;
        }

        .footer {
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .navbar-brand img {
            height: 70px;
            width: auto;
        }

        .register-left img {
            width: 100px;
        }

        .btnRegister {
            background: #d4cd00;
            color: #fff;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .sign-in-up {
                margin-top: 3%;
                padding: 2%;
            }

            .register-left img {
                width: 80px;
            }

            .footer {
                position: static;
            }
        }

        @media (max-width: 576px) {
            .sign-in-up {
                margin-top: 2%;
                padding: 2%;
            }

            .register-left img {
                width: 60px;
            }
        }

        .header {
            padding: 0rem 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: #333;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #b63a29;
        }

        .btn-login {
            background-color: #b63a29;
            color: #fff;
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }

        .btn-login:hover {
            background-color: #8c2921;
        }

        .footer {
            background-color: #8c2921;
        }

        .footer .social-icons a {
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .footer .social-icons a:hover {
            color: #ffcc00;
        }
    </style>
</head>

<body>

    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="./src/assets/images/library.png" alt="Library Logo" class="mr-2">
                    <img src="./src/assets/images/350861720_596709698913796_561423606268093637_n.png" alt="logo" />

                    <span class="font-weight-bold text-dark">&nbsp;&nbsp;&nbsp;Gensantos Foundation College, Inc. Library</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="btn btn-login nav-link text-white" href="index.php">HOME</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="container sign-in-up">
        <div class="row">
            <div class="col-md-3 register-left ">
                <img src="src/assets/images/icon-gfi-book.png" alt="" />
                <h3>Welcome</h3>
                <p>GFI's Online Library</p>
                <input type="button" id="toggleLogin" value="Register" /><br />
            </div>

            <div class="col-md-9 login-right">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form id="loginForm" class="student" method="POST" action="">
                            <h3 class="login-heading">Login Account</h3>
                            <div class="row login-form">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" name="email" class="form-control" placeholder="Your Email *" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Your Password *" required />
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePasswordVisibility()">
                                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group d-flex justify-content-between">
                                            <label class="form-check-label">
                                                <input type="checkbox" id="rememberMe" name="rememberMe" onclick="handleRememberMe()" />
                                                Remember Me
                                            </label>
                                            <div class="">
                                                <a id="forgot_password" href="#" role="tab" aria-controls="ForgotPassword" aria-selected="false">Forgot Password?</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger" style="display: <?php echo $loginError ? 'block' : 'none'; ?>;">
                                        <?php echo isset($loginMessage) ? $loginMessage : ''; ?>
                                    </div>
                                    <input type="submit" class="btnlogin" name="student_login" value="Login" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function togglePasswordVisibility() {
                    const passwordInput = document.getElementById('password');
                    const toggleIcon = document.getElementById('togglePasswordIcon');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                }

                function handleRememberMe() {
                    const rememberMeCheckbox = document.getElementById('rememberMe');

                    if (rememberMeCheckbox.checked) {
                        const email = document.querySelector('input[name="email"]').value;
                        const password = document.querySelector('input[name="password"]').value;
                        localStorage.setItem('rememberMe', 'true');
                        localStorage.setItem('email', email);
                        localStorage.setItem('password', password);
                    } else {
                        localStorage.removeItem('rememberMe');
                        localStorage.removeItem('email');
                        localStorage.removeItem('password');
                    }
                }
                window.onload = function() {
                    if (localStorage.getItem('rememberMe') === 'true') {
                        document.getElementById('rememberMe').checked = true;
                        document.querySelector('input[name="email"]').value = localStorage.getItem('email') || '';
                        document.querySelector('input[name="password"]').value = localStorage.getItem('password') || '';
                    }
                };
            </script>

            <div class="col-md-9 forgot-password-right" style="display: none;">
                <form id="forgotPassword">
                    <h3 class="forgot-password-heading">Forgot Password</h3>
                    <div class="row forgot-password-form">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Your Email *" required />
                                </div>
                                <div id="forgotPasswordAlert" class="alert" style="display: none;"></div>
                            </div>
                            <button type="button" class="btnlogin" id="submitForgotPassword">Send Email</button>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                $(document).ready(function() {
                    $('#submitForgotPassword').on('click', function() {
                        const email = $('#email').val();
                        $('#forgotPasswordAlert').hide().removeClass('alert-success alert-danger');

                        if (!email) {
                            $('#forgotPasswordAlert').text('Please enter your email.').addClass('alert-danger').show();
                            return;
                        }

                        $.ajax({
                            url: '',
                            type: 'POST',
                            data: {
                                email: email,
                                forgot_password: true
                            },
                            dataType: 'json',
                            success: function(response) {
                                const alertClass = response.status === 'success' ? 'alert-success' : 'alert-danger';
                                $('#forgotPasswordAlert')
                                    .text(response.message)
                                    .addClass(alertClass)
                                    .show();
                            },
                            error: function() {
                                $('#forgotPasswordAlert').text('An unexpected error occurred. Please try again.').addClass('alert-danger').show();
                            },
                        });
                    });
                });
            </script>

            <div class="col-md-9 validation-right" style="display: none;">
                <form id="validationForm">
                    <h3 class="validation-heading">Verify Id No.</h3>
                    </h3>
                    <div class="row validation-form">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <p class="validation-message alert alert-warning d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Before you proceed, make sure that your Student ID No. belongs to you.
                                    </p>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" name="student_id" id="student_id" class="form-control" placeholder="Enter Student ID No." required />
                                </div>
                            </div>
                            <div id="accessDeniedAlert" class="alert alert-danger" style="display: none;">Incorrect Student ID No.</div>
                            <input type="button" class="btnRegister" id="proceedToRegister" value="Proceed" />
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-9 register-right" style="display: none;">
                <form id="registrationStudentForm" class="register" method="POST" action="login.php">
                    <h3 class="register-heading"> Library Registration</h3>
                    <div class="row register-form">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="hidden" name="validated_student_id" id="validated_student_id" />
                                    <input type="text" name="First_Name" class="form-control" placeholder="First Name *" required />
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="Middle_Initial" class="form-control" placeholder="MI" style="max-width: 50px;" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="Last_Name" class="form-control" placeholder="Last Name *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    </div>
                                    <input type="text" name="Suffix_Name" class="form-control" placeholder="Suffix (e.g., Jr., Sr.)" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="Email_Address" class="form-control" placeholder="Your Email *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" minlength="10" maxlength="11" name="Mobile_Number" class="form-control" placeholder="Your Phone *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="Year_Level" class="form-control">
                                    <option class="hidden" selected disabled>Year Level</option>
                                    <option>1st Year</option>
                                    <option>2nd Year</option>
                                    <option>3rd Year</option>
                                    <option>4th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" name="course_id" id="course_id" required>
                                    <option value="" disabled selected>Course</option>
                                    <?php
                                    include 'connection.php';
                                    $sql = "SELECT course_id, course FROM course";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course']) . '</option>';
                                        }
                                    } else {
                                        echo '<option disabled>No courses available</option>';
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="maxl">
                                    <label class="radio inline">
                                        <input type="radio" name="S_Gender" value="male" checked>
                                        <span class="radio-label"> Male </span>
                                    </label>
                                    <label class="radio inline">
                                        <input type="radio" name="S_Gender" value="female">
                                        <span class="radio-label"> Female </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="Password" id="student_password" class="form-control" placeholder="Password *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>

                                    <input type="password" name="confirm_password" id="student_confirm_password" class="form-control" placeholder="Confirm Password *" required />
                                </div>
                            </div>
                        </div>

                        <div id="studentPasswordAlert" class="alert alert-danger" style="display: none;">
                            Passwords do not match. Please re-enter.
                        </div>

                        <div class="col-md-10">
                            <input type="hidden" name="Register_Status" value="Pending" />
                            <input type="submit" class="btnRegister" name="student_register" value="Register" />
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-9 registerFaculty-right" style="display: none;">
                <form id="registrationFacultyForm" class="register" method="POST" action="">
                    <h3 class="register-heading">Library Registration</h3>
                    <div class="row register-form">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="firstname" class="form-control" placeholder="First Name *" />
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="middlename" class="form-control" placeholder="MI" style="max-width: 50px;" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="lastname" class="form-control" placeholder="Last Name *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                    </div>
                                    <input type="text" name="suffix" class="form-control" placeholder="Suffix (e.g., Jr., Sr.)" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="email" class="form-control" placeholder="Your Email *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" minlength="10" maxlength="10" name="txtEmpPhone" class="form-control" placeholder="Your Phone *" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" name="department_id" id="department_id" required>
                                    <option value="" disabled selected>Department</option>
                                    <?php
                                    include 'connection.php';
                                    $sql = "SELECT department_id, department_name FROM departments WHERE archive != 'yes'";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($row['department_id']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                                        }
                                    } else {
                                        echo '<option disabled>No department_names available</option>';
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                        </div>


                        


                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="maxl">
                                    <label class="radio inline">
                                        <input type="radio" name="gender" value="male" checked>
                                        <span class="radio-label"> Male </span>
                                    </label>
                                    <label class="radio inline">
                                        <input type="radio" name="gender" value="female">
                                        <span class="radio-label"> Female </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    </div>
                                    <select name="employment_status" class="form-control" required>
                                        <option class="hidden" selected disabled>Employment Status</option>
                                        <option value="full_time">Full-time</option>
                                        <option value="part_time">Part-time</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="password" id="faculty_password" class="form-control" placeholder="Password *" required />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="confirm_password" id="faculty_confirm_password" class="form-control" placeholder="Confirm Password *" required />

                                </div>
                            </div>
                        </div>

                        <div id="facultyPasswordAlert" class="alert alert-danger" style="display: none;">
                            Passwords do not match. Please re-enter.
                        </div>

                        <div class="col-md-10">
                            <input type="hidden" name="validated_faculty_id" id="validated_faculty_id" />
                            <input type="submit" class="btnRegister" name="faculty_register" value="Register" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Student Form Validation
        document.getElementById('registrationStudentForm').addEventListener('submit', function(event) {
            var password = document.getElementById('student_password').value;
            var confirmPassword = document.getElementById('student_confirm_password').value;
            var passwordAlert = document.getElementById('studentPasswordAlert');
            if (password !== confirmPassword) {
                passwordAlert.style.display = 'block';
                event.preventDefault();
            } else {
                passwordAlert.style.display = 'none';
            }
        });
        // Faculty Form Validation
        document.getElementById('registrationFacultyForm').addEventListener('submit', function(event) {
            var password = document.getElementById('faculty_password').value;
            var confirmPassword = document.getElementById('faculty_confirm_password').value;
            var passwordAlert = document.getElementById('facultyPasswordAlert');

            if (password !== confirmPassword) {
                passwordAlert.style.display = 'block';
                event.preventDefault();
            } else {
                passwordAlert.style.display = 'none';
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            const lockoutDurations = [1, 3, 8, 60]; // Lockout durations in minutes
            let attempts = 0; // Number of failed attempts
            let lockoutEndTime = 0; // Timestamp when the user can retry
            // Toggle between login and registration forms
            $("#toggleLogin").click(function() {
                // Hide forms and reset input fields
                $(".validation-right").hide();
                $(".register-right").hide();
                $(".registerFaculty-right").hide(); // Ensure faculty registration is hidden
                $(".forgot-password-right").hide(); // Hide forgot password form
                $("#validationForm")[0].reset(); // Reset validation form
                $("#registrationStudentForm")[0].reset(); // Reset student registration form
                $("#registrationFacultyForm")[0].reset(); // Reset faculty registration form
                // Toggle visibility of login and validation forms
                if ($(".login-right").is(":visible")) {
                    $(".login-right").hide();
                    $(".validation-right").show();
                    $(this).val("Login");
                } else {
                    $(".login-right").show();
                    $(".validation-right").hide();
                    $(this).val("Register");
                }
            });
            // Show forgot password form
            $("#forgot_password").click(function(e) {
                e.preventDefault(); // Prevent default anchor click behavior
                $(".login-right").hide(); // Hide login form
                $(".forgot-password-right").show(); // Show forgot password form
                $("#toggleLogin").val("Log In"); // Change register button text to "Log In"
            });
            // Show registration form after clicking Proceed
            $("#proceedToRegister").click(function() {
                const student_id = $("#student_id").val();
                const currentTime = new Date().getTime(); // Current timestamp
                // Check if user is in lockout period
                if (currentTime < lockoutEndTime) {
                    const remainingTime = Math.ceil((lockoutEndTime - currentTime) / 1000);
                    $("#accessDeniedAlert").text(`Please wait ${remainingTime} seconds before retrying.`).show();
                    return;
                }
                // Reset lockout message
                $("#accessDeniedAlert").hide();
                // AJAX request to validate student ID
                $.ajax({
                    url: 'validate_student.php',
                    type: 'POST',
                    data: {
                        student_id: student_id
                    },
                    success: function(response) {
                        if (response === 'show_student_registration') {
                            // Show student registration form
                            $("#validated_student_id").val(student_id);
                            $(".validation-right").hide();
                            $(".register-right").show();
                            attempts = 0; // Reset attempts on successful validation
                        } else if (response === 'show_faculty_registration') {
                            // Show faculty registration form
                            $("#validated_faculty_id").val(student_id);
                            $(".validation-right").hide();
                            $(".registerFaculty-right").show();
                            attempts = 0; // Reset attempts on successful validation
                        } else if (response === 'already_registered') {
                            // Show already registered message
                            $("#accessDeniedAlert").text("Student ID is already registered").show();
                        } else {
                            // Increment attempts only after free attempts are exhausted
                            if (attempts >= 3) {
                                // Apply lockout durations
                                if (attempts - 3 >= lockoutDurations.length) {
                                    // Set lockout to max duration if attempts exceed the array length
                                    lockoutEndTime =
                                        currentTime +
                                        lockoutDurations[lockoutDurations.length - 1] * 60 * 1000;
                                    attempts = 3; // Keep attempts at 3 during max lockout period
                                } else {
                                    // Set lockout time based on the current attempt's lockout duration
                                    lockoutEndTime = currentTime + lockoutDurations[attempts - 3] * 60 * 1000;
                                }
                                // Show lockout message with time in minutes
                                const lockoutMinutes = Math.ceil(
                                    (lockoutEndTime - currentTime) / 1000 / 60
                                );
                                $("#accessDeniedAlert")
                                    .text(`Max attempts reached. Please wait ${lockoutMinutes} minute(s) before retrying.`)
                                    .show();
                            } else {
                                // Increment attempts during free attempts
                                attempts++;
                                $("#accessDeniedAlert").text("Incorrect  ID No.").show();
                            }
                        }
                    },
                    error: function() {
                        // Show error message
                        $("#accessDeniedAlert").text("An error occurred. Please try again.").show();
                    },
                });
            });

        });
    </script>

    <footer class="footer  text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Copyright Text -->
            <span class="text-center">
                © Copyright © 2024 GFI FOUNDATION COLLEGE, INC. All Rights Reserved.
            </span>
            <!-- Social Media Icons -->
            <div class="social-icons">
                <a href="#" class="text-white mx-2">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="text-white mx-2">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
        </div>
    </footer>

</html>