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
    <!-- <link rel="stylesheet" href="./css/index.css"> -->
    <link rel="stylesheet" href="./css/signup.css">
    <title>Document</title>
</head>

<body style="background-color: #E4DEAE;">
    <div id="header">
        <?php include './component/loginHeader.php' ?>
    </div>

    <div class="main-content" style="display: flex;">
        <div class="img-container">
            <img src="./img/maneclick_logo.png" alt="Mane Click Logo">
        </div>
        <div class="signup-container" style="background-color: rgba(255, 255, 255, 0.5);">
            <div class="signup-main">
                <form method="post" id="signupForm">
                    <h1>Sign Up</h1>
                    <div class="input-row">
                        <input type="text" name="firstname" placeholder="First Name" required>
                        <input type="text" name="lastname" placeholder="Last Name" required>
                    </div>
                    <div class="input-row">
                        <input type="date" name="birthdate" placeholder="Birthdate" id="birthdate" required>
                        <input type="text" name="address" placeholder="Street Address" required>
                    </div>


                    <div class="input-row">
                        <select class="form-control" id="province" name="province" required>
                            <option value="">Select Province</option>
                            <!-- Options will be populated here -->
                        </select>

                        <select class="form-control" id="city" name="city" required>
                            <option value="">Select City</option>
                            <!-- Options will be populated here -->
                        </select>
                    </div>

                    <div class="input-row">
                    <select class="form-control" id="barangay" name="barangay" required>
            <option value="">Select Barangay</option>
            <!-- Options will be populated here -->
        </select>

        <input type="hidden" id="selected_province" name="selected_province" value="">
    <input type="hidden" id="selected_city" name="selected_city" value="">
    <input type="hidden" id="selected_barangay" name="selected_barangay" value="">

                    </div>





                    <div class="input-row">
                        <input type="text" name="phone" placeholder="Phone Number (e.g. 09123456789) " pattern="[0][9][0-9]{9}" required>
                        <select name="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="input-row">
                        <input type="text" name="email" placeholder="Email" required>
                        <input type="text" name="username" placeholder="Username" minlength="6" required >
                    </div>
                    <div class="input-row">
                        <input type="password" name="password" placeholder="Password" minlength="6" required >
                        <input type="password" name="cpassword" placeholder="Confirm Password" minlength="6" required>
                    </div>
                    <button type="submit">Next</button>
                </form>
                <p>Already have an account? <a href="login.php">Log in here</a></p>
            </div>
        </div>
    </div>
    <div class="submit-id" style="display: none;" id="submit-id">
        <div class="id-container">
            <form method="post" id="prcId">
                <h3 style="margin-bottom: 10px; text-align: center;">PLEASE SUBMIT YOUR PRC ID</h3>
                <input type="file" name="idFile" placeholder="Upload ID" accept=".jpg, .jpeg, .png" required>
     
                <input type="text" name="id-no" placeholder="ID NUMBER" minlength="7" maxlength="7" required>
                <p style="color: gray;">The id will be used by admin to verify your account.</p>
                <button type="submit">Sign up</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>



    <script>
        document.getElementById("signupForm").addEventListener("submit", function(event) {
            event.preventDefault();
            var form = this;
            fetch('../BACKEND/routes/signup_process.php', {
                    method: form.method,
                    body: new FormData(form)
                })
                .then(response => {
                    return response;
                })
                .then(response => {
                    if (response.status === 200) {
                        var username = form.querySelector('input[name="username"]').value;
                        sessionStorage.setItem('username', username);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Saved Successfully',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function() {
                            var submitId = document.querySelector(".submit-id");
                            var mainContent = document.querySelector(".main-content");
                            submitId.style.display = "flex";
                            mainContent.style.display = "none";
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'User already exist',
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

        const input = document.getElementById('birthdate');
        const today = new Date();
        const minDate = new Date(today.getFullYear() - 21, today.getMonth(), today.getDate());
        input.setAttribute('max', minDate.toISOString().split('T')[0]);

        document.getElementById("prcId").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission
    var form = this;
    var fileInput = form.querySelector('input[type="file"]'); // Get the file input
    var filePath = fileInput.value;
    
    // Check if the file has a valid extension
    var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    if (!allowedExtensions.exec(filePath)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Please upload a file with .jpg, .jpeg, or .png extension.'
        });
        fileInput.value = ''; // Clear the input
        return; // Stop the form submission
    }

    var username = sessionStorage.getItem('username'); // Retrieve username from sessionStorage
    var formData = new FormData(form);
    formData.append('username', username);

    fetch('../BACKEND/routes/prcId_process.php', {
        method: form.method,
        body: formData
    })
    .then(response => {
        if (response.status === 200) {
            // PRC ID submitted successfully
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'PRC ID submitted successfully. Please click the verification link sent to your email.',
                showConfirmButton: false,
                timer: 3000
            }).then(function() {
                window.location.href = 'index.php';
                sessionStorage.removeItem('username');
            });
        } else {
            // Error occurred during submission
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred during the submission'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Handle error
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred during the submission'
        });
    });
});

    </script>

<script>
 $(document).ready(function() {
    // Fetch provinces from the API when the page loads
    $.ajax({
        url: 'https://psgc.gitlab.io/api/provinces/',
        method: 'GET',
        success: function(data) {
            

            try {
                // Check if data is a string and parse it
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }

                if (Array.isArray(data)) {
                    var provinceDropdown = $('#province');
                    provinceDropdown.html('<option value="">Select Province</option>'); // Clear previous options

                    // Populate the provinces dropdown
                    data.forEach(function(province) {
                        provinceDropdown.append('<option value="' + province.code + '">' + province.name + '</option>');
                    });
                } else {
                    console.error("Provinces data is not an array:", data);
                    alert("Error: Unable to fetch provinces. Please check the API response.");
                }
            } catch (e) {
                console.error("Parsing error:", e);
                alert("Error: Unable to parse provinces data. Please check the API response.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching provinces:", status, error, xhr.responseText);
            alert("Error: Unable to fetch provinces. Please try again later.");
        }
    });

    // Fetch cities based on the selected province
    $('#province').on('change', function() {
        var provinceCode = $(this).val();
        if (provinceCode) {
            $.ajax({
                url: 'https://psgc.gitlab.io/api/provinces/' + provinceCode + '/cities-municipalities/',
                method: 'GET',
                success: function(data) {
    

                    try {
                        // Check if data is a string and parse it
                        if (typeof data === 'string') {
                            data = JSON.parse(data);
                        }

                        if (Array.isArray(data)) {
                            var cityDropdown = $('#city');
                            cityDropdown.html('<option value="">Select City</option>'); // Clear previous options

                            data.forEach(function(city) {
                                cityDropdown.append('<option value="' + city.code + '">' + city.name + '</option>');
                            });

                            $('#barangay').html('<option value="">Select Barangay</option>'); // Reset barangay options
                        } else {
                            console.error("Cities data is not an array:", data);
                            alert("Error: Unable to fetch cities. Please check the API response.");
                        }
                    } catch (e) {
                        console.error("Parsing error:", e);
                        alert("Error: Unable to parse cities data. Please check the API response.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching cities:", status, error, xhr.responseText);
                    alert("Error: Unable to fetch cities. Please try again later.");
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
            $('#barangay').html('<option value="">Select Barangay</option>');
        }
    });

    // Fetch barangays based on the selected city
    $('#city').on('change', function() {
        var cityCode = $(this).val();
        if (cityCode) {
            $.ajax({
                url: 'https://psgc.gitlab.io/api/cities-municipalities/' + cityCode + '/barangays/',
                method: 'GET',
                success: function(data) {
            

                    try {
                        // Check if data is a string and parse it
                        if (typeof data === 'string') {
                            data = JSON.parse(data);
                        }

                        if (Array.isArray(data)) {
                            var barangayDropdown = $('#barangay');
                            barangayDropdown.html('<option value="">Select Barangay</option>'); // Clear previous options

                            data.forEach(function(barangay) {
                                barangayDropdown.append('<option value="' + barangay.name + '">' + barangay.name + '</option>');
                            });
                        } else {
                            console.error("Barangays data is not an array:", data);
                            alert("Error: Unable to fetch barangays. Please check the API response.");
                        }
                    } catch (e) {
                        console.error("Parsing error:", e);
                        alert("Error: Unable to parse barangays data. Please check the API response.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching barangays:", status, error, xhr.responseText);
                    alert("Error: Unable to fetch barangays. Please try again later.");
                }
            });
        } else {
            $('#barangay').html('<option value="">Select Barangay</option>');
        }
    });

    // Form submission
    $('#yourFormId').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var form = this; // Reference to the form element
        var formData = new FormData(form);

        // Append selected names to FormData
        formData.append('province', $('#province option:selected').text());
        formData.append('city', $('#city option:selected').text());
        formData.append('barangay', $('#barangay option:selected').text());

        // Submit the form data using AJAX
        $.ajax({
            url: 'your_submission_url.php', // Replace with your actual URL
            method: 'POST',
            data: formData,
            processData: false, // Important
            contentType: false, // Important
            success: function(response) {
                // Handle success
                console.log("Form submitted successfully:", response);
            },
            error: function(xhr, status, error) {
                console.error("Error submitting form:", status, error);
            }
        });
    });

    // Optional: If you want to store the selected values in hidden fields for additional use
    $('#province, #city, #barangay').on('change', function() {
        $('#selected_province').val($('#province option:selected').text());
        $('#selected_city').val($('#city option:selected').text());
        $('#selected_barangay').val($('#barangay option:selected').text());
    });
});

   </script>


</body>

</html>