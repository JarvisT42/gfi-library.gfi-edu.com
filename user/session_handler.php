<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    // Store date and time in session
    if (isset($data['date'])) {
        $_SESSION['selected_date'] = $data['date'];
    }
    if (isset($data['time'])) {
        $_SESSION['selected_time'] = $data['time'];
    }
    // Ensure appointment_id is set before returning it
    $appointmentId = $_SESSION['appointment_id'] ?? null;
    // Return the selected data as a JSON response
    echo json_encode([
        'date' => $_SESSION['selected_date'] ?? null,
        'time' => $_SESSION['selected_time'] ?? null,
        'appointment_id' => $appointmentId
    ]);
    exit;
}
// For non-POST requests, display the stored appointment_id
if (isset($_SESSION['appointment_id'])) {
    $appointmentId = $_SESSION['appointment_id'];
    echo "<h2>Selected Appointment ID: " . htmlspecialchars($appointmentId) . "</h2>";
} else {
    echo "<h2>No Appointment ID Selected</h2>";
}

?>
