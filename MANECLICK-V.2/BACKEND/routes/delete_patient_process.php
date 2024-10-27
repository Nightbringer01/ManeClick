<?php
// Include the necessary files and start the session if required
include '../config/db.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the 'id' parameter is set in the POST request
    if (isset($_POST['id'])) {
        // Sanitize the input to prevent SQL injection
        $patientId = htmlspecialchars($_POST['id']);

        // Prepare a DELETE statement to remove the patient record
        $stmt = $conn->prepare("DELETE FROM patients WHERE id = :id");

        // Bind the parameter
        $stmt->bindParam(':id', $patientId);

        // Attempt to execute the statement
        try {
            $stmt->execute();
            // Check if any rows were affected (i.e., if the deletion was successful)
            if ($stmt->rowCount() > 0) {
                // Return a success message
                echo json_encode(array("status" => 200, "message" => "Patient deleted successfully."));
            } else {
                // Return an error message if no rows were affected (patient not found)
                echo json_encode(array("status" => 404, "message" => "Patient not found."));
            }
        } catch (PDOException $e) {
            // Return an error message if an exception occurred
            echo json_encode(array("status" => 500, "message" => "Error: " . $e->getMessage()));
        }
    } else {
        // Return an error message if the 'id' parameter is not set
        echo json_encode(array("status" => 400, "message" => "Bad request: ID parameter is missing."));
    }
} else {
    // Return an error message if the request method is not POST
    echo json_encode(array("status" => 405, "message" => "Method Not Allowed: Only POST requests are allowed."));
}
