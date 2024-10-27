<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if the user is logged in
        if (!isset($_SESSION['username'])) {
            throw new Exception("User is not logged in");
        }

        function generateRandom12DigitNumber() {
            $part1 = str_pad(random_int(100000, 999999), 4, '0', STR_PAD_LEFT); 
            $part2 = str_pad(random_int(100000, 999999), 4, '0', STR_PAD_LEFT); 
            return $part1 . $part2; 
        }

        // Extract session number from the POST request
        $session_number = generateRandom12DigitNumber();
        $patient_id = $_POST['patient_id'];

        // Check if session already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) AS session_count FROM t_sessions WHERE patient_id = :patient_id AND session_number = :session_number");
        $checkStmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $checkStmt->bindParam(':session_number', $session_number, PDO::PARAM_INT);
        if (!$checkStmt->execute()) {
            throw new Exception("Failed to execute check query");
        }
        $rowChecker = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rowChecker === false) {
            throw new Exception("Failed to fetch data from check query");
        }

        if ($rowChecker['session_count'] > 0) {
            http_response_code(400);
            echo json_encode(array("status" => 400, "message" => "Therapy session already exists"));
            exit;
        }

        $selectStmt = $conn->prepare("SELECT * FROM therapy WHERE patient_id = :patient_id");
        $selectStmt->bindParam(':patient_id', $patient_id);
        if (!$selectStmt->execute()) {
            throw new Exception("Failed to execute select query");
        }
        $row = $selectStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) {
            throw new Exception("Failed to fetch data from select query");
        }

        $DSI = $row['DSI'] ;

        // Prepare the statement for inserting into t_sessions
        $insertStmt = $conn->prepare("INSERT INTO t_sessions (DSI, patient_id, session_number, word, prompt, interpretation, remarks) 
        VALUES (:DSI, :patient_id, :session_number, :word, :prompt, :interpretation, :remarks)");

        // Bind parameters
        $insertStmt->bindParam(':DSI', $DSI);
        $insertStmt->bindParam(':patient_id', $patient_id);
        $insertStmt->bindParam(':session_number', $session_number);
        $insertStmt->bindParam(':word', $word);
        $insertStmt->bindParam(':prompt', $prompt);
        $insertStmt->bindParam(':interpretation', $interpretation);
        $insertStmt->bindParam(':remarks', $remarks);

        // Loop through each word prompt input
        for ($i = 0; $i < count($_POST['word']); $i++) {
            // Retrieve input values
            $word = $_POST['word'][$i];
            $prompt = $_POST['prompt'][$i];
            $interpretation = $_POST['interpretation'][$i];
            $remarks = $_POST['remarks'][$i];

            // Execute the prepared statement
            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert session data");
            }
        }

        // Log the action
        $type = 'Add Session';
        $action = $_SESSION['username'] . " added a session to patient with DSI $DSI";
        $role = $_SESSION['username'];

        $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
        $logStmt->bindParam(':type', $type);
        $logStmt->bindParam(':action', $action);
        $logStmt->bindParam(':role', $role);
        if (!$logStmt->execute()) {
            throw new Exception("Failed to log action");
        }

        // Send a success response
        http_response_code(200);
        echo json_encode(array("status" => 200, "message" => "New session data added successfully"));
    } catch (Exception $e) {
        // Send an error response with the error message
        http_response_code(500);
        echo json_encode(array("status" => 500, "message" => "Error: " . $e->getMessage()));
    }
}
