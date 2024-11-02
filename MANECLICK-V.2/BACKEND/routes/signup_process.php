<?php
include '../config/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Ensure PHPMailer is loaded
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2');
$dotenv->load();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Fetch existing user with the provided username or email
        $existingUserStmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $existingUserStmt->bindParam(':username', $_POST['username']);
        $existingUserStmt->bindParam(':email', $_POST['email']);
        $existingUserStmt->execute();
        $existingUser = $existingUserStmt->fetch(PDO::FETCH_ASSOC);

        // If user or email already exists, return a 400 response
        if ($existingUser) {
            http_response_code(400);
            echo "Error: User or email already exists";
        } else {
            // User or email doesn't exist, proceed with insertion
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $birthdate = $_POST['birthdate'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $gender = $_POST['gender'];
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            $province = $_POST['selected_province'];
            $city = $_POST['selected_city'];
            $barangay = $_POST['selected_barangay'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = 'SLP';

            $birthdateDate = new DateTime($birthdate);
            $today = new DateTime();
            $today->setTime(0, 0, 0); 
            // Date validation
            if ($birthdateDate > $today) {
                http_response_code(203);
                echo json_encode(array("status" => 203, "message" => "Birthdate cannot be in the future."));
                exit; 
            }

            // Generate a unique verification token
            $token = bin2hex(random_bytes(16));

            // Prepare and execute the insertion query
            $insertStmt = $conn->prepare("INSERT INTO users (firstname, lastname, birthdate, address, phone, gender, email, username, password, role, province, city, barangay, verification_token) 
            VALUES (:firstname, :lastname, :birthdate, :address, :phone, :gender, :email, :username, :password, :role, :province, :city, :barangay, :token)");
            $insertStmt->bindParam(':firstname', $firstname);
            $insertStmt->bindParam(':lastname', $lastname);
            $insertStmt->bindParam(':birthdate', $birthdate);
            $insertStmt->bindParam(':address', $address);
            $insertStmt->bindParam(':phone', $phone);
            $insertStmt->bindParam(':gender', $gender);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':username', $username);
            $insertStmt->bindParam(':password', $hashedPassword);
            $insertStmt->bindParam(':role', $role);
            $insertStmt->bindParam(':province', $province);
            $insertStmt->bindParam(':city', $city);
            $insertStmt->bindParam(':barangay', $barangay);
            $insertStmt->bindParam(':token', $token); // Bind the token
            $insertStmt->execute();

            // Log the registration action
            $auditType = 'Register';
            $auditAction = 'New user registered, account name: '. $username;
            $auditRole = $role;

            $auditStmt = $conn->prepare("INSERT INTO audit_logs (type, action, role) VALUES (:type, :action, :role)");
            $auditStmt->bindParam(':type', $auditType);
            $auditStmt->bindParam(':action', $auditAction);
            $auditStmt->bindParam(':role', $auditRole);
            $auditStmt->execute();

            // Send verification email
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
                $mail->setFrom($_ENV['Email_Acc'], 'ManeClick');
                $mail->addAddress($email); // User's email

                // Create a verification link
                $verificationLink = "http://localhost/maneclick-v.2/BACKEND/routes/verify.php?token=$token";

                // Mail content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Verify Your Email Address';
                $mail->Body    = "Dear $firstname, <br> Please click the link below to verify your email address: <br><a href='$verificationLink'>$verificationLink</a>";

                // Send email
                if (!$mail->send()) {
                    http_response_code(500);
                    echo json_encode(['status' => 'fail', 'error' => $mail->ErrorInfo]);
                    exit;
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'fail', 'error' => $mail->ErrorInfo]);
                exit;
            }

            // Send a 200 response indicating success
            http_response_code(200);
            echo "New record created successfully, verification email sent.";
        }
    } catch (\Throwable $th) {
        // If an error occurs, send a 500 response with the error message
        http_response_code(500);
        echo "Error: " . $th->getMessage();
    }
}
?>
