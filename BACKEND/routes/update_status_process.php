<?php
session_start(); // Start the session to manage user login state

include '../config/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the logged-in user is an admin
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        $response['success'] = false;
        $response['message'] = "You don't have permission to perform this action.";
        echo json_encode($response);
        exit;
    }

    $userId = $_POST['user_id'];
    $newStatus = $_POST['new_status'];
    $email = $_POST['email'];


    $stmtUsers = $conn->prepare("UPDATE users SET status = :status WHERE id = :id");
    // $stmtSubscription = $conn->prepare("UPDATE subscription SET status = :status WHERE user_id = :id");

    $stmtUsers->bindParam(':status', $newStatus);
    $stmtUsers->bindParam(':id', $userId);
    // $stmtSubscription->bindParam(':status', $newStatus);
    // $stmtSubscription->bindParam(':id', $userId);

    $conn->beginTransaction();

    // Execute the updates
    $stmtUsers->execute();
    // $stmtSubscription->execute();

    // Check if both updates were successful
    if ($stmtUsers->rowCount() > 0 ) {
        $conn->commit();
        http_response_code(200); // Success

        // mail
        $mail = new PHPMailer(true);

        if($newStatus == 1){
            $message = "Your account has been approved, you can now proceed to login";
        }else{
            $message = "Your account has been rejected, try to signup again and make sure you provide correct details";
        }
    
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'masukista001@gmail.com'; // SMTP username
            $mail->Password   = 'rnsfukcsbvqcdeqv'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
            $mail->Port       = 587;
    
            // Sender and recipient settings
            $mail->setFrom('masukista001@gmail.com', 'ManeClick');
            $mail->addAddress($email); // Use the email from the AJAX request
    
            // Mail content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Important Patient Update';
            $mail->Body    = $message ;
    
            // Send email
            if ($mail->send()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'fail']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'fail', 'error' => $mail->ErrorInfo]);
        }







        $response['success'] = true;
        $response['message'] = "User status updated successfully.";
    } else {
        $conn->rollback();
        http_response_code(400); // Bad Request
        $response['success'] = false;
        $response['message'] = "Failed to update user status.";
    }

    // Send JSON response
    echo json_encode($response);
    exit;
} else {
    http_response_code(405); // Method Not Allowed
    $response['success'] = false;
    $response['message'] = "Method not allowed.";
    echo json_encode($response);
    exit;
}
