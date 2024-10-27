<?php
session_start(); // Start the session to manage user login state
include '../config/db.php';
include_once 'encryption.php';
$encryption_key = $_SESSION['encrypt_key'];

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401); // Unauthorized
    exit("Unauthorized");
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $id = $_POST['id'];
    $slp_id = $_SESSION['user_id'];
    $fname = encrypt($_POST['editfname'], $encryption_key);
    $lname = encrypt($_POST['editlname'], $encryption_key);
    $email = encrypt($_POST['editemail'], $encryption_key);
    $disorder = encrypt($_POST['editdisorder'], $encryption_key);
    $sex = encrypt($_POST['editsex'], $encryption_key);
    $birthdate = $_POST['editbirthdate'];
    $address = encrypt($_POST['editaddress'], $encryption_key);
    $guardian = encrypt($_POST['editguardian'], $encryption_key);
    $status = $_POST['editstatus'];

    // Retrieve province, city, and barangay values
    $province = encrypt($_POST['selected_province_name'], $encryption_key);
    $city = encrypt($_POST['selected_city_name'], $encryption_key);
    $barangay = encrypt($_POST['selected_barangay_name'], $encryption_key);

    // Fetch existing values from the database if new values are empty
    $existingStmt = $conn->prepare("SELECT province, city, barangay FROM patients WHERE id = :id");
    $existingStmt->bindParam(':id', $id);
    $existingStmt->execute();
    $existingValues = $existingStmt->fetch(PDO::FETCH_ASSOC);

    // Retain existing values if new ones are empty
    if (empty($province)) {
        $province = $existingValues['province'];
    }
    if (empty($city)) {
        $city = $existingValues['city'];
    }
    if (empty($barangay)) {
        $barangay = $existingValues['barangay'];
    }

    // Archive logic if status is set to 'inactive'
    if ($status == 'inactive') {
        $archiveStmt = $conn->prepare("
            INSERT INTO archive_p (id, slp_id, fname, lname, email, disorder, sex, birthdate, address, guardian, status, province, city, barangay)
            VALUES (:id, :slp_id, :firstname, :lastname, :email, :disorder, :sex, :birthdate, :address, :guardian, :status, :province, :city, :barangay)
        ");
        $archiveStmt->bindParam(':id', $id);
        $archiveStmt->bindParam(':slp_id', $slp_id);
        $archiveStmt->bindParam(':firstname', $fname);
        $archiveStmt->bindParam(':lastname', $lname);
        $archiveStmt->bindParam(':email', $email);
        $archiveStmt->bindParam(':disorder', $disorder);
        $archiveStmt->bindParam(':sex', $sex);
        $archiveStmt->bindParam(':birthdate', $birthdate);
        $archiveStmt->bindParam(':address', $address);
        $archiveStmt->bindParam(':guardian', $guardian);
        $archiveStmt->bindParam(':status', $status);
        $archiveStmt->bindParam(':province', $province);
        $archiveStmt->bindParam(':city', $city);
        $archiveStmt->bindParam(':barangay', $barangay);

        if (!$archiveStmt->execute()) {
            var_dump($archiveStmt->errorInfo());
            exit("Failed to archive patient details.");
        }

        // Log the archive action (add your logging logic here)
        // Example:
        // $logStmt = $conn->prepare("INSERT INTO audit_logs (action, user_id) VALUES (:action, :user_id)");
        // $logStmt->bindParam(':action', $action);
        // $logStmt->bindParam(':user_id', $_SESSION['user_id']);
        // $logStmt->execute();
    }

    // Update patient record in the database
    $updateSql = "
        UPDATE patients 
        SET 
            fname = :fname, 
            lname = :lname, 
            email = :email, 
            disorder = :disorder, 
            sex = :sex, 
            birthdate = :birthdate, 
            address = :address, 
            guardian = :guardian, 
            status = :status, 
            province = :province, 
            city = :city, 
            barangay = :barangay 
        WHERE id = :id
    ";

    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bindParam(':fname', $fname);
    $updateStmt->bindParam(':lname', $lname);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':disorder', $disorder);
    $updateStmt->bindParam(':sex', $sex);
    $updateStmt->bindParam(':birthdate', $birthdate);
    $updateStmt->bindParam(':address', $address);
    $updateStmt->bindParam(':guardian', $guardian);
    $updateStmt->bindParam(':status', $status);
    $updateStmt->bindParam(':province', $province);
    $updateStmt->bindParam(':city', $city);
    $updateStmt->bindParam(':barangay', $barangay);
    $updateStmt->bindParam(':id', $id);

    // Execute the update statement
    if (!$updateStmt->execute()) {
        var_dump($updateStmt->errorInfo());
        exit("Failed to update patient details.");
    }

    // Log the update action (add your logging logic here)
    // Example:
    // $logStmt = $conn->prepare("INSERT INTO audit_logs (action, user_id) VALUES (:action, :user_id)");
    // $logStmt->bindParam(':action', $action);
    // $logStmt->bindParam(':user_id', $_SESSION['user_id']);
    // $logStmt->execute();

    // Response after successful update
    http_response_code(200); // OK
    exit("Patient details updated successfully.");
} else {
    http_response_code(405); // Method Not Allowed
    exit("Method Not Allowed");
}
