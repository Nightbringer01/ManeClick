<?php
session_start();
include '../config/db.php';

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve parameters from query string
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

    if (!$startDate || !$endDate) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Missing startDate or endDate parameter']);
        exit;
    }

    try {
        // Prepare and execute the query to fetch counts of distinct roles per day
        $stmt = $conn->prepare("
            SELECT 
                DATE(createdAt) AS date,
                COUNT(DISTINCT role) AS role_count
            FROM 
                audit_logs
            WHERE 
                type = 'login' AND
                createdAt BETWEEN :startDate AND :endDate
            GROUP BY 
                DATE(createdAt)
            ORDER BY 
                date ASC
        ");

        // Bind the parameters to the query
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        // Fetch the data
        $loginsPerDay = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data as a JSON response
        header('Content-Type: application/json');
        echo json_encode($loginsPerDay);
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
