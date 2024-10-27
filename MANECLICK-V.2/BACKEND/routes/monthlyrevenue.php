<?php
session_start();
include '../config/db.php';

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare and execute the query to fetch subscription counts and revenue per month
        $stmt = $conn->prepare("
            SELECT 
                DATE_FORMAT(createdAt, '%Y-%m') AS month, 
                SUM(plan_cost) AS count
            FROM 
                subscription
            GROUP BY 
                month
            ORDER BY 
                month ASC
        ");
        $stmt->execute();

        // Fetch the data
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data as a JSON response
        header('Content-Type: application/json');
        echo json_encode($subscriptions);
        exit;
    } catch (PDOException $e) {
        // Handle any errors that occur
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    exit;
}
?>
