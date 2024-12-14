<?php
session_start();
// Unset the session variables
unset($_SESSION['book_bag']);
unset($_SESSION['selected_date']);
unset($_SESSION['selected_time']);
// Return a success response (even if it's not directly used)
echo json_encode(['success' => true]);
?>
