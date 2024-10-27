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
                <h1>Forgot Password</h1>
                <input type="text" name="email" placeholder="Enter the email you've registered.">
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                var email = $('input[name="email"]').val(); // Get the email input value

                // AJAX request
                $.ajax({
                    type: 'POST',
                    // URL NEEDS TO BE CHANGE
                    url: 'http://54.153.187.137/user/forgot',
                    contentType: 'application/json',
                    data: JSON.stringify({ email: email }),
                    success: function(response) {
                        // Display success message or handle response from backend
                        Swal.fire({
                            title: 'Email Sent!',
                            text: "Please check the link that has been sent to your gmail.",
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'index.php';
                        });
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing your request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>


</body>





</html>