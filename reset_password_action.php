<?php
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

if (isset($_POST["submit"])) {
    $email = $_POST["email"];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM students WHERE Email_Address = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        try {
            // Generate a secure token
            $plain_token = bin2hex(random_bytes(32)); // Plain token
            $hashed_token = hash('sha256', $plain_token); // Hash token for security
            $expiry = date("Y-m-d H:i:s", strtotime('+1 minutes')); // Token expiry time

            // Debug: Output PHP time and calculated expiry
            echo "PHP time: " . date("Y-m-d H:i:s") . "<br>";
            echo "Calculated expiry: " . $expiry . "<br>";

            // Store the hashed token in the database
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_token, $expiry);
            $stmt->execute();

            // Verify synchronization
            $db_time = $conn->query("SELECT NOW() AS db_time")->fetch_assoc();
            echo "MySQL time: " . $db_time['db_time'] . "<br>";

            // Create the reset link with the plain token
            $reset_link = "http://localhost:3000/gfi-library.gfi-edu.com/reset_password_form.php?token=$plain_token&email=" . urlencode($email);

            // Configure PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'mail.gfi-edu.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'gfcilibrary@gfi-edu.com'; // Your SMTP email
            $mail->Password = '0l)^v*8UI(8;'; // Your SMTP password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->addBCC('gfcilibrary@gfi-edu.com');

            // Set "No-Reply" email address
            $mail->setFrom('no-reply@gfi-edu.com', 'GFCI Library'); // No-reply address
            $mail->addAddress($email); // Recipient's email address
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <p>Hi,</p>
                <p>We received a request to reset your password. Click the link below to reset it:</p>
                <p><a href='$reset_link'>$reset_link</a></p>
                <p>If you didn't request this, you can ignore this email.</p>
            ";

            // Send the email
            $mail->send();
            echo "Password reset link sent to your email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }
} else {
    echo "Please submit the form.";
}

?>
