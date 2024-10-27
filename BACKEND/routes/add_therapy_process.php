<?php
session_start(); // Start the session to manage user login state
include '../config/db.php';


$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

function generateDSI()
{
    $dsi = '#';
    for ($i = 0; $i < 10; $i++) {
        $dsi .= mt_rand(0, 9); 
    }
    return $dsi;
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $SLP = $_POST['SLP'];
    $disorders = $_POST['disorders'];
    $DSI = generateDSI();
    $DOE = $_POST['DOE'];
    $valid_until = $_POST['valid_until'];
    $FTD = $_POST['FTD'];
    $TFD = $_POST['TFD'];
    $patient_id = $_POST['patient_id'];

    // Prepare SQL statement to insert data into therapy table
    $stmt = $conn->prepare("INSERT INTO therapy (patient_id, name, sex, SLP, disorders, DSI, DOE, valid_until, FTD, TFD,user_id) VALUES (:patient_id, :name, :sex, :SLP, :disorders, :DSI, :DOE, :valid_until, :FTD, :TFD, :user_id)");

    // Bind parameters
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':sex', $sex);
    $stmt->bindParam(':SLP', $SLP);
    $stmt->bindParam(':disorders', $disorders);
    $stmt->bindParam(':DSI', $DSI);
    $stmt->bindParam(':DOE', $DOE);
    $stmt->bindParam(':valid_until', $valid_until);
    $stmt->bindParam(':FTD', $FTD);
    $stmt->bindParam(':TFD', $TFD);
    $stmt->bindParam(':user_id', $user_id);
    // Execute the statement
    if ($stmt->execute()) {
        
        http_response_code(200);
        echo "Therapy information inserted successfully.";
        // Insert audit log
        $type = 'Add Therapy';
        $action = $_SESSION['username'] . ' added therapy for patient ' . $name . ' with DSI ' . $DSI;
        $role = $_SESSION['username'];

        $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
        $logStmt->bindParam(':type', $type);
        $logStmt->bindParam(':action', $action);
        $logStmt->bindParam(':role', $role);
        $logStmt->execute();

    } else {
        // Insertion failed
        echo "Error inserting therapy information.";
    }
} else {
    exit;
}
