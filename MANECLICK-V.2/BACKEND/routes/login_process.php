<?php
session_start(); // Start the session to manage user login state

include '../config/db.php'; // Include your database connection file
// include 'encryption_helper.php';

$response = array(); // Initialize an empty array for the response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to fetch user with the provided username from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['status'] == 0) {
            http_response_code(202); // Forbidden for users with status 0
            $response['success'] = false;
            $response['message'] = "User is inactive";
        } else {
            // Verify the password using password_verify
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['encrypt_key'] = $user['password'];

                // Set HTTP status code based on user role
                if ($_SESSION['role'] == 'admin') {
                    $type = 'login';
                    $action = $_SESSION['username'] . ' logged in to the website';
                    $role = $_SESSION['username'];

                    $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
                    $logStmt->bindParam(':type', $type);
                    $logStmt->bindParam(':action', $action);
                    $logStmt->bindParam(':role', $role);

                    if ($logStmt->execute()) {
                        http_response_code(200); // OK
                        $response['success'] = true;
                        $response['message'] = "Successfully logged in as SLP";
                    } else {
                        http_response_code(500); // Internal Server Error
                        $response['success'] = false;
                        $response['message'] = "Error logging in: " . $logStmt->errorInfo()[2];
                    }

                    $logStmt = null; // Close the statement
                    
                    http_response_code(200); // Admin role
                    $response['success'] = true;
                    $response['message'] = "Successfully logged in admin";
                } else if ($_SESSION['role'] == 'SLP') {
                    // Prepare and execute the audit log insertion
                    $type = 'login';
                    $action = $_SESSION['username'] . ' logged in to the website';
                    $role = $_SESSION['username'];

                    $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
                    $logStmt->bindParam(':type', $type);
                    $logStmt->bindParam(':action', $action);
                    $logStmt->bindParam(':role', $role);

                    if ($logStmt->execute()) {
                        http_response_code(200); // OK
                        $response['success'] = true;
                        $response['message'] = "Successfully logged in as SLP";
                    } else {
                        http_response_code(500); // Internal Server Error
                        $response['success'] = false;
                        $response['message'] = "Error logging in: " . $logStmt->errorInfo()[2];
                    }

                    $logStmt = null; // Close the statement
                }

            } else {
                // Password is incorrect
                http_response_code(400); // Bad Request
                $response['success'] = false;
                $response['message'] = "Invalid password";
            }
        }
    } else {
        // User does not exist with the provided username
        http_response_code(400); // Bad Request
        $response['success'] = false;
        $response['message'] = "User not found";
    }

    // Send JSON response
    echo json_encode($response);
    exit;
}
?>
