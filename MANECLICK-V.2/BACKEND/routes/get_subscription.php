<?php
include '../config/db.php';

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Query to fetch subscription information based on user ID
    $stmt = $conn->prepare("SELECT * FROM subscription WHERE user_id = :user_id order by createdAt desc");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return subscription information as JSON response
    echo json_encode($subscription);
} else {
    // Handle if user ID is not provided in the request
    echo json_encode(['error' => 'User ID not provided']);
}
