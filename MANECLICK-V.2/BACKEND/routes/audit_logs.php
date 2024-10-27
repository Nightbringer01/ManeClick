<?php
include '../config/db.php';
session_start(); // Start the session to access session variables

// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $type = 'Generate Arima';
    $action = isset($_SESSION['username']) ? $_SESSION['username'] . ' generated an arima report for patient.' : 'Unknown user generated a report for patient.';
    $role = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';

    // Validate input
    if (empty($type) || empty($action)) {
        echo json_encode(["status" => "error", "message" => "Type and action are required"]);
        exit;
    }

    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");

        // Bind parameters
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':action', $action);
        $stmt->bindValue(':role', $role);

        // Execute statement
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Log entry created"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $stmt->errorInfo()[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }

    // Close connection (PDO does this automatically)
    $conn = null;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
