<?php
session_start();
include '../config/db.php';

// Check if the patient ID is provided in the GET request
if (isset($_GET['patient_id'])) {
    try {
        // Sanitize the input to prevent SQL injection
        $patient_id = filter_var($_GET['patient_id'], FILTER_SANITIZE_NUMBER_INT);

        // Prepare and execute the query to fetch patient information
        $stmt = $conn->prepare("SELECT * FROM patients WHERE id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        // Fetch the patient information
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the patient exists
        if ($patient) {
            // Get patient's full name
            $patient_name = $patient['fname'] . ' ' . $patient['lname'];

            // Log the view action
            if (isset($_SESSION['username'])) {
                $type = 'View Patient';
                $action = $_SESSION['username'] . " viewed patient details for " . $patient_name;
                $role = $_SESSION['username'];

                $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
                $logStmt->bindParam(':type', $type);
                $logStmt->bindParam(':action', $action);
                $logStmt->bindParam(':role', $role);
                $logStmt->execute();
            }

            // Return patient information as JSON response
            header('Content-Type: application/json');
            echo json_encode($patient);
            exit;
        } else {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(array("status" => 500, "message" => "Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
?>
