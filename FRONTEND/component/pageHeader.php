<!-- header.html -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maneclick</title>
    <style>
        .header-div {
            background-color: #415E35;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .header-div h1 {
            background-color: #2f603b;
            color: white;
            border: 1px solid black;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .nav-div ul {
            display: flex;
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .nav-div ul li {
            margin-left: 10px;
        }

        .nav-div ul li:first-child {
            margin-left: 0;
        }

        .nav-div ul li a {
            border: 1px solid #ffffff;
            border-radius: .5rem;
            background-color: #69aa85;
            padding: 5px 10px;
            margin-bottom: 5px;
            text-decoration: none;
            font-size: 24px;
            color: white;
        }

        .nav-div ul li a:hover {
            background-color: #79a18a;
        }

        /* User icon CSS */
        .user-icon {
            margin-right: 10px;
            cursor: pointer;
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            /* background-color: #69aa85; */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Dropdown CSS */
        .dropdown-menu {
            position: fixed;
            width: 5%;
            top: 60px;
            left: 90%;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            display: none;
            padding: 10px;
        }

        .dropdown-menu a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 5px 0;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <header class="header-div">
        <a href="index.php" style="text-decoration: none;">
            <h1>Mane Click</h1>
        </a>
        <div class="user-icon" onclick="toggleDropdown()">
            <img src="../../MANECLICK-V.2/FRONTEND/img/profile-user2.png" alt="User Icon" width="30" height="30">
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="#" id="logoutText">Logout</a>
            </div>
        </div>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleDropdown() {
            var dropdownMenu = document.getElementById("dropdownMenu");
            if (dropdownMenu.style.display === "none" || dropdownMenu.style.display === "") {
                dropdownMenu.style.display = "block";
            } else {
                dropdownMenu.style.display = "none";
            }
        }

        document.getElementById("logoutText").addEventListener("click", function() {
            fetch('../../../maneclick-v.2/BACKEND/routes/logout_process.php', {
                    method: 'POST'
                })
                .then(response => {
                    console.log(response)
                    if (response.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Logout Successful',
                            text: 'You have been logged out successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'login.php'; // Redirect to login page
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Logout Failed',
                            text: 'Failed to logout. Please try again later.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Logout Failed',
                        text: 'An error occurred while logging out. Please try again later.',
                    });
                });
        });
    </script>
</body>

</html>