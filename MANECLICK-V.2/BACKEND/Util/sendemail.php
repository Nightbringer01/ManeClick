<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start(); // Start the session to manage user login state

require_once $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/vendor/autoload.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/BACKEND/config/db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/BACKEND/routes/encryption.php';

function SendEmailAttachment($email, $path)
{
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'masukista001@gmail.com'; // SMTP username
        $mail->Password = 'rnsfukcsbvqcdeqv'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('masukista001@gmail.com', 'ManeClick');
        $mail->addAddress($email); // Use the email from the AJAX request

        // Mail content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Therapy reports';
        $mail->Body = 'Dear Patient, this is your therapy Report.';
        $mail->AddAttachment($path);

        // Send email
        if ($mail->send()) {
            http_response_code(200);
            echo json_encode('Email Sent');
            return;
        } else {
            http_response_code(500);
            echo json_encode(['status' => 500]);
            return;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 500, 'error' => $mail->ErrorInfo]);
        return;
    }

}

if (!isset($_POST['data']) | !isset($_POST['pid'])) {
    http_response_code(500);
    echo json_encode(['status' => 500, 'error' => "Data Lacks"]);
    exit();
}

try {


    $data = $_POST['data'];
    $pid = $_POST['pid'];
    $b64 = $data;

    $sessionstmt = $conn->prepare("SELECT email FROM patients WHERE id = :patientId");
    $sessionstmt->bindParam(':patientId', $pid, PDO::PARAM_INT);
    $sessionstmt->execute();
    $patientemail = $sessionstmt->fetchColumn();

    $patientemail = decrypt($patientemail, $_SESSION['encrypt_key']);

    # Decode the Base64 string, making sure that it contains only valid characters
    $bin = base64_decode($b64, true);

    # Perform a basic validation to make sure that the result is a valid PDF file
    # Be aware! The magic number (file signature) is not 100% reliable solution to validate PDF files
    # Moreover, if you get Base64 from an untrusted source, you must sanitize the PDF contents
    if (strpos($bin, '%PDF') !== 0) {
        throw new Exception('Missing the PDF file signature');
    }

    $path = $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/BACKEND/filestore/TherapyReport.pdf';

    # Write the PDF contents to a local file
    file_put_contents($path, $bin);

    SendEmailAttachment($patientemail, $path);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['status' => 500, 'error' => $ex->getMessage()]);
    exit();
}