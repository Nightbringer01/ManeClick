<?php
session_start();
// Check if the patientId is sent via POST
if (isset($_POST['patientId'])) {
    // Retrieve the patientId from the POST data
    $patientId = $_POST['patientId'];
    $_SESSION['patientId'] = $patientId;

    echo 'Patient ID received by PHP: ' . $patientId;
} else {
    // Handle case where patientId is not sent
    echo 'Error: Patient ID not received';
}
?>