<?php
session_start();
include '../config/db.php';

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve startDate and endDate from the query parameters
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

    if (!$startDate || !$endDate) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Missing startDate or endDate parameter']);
        exit;
    }

    try {
        // Prepare and execute the query to fetch counts per type within the date range
        $stmt = $conn->prepare("
            SELECT 
                type, 
                COUNT(*) AS count
            FROM 
                subscription
            WHERE 
                createdAt BETWEEN :startDate AND :endDate
            GROUP BY 
                type
            ORDER BY 
                type ASC
        ");

        // Bind the parameters to the query
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
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
