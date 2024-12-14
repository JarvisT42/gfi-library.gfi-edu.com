<?php
session_start();
require 'connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];

    if (!empty($student_id)) {
        // Prepare and execute query to check the student_id and status in the students_id table
        $stmt = $conn->prepare("SELECT status FROM students_ids WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the status of the student_id
            $row = $result->fetch_assoc();
            $status = $row['status'];

            if ($status === 'Taken') {
                // Return message if the student ID is already registered
                echo 'already_registered';
            } else {
                // Return 'show_student_registration' if the student ID is valid
                echo 'show_student_registration';
            }
        } else {
            // Prepare and execute query to check the student_id in the faculty_id table
            $stmt = $conn->prepare("SELECT status FROM faculty_ids WHERE faculty_id = ?");
            $stmt->bind_param("s", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch the status of the faculty_id
                $row = $result->fetch_assoc();
                $status = $row['status'];

                if ($status === 'Taken') {
                    // Optionally, return a different message if the faculty ID is taken
                    echo 'Faculty ID is already registered';
                } else {
                    // Return 'show_faculty_registration' if the faculty ID is valid
                    echo 'show_faculty_registration';
                }
            } else {
                // Return 'no_correct_ID_found' if no ID is found in both tables
                echo 'no_correct_ID_found';
            }
        }

        $stmt->close();
    } else {
        echo 'invalid';
    }

    $conn->close();
}
?>
