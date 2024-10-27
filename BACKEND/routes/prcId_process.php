<?php
include '../config/db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_FILES['idFile']) && isset($_POST['id-no']) && isset($_POST['username'])) {
        // Process the received data
        $username = $_POST['username'];
        $idNumber = $_POST['id-no'];

        // Handle the uploaded file
        $file = $_FILES['idFile'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];

        // Check for upload errors
        if ($fileError === UPLOAD_ERR_OK) {
            // Specify the directory to which the file will be uploaded
            $uploadDir = '../uploads/';
            // Generate a unique filename to prevent overwriting existing files
            $uniqueFileName = uniqid('prc_id_') . '_' . $fileName;
            // Create the destination path
            $destination = $uploadDir . $uniqueFileName;
            // Move the uploaded file to the destination directory
            if (move_uploaded_file($fileTmpName, $destination)) {

                // Example update user's profile:
                $updateSql = "UPDATE users SET prc_id = :filePath, prc_id_no = :idNumber WHERE username = :username";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->execute(['filePath' => "/MANECLICK-V.2/BACKEND/uploads/" . $uniqueFileName, 'idNumber' => $idNumber, 'username' => $username]);
                // Respond with success status
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'PRC ID submitted successfully']);
                exit;
            } else {
                // Error occurred while moving the file
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Error occurred while uploading the file']);
                exit;
            }
        } else {
            // Error occurred during file upload
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Error occurred during file upload']);
            exit;
        }
    } else {
        // Required fields are missing
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All required fields are not provided']);
        exit;
    }
} else {
    // Request method is not POST
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
