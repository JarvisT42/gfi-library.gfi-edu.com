<?php
// Database connection
include("connection.php");

if (isset($_GET['token']) && isset($_GET['email'])) {
    $plain_token = $_GET['token']; // Plain token from the URL
    $email = $_GET['email']; // Email from the URL

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
        $row = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
        </head>
        <body>
            <h2>Reset Your Password</h2>
            <form action="update_password.php" method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($plain_token); ?>">
                <label for="password">New Password:</label>
                <input type="password" name="password" id="password" required minlength="8"><br><br>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required minlength="8"><br><br>
                <button type="submit">Reset Password</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "Invalid request.";
}
?>
