<?php
session_start();

// Check if session exists
if(isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../FRONTEND/js/header.js"></script>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/modal.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .main-content {
            display: flex;
            align-items: center;
            background: linear-gradient(to bottom, #e8d8c3, #E4DEAE);
            justify-content: center;
            flex-grow: 1;
        }

        .text-container {
            text-align: center;
            margin-right: 20px;
            width: 40%;
        }

        .text-container h1 {
            font-size: 3rem;
        }

        .text-container p {
            font-size: 1.5rem;
        }

        .img-container img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div id="header">
        <?php include './component/header.php' ?>
    </div>
    <div class="main-content">
        <div class="text-container">
            <h1>Welcome to Mane Click</h1>
            <p>Track your patient’s speech therapy progress in just a few clicks!</p>
            <p>Log in now or sign up if you haven't already registered.</p>
        </div>
        <div class="img-container">
            <img src="./img/maneclick_logo.png" alt="Mane Click Logo">
        </div>
    </div>

    <div id="modal-container">
        <?php include './component/modalPlans.php'; ?>
    </div>

</body>

</html>