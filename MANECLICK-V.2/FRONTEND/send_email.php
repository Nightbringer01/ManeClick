<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['id'];
    $email = $_POST['email'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['Email_Acc']; // SMTP username
        $mail->Password   = $_ENV['Email_pass']; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption type
        $mail->Port       = 587;

        // Sender and recipient settings
        $mail->setFrom('manetestacc123@gmail.com', 'ManeClick');
        $mail->addAddress($email); // Use the email from the AJAX request

        // Mail content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Important Patient Update';
        $mail->Body    = 'Dear Patient, this is an important update regarding your case. Please get in touch for more information.';

        // Send email
        if ($mail->send()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'fail']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'fail', 'error' => $mail->ErrorInfo]);
    }
}
?>
