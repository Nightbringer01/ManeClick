<?php
session_start();
include '../BACKEND/config/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'SLP') {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$email =$_SESSION['email'];
// Check subscription
$stmt = $conn->prepare("SELECT * FROM subscription WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subscription) {
    header("Location: getplans.php");
    exit;
}

$subscription_type = $subscription['type'];
$subscription_status = $subscription['status'];

// Fetch patient data
$stmt = $conn->prepare("SELECT COUNT(*) AS patient_count, DATE_FORMAT(created_at, '%D %M %Y') AS creation_date FROM patients WHERE slp_id = :slp_id GROUP BY DATE(created_at)");
$stmt->bindParam(':slp_id', $user_id);
$stmt->execute();
$patientsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch session data
$stmt = $conn->prepare("SELECT COUNT(*) AS session_count, MONTH(createdAt) AS month FROM t_sessions WHERE patient_id IN (SELECT id FROM patients WHERE slp_id = :slp_id) GROUP BY MONTH(createdAt)");
$stmt->bindParam(':slp_id', $user_id);
$stmt->execute();
$sessionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT COUNT(*) AS archive_patient_count, DATE_FORMAT(created_at, '%M %Y') AS archive_creation_month FROM archive_p WHERE slp_id = :slp_id GROUP BY DATE_FORMAT(created_at, '%Y-%m')");
$stmt->bindParam(':slp_id', $user_id);
$stmt->execute();
$archivePatientsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$archiveLabels = [];
$archiveCounts = [];
foreach ($archivePatientsData as $data) {
    $archiveLabels[] = $data['archive_creation_month'];
    $archiveCounts[] = $data['archive_patient_count'];
}

$patientLabels = [];
$patientCounts = [];
foreach ($patientsData as $data) {
    $patientLabels[] = $data['creation_date'];
    $patientCounts[] = $data['patient_count'];
}

// Prepare session data
$sessionLabels = [];
$sessionCounts = [];
for ($i = 1; $i <= 12; $i++) {
    $sessionLabels[] = date("F", mktime(0, 0, 0, $i, 1)); // Month names
    $sessionCounts[] = 0; // Initialize counts to 0
}
foreach ($sessionsData as $data) {
    $sessionCounts[$data['month'] - 1] = $data['session_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/index.css">
    <style>
        #container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
    </style>
</head>
<body>
    <div id="header">
        <?php include './component/pageHeader.php'; ?>
    </div>
    <div id="container">
        <div style="border:1px solid white; justify-content:center; padding:20px 15px; width:50%; background-color:white; box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px; border-radius:.5rem;">
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        </div>
        <div style="display: flex; width:50%; margin-top:10px; font-size:24px; font-weight:600;">
            <div style="display: flex; flex-direction:column; background-color:white; width:48%; margin-right:2%; padding:10px; box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px; border-radius:.5rem;">
                <text>Subscription: <?php echo $subscription_type; ?></text>
                <text>
                    Payment Status:
                    <span style="color: <?php echo ($subscription['status'] == 0) ? 'gray' : 'green'; ?>;">
                        <?php echo ($subscription['status'] == 0) ? 'Pending' : 'Accepted'; ?>
                    </span>
                </text>
                <?php
// Assuming $subscription['createdAt'] is in 'Y-m-d H:i:s' format
$createdAt = new DateTime($subscription['createdAt']);
$currentDate = new DateTime();

// Add 30 days to the subscription start date to get the expiration date
$expirationDate = clone $createdAt;
$expirationDate->modify('+30 days');

// Remove time portion for the date comparison
$currentDateOnly = $currentDate->format('Y-m-d');
$expirationDateOnly = $expirationDate->format('Y-m-d');

// Calculate the difference in days between the current date and the expiration date
$interval = (new DateTime($currentDateOnly))->diff(new DateTime($expirationDateOnly));


// If the subscription is already expired (current date is after the expiration date)
if ($currentDateOnly > $expirationDateOnly) {
    echo "<text style='color:red'>Subscription Expired</text>";

    $mail = new PHPMailer(true);
    
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
        $mail->Body = "Dear User, your subscription already expired. Please subscribe again to continue using this service.";
  
        if ($mail->send()) {
  
        } else {
           
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'fail', 'error' => $mail->ErrorInfo]);
    }

}
// If the subscription is within the last 7 days before expiration
elseif ($interval->days <= 7) {
    echo "<text>Subscription Notification: <span  style='color:red'>  {$interval->days} days remaining</span></text>";

  // Create a new PHPMailer instance
  $mail = new PHPMailer(true);
    
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
      $mail->Body = "Dear User, you have {$interval->days} days left remaining in your subscription. Please subscribe again to continue using this service.";

      if ($mail->send()) {

      } else {
         
      }
  } catch (Exception $e) {
      echo json_encode(['status' => 'fail', 'error' => $mail->ErrorInfo]);
  }

}
?>



                
            </div>
            <div style="background-color: white; width: 50%; padding: 10px; display: flex; flex-direction: row; align-items: center; box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px; border-radius:.5rem;">
                <?php if ($subscription['status'] == 1) { ?>
                    <button onclick="navigate()" style="width: 100%;  height:100%; padding: 10px; border: none; background-color: #133A1B; color: white; font-size: 16px; cursor: pointer; font-size:large; margin-right:1%">My Patients</button>
                <?php } else { ?>
                    <button onclick="showPendingMessage()" style="width: 100%;  height:100%; padding: 10px; border: none; background-color: #133A1B; color: white; font-size: 16px; cursor: pointer; font-size:large; margin-right:1%">My Patients</button>
                <?php } ?>
                <button onclick="navigateProfile()" style="width: 100%; height:100%; padding: 10px; border: none; background-color: #133A1B; color: white; font-size: 16px; cursor: pointer; font-size:large">Profile</button>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; width: 80%; margin-top: 20px; height: 50%">
            <div style="width: 32%; background-color: white; padding: 10px; box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px; border-radius:.5rem;">
                <canvas id="patientChart"></canvas>
            </div>
            <div style="width: 32%; background-color: white; padding: 10px; box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px; border-radius:.5rem;">
                <canvas id="archiveChart"></canvas>
            </div>
            <div style="width: 32%; background-color: white; padding: 10px; box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px; border-radius:.5rem;">
                <canvas id="sessionChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function navigate() {
            window.location.href = "patients.php";
        }

        function navigateProfile() {
            window.location.href = "slp-profile.php";
        }

        function showPendingMessage() {
            Swal.fire({
                title: 'Please wait for admin to confirm your subscription',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }

        // Data from PHP for patients
        const patientLabels = <?php echo json_encode($patientLabels); ?>;
        const patientData = <?php echo json_encode($patientCounts); ?>;

        // Data from PHP for sessions
        const sessionLabels = <?php echo json_encode($sessionLabels); ?>;
        const sessionData = <?php echo json_encode($sessionCounts); ?>;

        // Data from PHP for archived patients
        const archiveLabels = <?php echo json_encode($archiveLabels); ?>;
        const archiveData = <?php echo json_encode($archiveCounts); ?>;

        // Chart.js configuration for patients
        const ctxPatient = document.getElementById('patientChart').getContext('2d');
        const patientChart = new Chart(ctxPatient, {
            type: 'bar',
            data: {
                labels: patientLabels,
                datasets: [{
                    label: 'Number of Patients',
                    data: patientData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart.js configuration for sessions
        const ctxSession = document.getElementById('sessionChart').getContext('2d');
        const sessionChart = new Chart(ctxSession, {
            type: 'line',
            data: {
                labels: sessionLabels,
                datasets: [{
                    label: 'Number of Sessions',
                    data: sessionData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        const ctxArchive = document.getElementById('archiveChart').getContext('2d');
        const archiveChart = new Chart(ctxArchive, {
            type: 'pie', // or 'donut'
            data: {
                labels: archiveLabels,
                datasets: [{
                    label: 'Number of Archived Patients',
                    data: archiveData,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, 
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                // Label format
                                let label = context.label || '';
                                let value = context.raw; // raw data value
                                let total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                let percentage = ((value / total) * 100).toFixed(2);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Archived Patients',
                        position: 'top', 
                        font: {
                            size: 18, 
                            weight: 'bold' 
                        },
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            }
        });

    </script>
</body>
</html>
