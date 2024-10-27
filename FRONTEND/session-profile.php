<?php
session_start(); // Start the session to manage user login state
include '../BACKEND/config/db.php';
// Check if the user is not logged in (no session exists)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'SLP') {
    header("Location: dashboard.php");
    exit;
}


$user_id = $_SESSION['user_id'];

$patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

$stmt = $conn->prepare("SELECT * FROM t_sessions WHERE patient_id = :patientId AND DATE(createdAt) = :date");
$stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
$stmt->bindParam(':date', $date, PDO::PARAM_STR);
$stmt->execute();
$existSession = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM therapy LEFT JOIN users ON therapy.user_id = users.id WHERE patient_id = :patientId");
$stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
$stmt->execute();
$therapyDets = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            background-color: #E4DEAE;
        }

        .cont-component {
            margin: 20px auto;
            border: 5px solid #79a18a;
            border-radius: .5rem;
            width: 70%;
            padding: 20px;
            background-color: white;
        }
        .card-body p{
            font-size: large;
        }
    </style>
</head>

<body>
    <div id="header">
        <?php include './component/pageHeader.php' ?>
    </div>
    <div class="cont-component">

        <div class="container w-[50%]">
            <div class="row">
                <div class="col-md-12">
                    <h3 style="color:#133A1B">Therapy Information</h3>
                    <div class="card mb-3">
                        <div class="card-body" style="display:flex; justify-content:space-around">
                            <div class="d-flex flex-column mb-2">
                                <strong>PID:</strong>
                                <p class="card-text"><?php echo $therapyDets['patient_id']; ?></p>
                            </div>
                            <div class="d-flex flex-column mb-2">
                                <strong>DSI:</strong>
                                <p class="card-text"><?php echo $therapyDets['DSI']; ?></p>
                            </div>
                            <div class="d-flex flex-column mb-2">
                                <strong>Patient Name:</strong>
                                <p class="card-text"><?php echo $therapyDets['name']; ?></p>
                            </div>
                            <div class="d-flex flex-column mb-2">
                                <strong>Sex:</strong>
                                <p class="card-text"><?php echo $therapyDets['sex']; ?></p>
                            </div>
                            <div class="d-flex flex-column mb-2">
                                <strong>SLP Name:</strong>
                                <p class="card-text"><?php echo $therapyDets['firstname']." ".$therapyDets['lastname']; ?></p>
                            </div>
                            <div class="d-flex flex-column mb-2">
                                <strong>Disorder:</strong>
                                <p class="card-text"><?php echo $therapyDets['disorders']; ?></p>
                            </div>

                            <div class="d-flex flex-column mb-2">
                                <strong>Therapy Status:</strong>
                                <p class="card-text"><?php echo $therapyDets['status']; ?></p>
                            </div>
                        </div>
                    </div>
                    <h3 style="color:#133A1B">Session Review ID <?php echo $existSession[0]['DSI']; ?></h3>
                    <?php foreach ($existSession as $session) : ?>
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Word Details</h5>
                                <p class="card-text"><strong>Word: </strong><?php echo $session['word']; ?></p>
                                <p class="card-text"><strong>Prompt: </strong><?php echo $session['prompt']; ?></p>
                                <p class="card-text"><strong>Interpretation: </strong><?php echo $session['interpretation']; ?></p>
                                <p class="card-text"><strong>Remarks: </strong><?php echo $session['remarks']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-core-api"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>


        </script>
</body>


</html>