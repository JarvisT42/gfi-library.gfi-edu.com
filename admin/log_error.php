<?php
// Check if the necessary data is passed
if (isset($_POST['error_message']) && isset($_POST['status']) && isset($_POST['xhr'])) {
    // Collect error details
    $error_message = $_POST['error_message'];
    $status = $_POST['status'];
    $xhr = $_POST['xhr'];

    // Prepare the error message for logging
    $error_log = date('Y-m-d H:i:s') . " - ERROR: " . $error_message . "\n";
    $error_log .= "Status: " . $status . "\n";
    $error_log .= "Response: " . $xhr . "\n\n";

    // Specify the log file location
    $log_file = 'error.txt';

    // Write the error to the file
    file_put_contents($log_file, $error_log, FILE_APPEND);

    // Respond with a success message (optional)
    echo 'Error logged successfully';
}
?>
