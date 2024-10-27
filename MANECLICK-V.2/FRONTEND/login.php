<?php
session_start();

// Check if session exists
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/login.css">
    <title>Document</title>

</head>

<body>
    <div id="header">
        <?php include './component/loginHeader.php' ?>
    </div>
    <div class="main-div">
        <div class="login-main">
            <form id="loginForm" method="post">
                <h1>Login</h1>
                <?php
$message = '';
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $message = 'Your email has been successfully verified! Wait for admin to approve your account.';
}

// Display the message if it exists
if ($message) {
    echo "<p style='color: green;'>$message</p>";
}
                ?>
                <input type="text" name="username" placeholder="Username">
                <br>
                <input type="password" name="password" placeholder="Password">
                <br>
                <button type="submit">Login</button>
            </form>
            <a href="forgot-pw.php">Forgot Password ?</a>
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    </div>



</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById("loginForm").addEventListener("submit", function(event) {
        event.preventDefault();
        var form = this;
        fetch('../BACKEND/routes/login_process.php', {
                method: form.method,
                body: new FormData(form)
            })
            .then(response => {
                if (response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'SLP Successfully logged in',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        window.location.href = 'homepage.php';
                    });
                }
                else if (response.status === 203) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Admin Successfully logged in',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        window.location.href = 'dashboard.php';
                    });
                } else if (response.status === 202) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please note that the verification process for the SLP will take approximately 1 hour.',
                    });
                } else {
                    // If login unsuccessful, show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid username or password',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing the request'
                });
            });
    });
</script>



</html>