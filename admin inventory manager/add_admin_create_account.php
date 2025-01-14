<?php
// Include your database connection file
include '../connection.php';

// Get the raw POST data from the request
$input = file_get_contents('php://input');
$data = json_decode($input, true);
file_put_contents('debug_log.txt', print_r($data, true), FILE_APPEND); // Debug logging

// Check if the required data exists and is valid
if (isset($data['fullname'], $data['username'], $data['email'], $data['password'])) {
    $fullname = mysqli_real_escape_string($conn, $data['fullname']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    $role_id = 3; // Default role_id for admin accounts

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $checkUserQuery = "SELECT * FROM admin_account WHERE Username='$username' OR Email='$email'";
    $result = $conn->query($checkUserQuery);

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or Email already exists!']);
    } else {
        // Insert the new user into the database
        $query = "INSERT INTO admin_account (Full_Name, Username, Email, Password, role_id) 
                  VALUES ('$fullname', '$username', '$email', '$hashed_password', $role_id)";

        if ($conn->query($query) === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data!']);
}

// Close the database connection
$conn->close();
?>
