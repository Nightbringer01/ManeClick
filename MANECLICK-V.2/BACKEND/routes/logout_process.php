<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start(); // Start the session

    // Check if the user is logged in
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username']; 
        $role = $_SESSION['username']; 

        $type = 'logout';
        $action = $username . ' logged out of the website';

        $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
        $logStmt->bindParam(':type', $type);
        $logStmt->bindParam(':action', $action);
        $logStmt->bindParam(':role', $role);
        $logStmt->execute();

        // Unset and destroy the session
        session_unset();
        session_destroy();
    }

    // Send a 200 status code even if the user is not logged in
    http_response_code(200);
} else {
    // If the request method is not POST, respond with a 405 Method Not Allowed status code
    http_response_code(405);
}
