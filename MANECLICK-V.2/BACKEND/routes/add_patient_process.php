<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if the user is logged in
        if (!isset($_SESSION['username'])) {
            throw new Exception("User is not logged in");
        }

        // Get the SLP's user_id from the session
        $slp_id = $_SESSION['user_id'];

        // Extracting data from POST request
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $disorder = $_POST['disorder'];
        $sex = $_POST['sex'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];

        $province = $_POST['selected_province'];
        $city = $_POST['selected_city'];
        $barangay = $_POST['selected_barangay'];

        $guardian = $_POST['guardian'];

        $birthdateDate = new DateTime($birthdate);
        $today = new DateTime();
        $today->setTime(0, 0, 0); 
        // date validation
        if ($birthdateDate > $today) {
            http_response_code(203);
            echo json_encode(array("status" => 203, "message" => "Birthdate cannot be in the future."));
            exit; 
        }
    
        // Prepare and execute the insertion query
        $insertStmt = $conn->prepare("INSERT INTO patients (slp_id, fname, lname, email, disorder, sex, birthdate, address, guardian,province,city,barangay) 
        VALUES (:slp_id, :fname, :lname, :email, :disorder, :sex, :birthdate, :address, :guardian,:province,:city,:barangay)");
        $insertStmt->bindParam(':slp_id', $slp_id);
        $insertStmt->bindParam(':fname', $fname);
        $insertStmt->bindParam(':lname', $lname);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':disorder', $disorder);
        $insertStmt->bindParam(':sex', $sex);
        $insertStmt->bindParam(':birthdate', $birthdate);
        $insertStmt->bindParam(':address', $address);
        $insertStmt->bindParam(':province', $province);
        $insertStmt->bindParam(':city', $city);
        $insertStmt->bindParam(':barangay', $barangay);
        $insertStmt->bindParam(':guardian', $guardian);
        
        if ($insertStmt->execute()) {
            $type = 'Add Patient';
            $action = $_SESSION['username'] . ' added a patient named ' . $fname . ' ' . $lname;
            $role = $_SESSION['username'];

            $logStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
            $logStmt->bindParam(':type', $type);
            $logStmt->bindParam(':action', $action);
            $logStmt->bindParam(':role', $role);
            $logStmt->execute();
        }

        // Send a success response
        http_response_code(200);
        echo json_encode(array("status" => 200, "message" => "New patient added successfully"));
    } catch (Exception $e) {
        // Send an error response with the error message
        http_response_code(500);
        echo json_encode(array("status" => 500, "message" => "Error: " . $e->getMessage()));
    }
}
