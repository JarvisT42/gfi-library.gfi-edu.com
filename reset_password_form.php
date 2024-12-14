<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$loginError = false; // Flag for login error

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST['email'];
	$token = $_POST['token'];
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];

	// Check if passwords match
	if ($password !== $confirm_password) {
		echo "<div class='alert alert-danger'>Passwords do not match.</div>";
		exit;
	}

	// Hash the password
	$hashed_password = password_hash($password, PASSWORD_BCRYPT);

	// Verify the token again
	$hashed_token = hash('sha256', $token);
	$stmt = $conn->prepare("
        SELECT * FROM password_resets
        WHERE email = ? AND token = ? AND expires_at > NOW()
    ");
	$stmt->bind_param("ss", $email, $hashed_token);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows === 1) {
		// Determine if the email exists in the students or faculty table
		$update_stmt = null;
		$stmt_student = $conn->prepare("SELECT * FROM students WHERE Email_Address = ?");
		$stmt_student->bind_param("s", $email);
		$stmt_student->execute();
		$result_student = $stmt_student->get_result();

		if ($result_student->num_rows === 1) {
			// Update the password in the students table
			$update_stmt = $conn->prepare("UPDATE students SET Password = ? WHERE Email_Address = ?");
		} else {
			// Check if the email exists in the faculty table
			$stmt_faculty = $conn->prepare("SELECT * FROM faculty WHERE Email_Address = ?");
			$stmt_faculty->bind_param("s", $email);
			$stmt_faculty->execute();
			$result_faculty = $stmt_faculty->get_result();

			if ($result_faculty->num_rows === 1) {
				// Update the password in the faculty table
				$update_stmt = $conn->prepare("UPDATE faculty SET Password = ? WHERE Email_Address = ?");
			}
		}

		// If a matching record is found, proceed with the update
		if ($update_stmt) {
			$update_stmt->bind_param("ss", $hashed_password, $email);
			$update_stmt->execute();

			// Delete the token after use
			$delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
			$delete_stmt->bind_param("s", $email);
			$delete_stmt->execute();

			echo "<div class='alert alert-success'>Your password has been reset successfully. You can now log in.</div>";
		} else {
			echo "<div class='alert alert-danger'>Email not found in any user table.</div>";
		}
	} else {
		echo "<div class='alert alert-danger'>Invalid or expired token.</div>";
	}
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
			/* Allows the text to wrap */
			word-wrap: break-word;
			/* Break words when needed */
			text-align: center;
			/* Align text for better presentation */
			font-size: 1.3rem;
			/* Adjust font size for responsiveness */
		}

		.footer {
			position: fixed;
			width: 100%;
			bottom: 0;
		}

		.navbar-brand img {
			height: 70px;
			/* Set the height to make the logo bigger */
			width: auto;
			/* Maintain the aspect ratio */
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
			/* Dark background color */
			/* color: red; Light gray text */
		}

		.footer .social-icons a {
			font-size: 1.2rem;
			transition: color 0.3s ease;
		}

		.footer .social-icons a:hover {
			color: #ffcc00;
			/* Highlight color for hover effect */
		}
	</style>
</head>

<body>

	<header class="header">
		<nav class="navbar navbar-expand-lg navbar-light">
			<div class="container">
				<!-- Logo and Title -->
				<a class="navbar-brand d-flex align-items-center" href="#">
					<img src="./src/assets/images/library.png" alt="Library Logo" class="mr-2">
					<span class="font-weight-bold text-dark">Gensantos Foundation College, Inc. Library</span>
				</a>

				<!-- Toggle button for mobile -->
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<!-- Navigation Links -->
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
			<div class="col-md-3 register-left">
				<img src="src/assets/images/icon-gfi-book.png" alt="GFI Book Icon" />
				<h3>Welcome</h3>
				<p>GFI's Online Library</p>
				<input type="button" id="toggleLogin" value="Log-in" onclick="redirectToLogin()" /><br />
			</div>

			<script>
				function redirectToLogin() {
					window.location.href = "login.php"; // Change to your desired URL
				}
			</script>
			<?php
			// Include the database connection
			include("connection.php");

			// Check if token and email parameters exist in the URL
			if (isset($_GET['token']) && isset($_GET['email'])) {
				$plain_token = $_GET['token']; // Plain token from the URL
				$email = $_GET['email']; // Email from the URL

				// Sanitize the inputs to prevent SQL injection
				$plain_token = htmlspecialchars($plain_token, ENT_QUOTES, 'UTF-8');
				$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

				// Hash the incoming plain token using the same hashing algorithm
				$hashed_token = hash('sha256', $plain_token);

				// Query database to verify token and expiration
				$stmt = $conn->prepare("
        SELECT token, expires_at
        FROM password_resets
        WHERE email = ? AND token = ? AND expires_at > NOW()
    ");
				$stmt->bind_param("ss", $email, $hashed_token);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows === 1) {
					// Token is valid and not expired
			?>
					<div class="col-md-9 forgot-password-right">
						<form id="forgotPassword" method="POST">
							<h3 class="forgot-password-heading">Reset Your Password</h3>
							<div class="row forgot-password-form">
								<div class="col-md-6">
									<!-- New Password Field -->
									<div class="form-group">
										<label for="password">New Password</label>
										<div class="input-group">
											<input type="password" id="password" name="password" class="form-control" placeholder="Enter your new password" required />
											<div class="input-group-append">
												<span class="input-group-text" onclick="togglePasswordVisibility('password')">
													<i class="fas fa-eye" id="togglePasswordIcon"></i>
												</span>
											</div>
										</div>
									</div>

									<!-- Confirm Password Field -->
									<div class="form-group">
										<label for="confirm_password">Confirm Password</label>
										<div class="input-group">
											<input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your new password" required />
											<div class="input-group-append">
												<span class="input-group-text" onclick="togglePasswordVisibility('confirm_password')">
													<i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
												</span>
											</div>
										</div>
									</div>

									<!-- Hidden fields to send email and token -->
									<input type="hidden" name="email" value="<?php echo $email; ?>">
									<input type="hidden" name="token" value="<?php echo $plain_token; ?>">

									<!-- Alert Message -->
									<div id="forgotPasswordAlert" class="alert" style="display: none;"></div>

									<!-- Submit Button -->
									<div class="form-group mt-3">
										<button type="submit" class="btn btn-primary btn-block">Reset Password</button>
									</div>
								</div>
							</div>
						</form>
					</div>
			<?php
				} else {
					// Invalid or expired token
					echo "<div class='alert alert-danger'>Invalid or expired token. Please request a new password reset.</div>";
				}
			} else {
				// Invalid request
				echo "<div class='alert alert-danger'>Invalid request. Please use the correct password reset link.</div>";
			}
			?>

			<script>
				function togglePasswordVisibility(fieldId) {
					const passwordField = document.getElementById(fieldId);
					const toggleIcon = document.querySelector(`#${fieldId} ~ .input-group-append .fas`);

					if (passwordField.type === "password") {
						passwordField.type = "text";
						toggleIcon.classList.remove("fa-eye");
						toggleIcon.classList.add("fa-eye-slash");
					} else {
						passwordField.type = "password";
						toggleIcon.classList.remove("fa-eye-slash");
						toggleIcon.classList.add("fa-eye");
					}
				}
			</script>
		</div>
	</div>

	<script>

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