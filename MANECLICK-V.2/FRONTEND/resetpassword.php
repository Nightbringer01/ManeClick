<?php
session_start();

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
                <h1>Change Password</h1>
                <input type="password" name="newPW" placeholder="Enter your new password">
                <input type="password" name="confirmPW" placeholder="Confirm new password">
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

                var resetToken = getUrlParameter('token'); // Retrieve resetToken from URL
                var newPW = $('input[name="newPW"]').val(); // Get the new password
                var confirmPW = $('input[name="confirmPW"]').val(); // Get the confirmation password

                // Send AJAX request
                $.ajax({
                    type: 'POST',
                    url: 'http://54.153.187.137/user/reset-password',
                    contentType: 'application/json', // Set content type to JSON
                    data: JSON.stringify({
                        resetToken: resetToken,
                        newPassword: newPW,
                        confirmNewPassword: confirmPW
                    }),
                    success: function(response) {
                        // Display success message
                        Swal.fire({
                            title: 'Success',
                            text: 'Password changed successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Redirect to some page after success if needed
                            window.location.href = 'index.php';
                        });
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while processing your request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });

        // Function to get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        };
    </script>


</body>

</html>