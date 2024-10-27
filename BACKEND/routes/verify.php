<?php
include '../config/db.php'; // Include your database connection

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Find the user with the matching token
        $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update the user to set is_verified = 1 and clear the verification token
            $updateStmt = $conn->prepare("UPDATE users SET is_verified = 'Yes', verification_token = NULL WHERE id = :id");
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            // Redirect to login.php with a success message
            header("Location: http://localhost/maneclick-v.2/FRONTEND/login.php?message=success");
            exit();
        } else {
            // If the token is invalid
            echo "Invalid verification token.";
        }
    } catch (PDOException $e) {
        // Handle any database errors
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no token is provided
    echo "No token provided.";
}
?>
