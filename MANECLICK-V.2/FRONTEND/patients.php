<?php
session_start(); // Start the session to manage user login state
include '../BACKEND/config/db.php';
include_once '../BACKEND/routes/encryption.php';
// Check if the user is not logged in (no session exists)
// need to check if encrypt_key is also set to decrypt patient information. 
if (!isset($_SESSION['username']) || !isset($_SESSION['encrypt_key'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'SLP') {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the user's ID exists in the subscription table
$stmt = $conn->prepare("SELECT * FROM subscription WHERE user_id = :user_id AND status = 1");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);

// If subscription doesn't exist
if (!$subscription) {
    header("Location: homepage.php");
    exit;
}

$selectedStatus = $_POST['patientStatus'] ?? 'active';

if ($selectedStatus === 'active') {
    // Select from patients table if status is active
    $stmt = $conn->prepare("SELECT * FROM patients WHERE slp_id = :user_id and status ='active'");
} else {
    // Select from archive_p table if status is inactive
    $stmt = $conn->prepare("SELECT * FROM archive_p WHERE slp_id = :user_id");
}

$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$filteredPatients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$userDets = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
</head>

<body>
    <div id="header">
        <?php include './component/pageHeader.php' ?>
    </div>
    <div style="padding:20px; background-color: #E4DEAE; font-family: Arial, sans-serif;">
        <div style="display: flex; flex-direction: row; align-items:center; justify-content:space-between">
            <h1>My Patients</h1>
            <div style="display:flex; flex-direction:row; align-items:center;">
                <form method="POST" action="" class="mb-3 mr-4">
                    <select class="form-control" id="patientStatusFilter" name="patientStatus"
                        onchange="this.form.submit()">
                        <option value="active" <?= ($selectedStatus === 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($selectedStatus === 'inactive') ? 'selected' : '' ?>>Archived
                        </option>
                    </select>
                </form>
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addPatientModal"
                    style="background-color:#133A1B">
                    Add Patient
                </button>

            </div>
        </div>

        <div class="table-responsive"
            style="width: 100%; max-height: 560px; overflow-y: auto; background-color:white; padding:10px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; border-radius:.5rem;">
            <table class="table table-bordered" id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Disorder</th>
                        <th>Sex</th>
                        <th>Birthdate</th>
                        <th>Province</th>
                        <th>City</th>
                        <th>Barangay</th>
                        <th>Address</th>
                        <th>Guardian</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($filteredPatients as $patient): ?>
                        <tr data-id="<?php echo $patient['id']; ?>">
                            <td><?php echo $patient['id'] ?></td>
                            <td><?php echo decrypt($patient['fname'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['lname'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['email'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['disorder'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['sex'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo $patient['birthdate'] ?></td>
                            <td><?php echo decrypt($patient['province'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['city'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['barangay'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['address'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo decrypt($patient['guardian'], $_SESSION['encrypt_key']) ?></td>
                            <td><?php echo $patient['status'] ?></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($patient['created_at'] . ' +8 hours')); ?></td>
                            <td>
                                <!-- Action icons -->
                                <a href="#" class="text-info mr-2" name="viewPatientBtn" id="viewPatientBtn"><i
                                        class="fas fa-eye"></i></a> <!-- View icon -->
                                <a href="#" id="editPatientBtn" name="editPatientBtn" class="text-primary editPatientBtn"
                                    data-id="<?php echo $patient['id']; ?>"><i class="fas fa-edit"></i></a>
                                <!-- Email icon -->
                                <a href="#" class="text-success sendEmailBtn" data-id="<?php echo $patient['id']; ?>"
                                    data-email="<?php echo $patient['email']; ?>"><i class="fas fa-envelope"></i></a>
                                <!-- Email icon -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

    <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPatientModalLabel">Edit Patient Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form to edit patient details -->
                    <form id="editPatientForm" method="post">
                        <!-- Hidden input field to store patient ID -->
                        <input type="hidden" id="editPatientId" name="id">
                        <!-- First Name -->
                        <div class="form-group">
                            <label for="editFirstName">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="editfname" readonly
                                required>
                        </div>
                        <!-- Last Name -->
                        <div class="form-group">
                            <label for="editLastName">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="editlname" readonly
                                required>
                        </div>
                        <!-- Email -->
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="editemail" required>
                        </div>
                        <!-- Disorder -->
                        <div class="form-group">
                            <label for="editDisorder">Disorder</label>
                            <input type="text" class="form-control" id="editDisorder" name="editdisorder" required>
                        </div>
                        <!-- Sex -->
                        <div class="form-group">
                            <label for="editSex">Sex</label>
                            <input type="text" class="form-control" id="editSex" name="editsex" readonly required>
                        </div>
                        <!-- Birthdate -->
                        <div class="form-group">
                            <label for="editBirthdate">Birthdate</label>
                            <input type="date" class="form-control" id="editBirthdate" name="editbirthdate" readonly
                                required>
                        </div>
                        <!-- Address -->
                        <div class="form-group">
                            <label for="province">Province</label>
                            <select id="editProvince" class="form-control" name="editProvince">
                                <option value="">Select Province</option>
                            </select>
                            <input type="hidden" id="selected_province_code" name="selected_province_code" value="">
                            <input type="hidden" id="selected_province_name" name="selected_province_name" value="">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <select id="editCity" class="form-control" name="editCity">
                                <option value="">Select City</option>
                            </select>
                            <input type="hidden" id="selected_city_code" name="selected_city_code" value="">
                            <input type="hidden" id="selected_city_name" name="selected_city_name" value="">
                        </div>
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <select id="editBarangay" class="form-control" name="editBarangay">
                                <option value="">Select Barangay</option>
                            </select>
                            <input type="hidden" id="selected_barangay_code" name="selected_barangay_code" value="">
                            <input type="hidden" id="selected_barangay_name" name="selected_barangay_name" value="">
                        </div>


                        <div class="form-group">
                            <label for="editAddress">Address</label>
                            <input type="text" class="form-control" id="editAddress" name="editaddress" required>
                        </div>
                        <!-- Guardian -->
                        <div class="form-group">
                            <label for="editGuardian">Guardian</label>
                            <input type="text" class="form-control" id="editGuardian" name="editguardian" required>
                        </div>
                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select class="form-control" id="editStatus" name="editstatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Archive</option>
                            </select>
                        </div>
                        <!-- Submit and close modal buttons -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPatientModalLabel">Add New Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form to add a new patient -->
                    <form id="addPatientForm" method="post">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lname" name="lname" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="disorder">Disorder</label>
                            <input type="text" class="form-control"
                                placeholder="Type all applicable disorder/s (eg. autism, ataxia)" id="disorder"
                                name="disorder" required>
                        </div>
                        <div class="form-group">
                            <label for="sex">Sex</label>
                            <select class="form-control" id="sex" name="sex" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                        </div>

                        <div class="form-group">
                            <label for="province">Province</label>
                            <select class="form-control" id="province" name="province" required>
                                <option value="">Select Province</option>
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <select class="form-control" id="city" name="city" required>
                                <option value="">Select City</option>
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="barangay">Barangay</label>
                            <select class="form-control" id="barangay" name="barangay" required>
                                <option value="">Select Barangay</option>
                                <!-- Options will be populated here -->
                            </select>
                        </div>

                        <input type="hidden" id="selected_province" name="selected_province" value="">
                        <input type="hidden" id="selected_city" name="selected_city" value="">
                        <input type="hidden" id="selected_barangay" name="selected_barangay" value="">
                        <div class="form-group">

                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>


                        <div class="form-group">
                            <label for="guardian">Guardian</label>
                            <input type="text" class="form-control" id="guardian" name="guardian" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" style="background-color:#133A1B">Save
                                changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>



    <script>
        $(document).ready(function () {
            // Fetch provinces from the API when the page loads
            $.ajax({
                url: 'https://psgc.gitlab.io/api/provinces/',
                method: 'GET',
                success: function (data) {


                    try {
                        // Check if data is a string and parse it
                        if (typeof data === 'string') {
                            data = JSON.parse(data);
                        }

                        if (Array.isArray(data)) {
                            var provinceDropdown = $('#province');
                            provinceDropdown.html('<option value="">Select Province</option>'); // Clear previous options

                            // Populate the provinces dropdown
                            data.forEach(function (province) {
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
                error: function (xhr, status, error) {
                    console.error("Error fetching provinces:", status, error, xhr.responseText);
                    alert("Error: Unable to fetch provinces. Please try again later.");
                }
            });

            // Fetch cities based on the selected province
            $('#province').on('change', function () {
                var provinceCode = $(this).val();
                if (provinceCode) {
                    $.ajax({
                        url: 'https://psgc.gitlab.io/api/provinces/' + provinceCode + '/cities-municipalities/',
                        method: 'GET',
                        success: function (data) {


                            try {
                                // Check if data is a string and parse it
                                if (typeof data === 'string') {
                                    data = JSON.parse(data);
                                }

                                if (Array.isArray(data)) {
                                    var cityDropdown = $('#city');
                                    cityDropdown.html('<option value="">Select City</option>'); // Clear previous options

                                    data.forEach(function (city) {
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
                        error: function (xhr, status, error) {
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
            $('#city').on('change', function () {
                var cityCode = $(this).val();
                if (cityCode) {
                    $.ajax({
                        url: 'https://psgc.gitlab.io/api/cities-municipalities/' + cityCode + '/barangays/',
                        method: 'GET',
                        success: function (data) {


                            try {
                                // Check if data is a string and parse it
                                if (typeof data === 'string') {
                                    data = JSON.parse(data);
                                }

                                if (Array.isArray(data)) {
                                    var barangayDropdown = $('#barangay');
                                    barangayDropdown.html('<option value="">Select Barangay</option>'); // Clear previous options

                                    data.forEach(function (barangay) {
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
                        error: function (xhr, status, error) {
                            console.error("Error fetching barangays:", status, error, xhr.responseText);
                            alert("Error: Unable to fetch barangays. Please try again later.");
                        }
                    });
                } else {
                    $('#barangay').html('<option value="">Select Barangay</option>');
                }
            });

            // Form submission
            $('#yourFormId').on('submit', function (e) {
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
                    success: function (response) {
                        // Handle success
                        console.log("Form submitted successfully:", response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error submitting form:", status, error);
                    }
                });
            });

            // Optional: If you want to store the selected values in hidden fields for additional use
            $('#province, #city, #barangay').on('change', function () {
                $('#selected_province').val($('#province option:selected').text());
                $('#selected_city').val($('#city option:selected').text());
                $('#selected_barangay').val($('#barangay option:selected').text());
            });
        });

    </script>



    <script>
        $(document).ready(function () {
            // Fetch provinces from the API when the page loads
            $.ajax({
                url: 'https://psgc.gitlab.io/api/provinces/',
                method: 'GET',
                success: function (data) {


                    try {
                        // Check if data is a string and parse it
                        if (typeof data === 'string') {
                            data = JSON.parse(data);
                        }

                        if (Array.isArray(data)) {
                            var provinceDropdown = $('#editProvince');
                            provinceDropdown.html('<option value="">Select Province</option>'); // Clear previous options

                            // Populate the provinces dropdown
                            data.forEach(function (province) {
                                provinceDropdown.append('<option value="' + province.name + '" data-code="' + province.code + '">' + province.name + '</option>');
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
                error: function (xhr, status, error) {
                    console.error("Error fetching provinces:", status, error, xhr.responseText);
                    alert("Error: Unable to fetch provinces. Please try again later.");
                }
            });

            // Fetch cities based on the selected province
            $('#editProvince').on('change', function () {
                var selectedProvinceName = $(this).val();
                var provinceCode = $(this).find('option:selected').data('code'); // Get the code from the selected option
                $('#selected_province_code').val(provinceCode); // Set hidden field for code
                $('#selected_province_name').val(selectedProvinceName); // Set hidden field for name

                if (selectedProvinceName) {
                    $.ajax({
                        url: 'https://psgc.gitlab.io/api/provinces/' + provinceCode + '/cities-municipalities/',
                        method: 'GET',
                        success: function (data) {


                            try {
                                // Check if data is a string and parse it
                                if (typeof data === 'string') {
                                    data = JSON.parse(data);
                                }

                                if (Array.isArray(data)) {
                                    var cityDropdown = $('#editCity');
                                    cityDropdown.html('<option value="">Select City</option>'); // Clear previous options

                                    data.forEach(function (city) {
                                        cityDropdown.append('<option value="' + city.name + '" data-code="' + city.code + '">' + city.name + '</option>');
                                    });

                                    $('#editBarangay').html('<option value="">Select Barangay</option>'); // Reset barangay options
                                } else {
                                    console.error("Cities data is not an array:", data);
                                    alert("Error: Unable to fetch cities. Please check the API response.");
                                }
                            } catch (e) {
                                console.error("Parsing error:", e);
                                alert("Error: Unable to parse cities data. Please check the API response.");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error fetching cities:", status, error, xhr.responseText);
                            alert("Error: Unable to fetch cities. Please try again later.");
                        }
                    });
                } else {
                    $('#editCity').html('<option value="">Select City</option>');
                    $('#selected_city_code').val(''); // Reset hidden field
                    $('#selected_city_name').val(''); // Reset hidden field for name
                    $('#editBarangay').html('<option value="">Select Barangay</option>');
                    $('#selected_barangay_code').val(''); // Reset hidden field
                }
            });

            // Fetch barangays based on the selected city
            $('#editCity').on('change', function () {
                var selectedCityName = $(this).val();
                var cityCode = $(this).find('option:selected').data('code'); // Get the code from the selected option
                $('#selected_city_code').val(cityCode); // Set hidden field for code
                $('#selected_city_name').val(selectedCityName); // Set hidden field for name

                if (selectedCityName) {
                    $.ajax({
                        url: 'https://psgc.gitlab.io/api/cities-municipalities/' + cityCode + '/barangays/',
                        method: 'GET',
                        success: function (data) {


                            try {
                                // Check if data is a string and parse it
                                if (typeof data === 'string') {
                                    data = JSON.parse(data);
                                }

                                if (Array.isArray(data)) {
                                    var barangayDropdown = $('#editBarangay');
                                    barangayDropdown.html('<option value="">Select Barangay</option>'); // Clear previous options

                                    data.forEach(function (barangay) {
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
                        error: function (xhr, status, error) {
                            console.error("Error fetching barangays:", status, error, xhr.responseText);
                            alert("Error: Unable to fetch barangays. Please try again later.");
                        }
                    });
                } else {
                    $('#editBarangay').html('<option value="">Select Barangay</option>');
                    $('#selected_barangay_code').val(''); // Reset hidden field
                    $('#selected_barangay_name').val(''); // Reset hidden field for name
                }
            });

            // Populate the hidden fields with the selected barangay's code when a barangay is selected
            $('#editBarangay').on('change', function () {
                var selectedBarangayName = $(this).val();
                $('#selected_barangay_name').val(selectedBarangayName); // Set hidden field for name
            });
        });

    </script>






    <script>
        $(document).ready(function () {
            $('#userTable').DataTable();
        });
        //add nyo lang to
        document.addEventListener('DOMContentLoaded', function () {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('birthdate').setAttribute('max', today);
        });
        //

        $(document).on("click", ".editPatientBtn", function () {
            // Retrieve data from the clicked row
            var patientId = $(this).data("id");
            var row = $(this).closest('tr');
            var firstName = row.find('td:nth-child(2)').text();
            var lastName = row.find('td:nth-child(3)').text();
            var email = row.find('td:nth-child(4)').text();
            var disorder = row.find('td:nth-child(5)').text();
            var sex = row.find('td:nth-child(6)').text();
            var birthdate = row.find('td:nth-child(7)').text();

            var province = row.find('td:nth-child(8)').text();
            var city = row.find('td:nth-child(9)').text();
            var barangay = row.find('td:nth-child(10)').text();

            var address = row.find('td:nth-child(11)').text();
            var guardian = row.find('td:nth-child(12)').text();
            var status = row.find('td:nth-child(13)').text();

            console.log(address);

            // Populate modal inputs with retrieved data
            $('#editPatientId').val(patientId);
            $('#editFirstName').val(firstName);
            $('#editLastName').val(lastName);
            $('#editEmail').val(email);
            $('#editDisorder').val(disorder);
            $('#editSex').val(sex);
            $('#editBirthdate').val(birthdate);

            $('#editAddress').val(address);
            $('#editGuardian').val(guardian);
            $('#editStatus').val(status);

            $('#selected_barangay_name').val(province);
            $('#selected_barangay_name').val(city);
            $('#selected_barangay_name').val(barangay);

            // Open the edit patient modal
            $('#editPatientModal').modal('show');
        });

        document.getElementById('patientStatusFilter').addEventListener('change', function () {
            this.form.submit();
        });

        $(document).on("click", "#viewPatientBtn", function (event) {
            event.preventDefault(); // Prevent default link behavior

            // Retrieve data from the clicked row
            var patientId = $(this).closest('tr').data("id");

            // // Set the patient ID in localStorage
            // localStorage.setItem('patientId', patientId);

            // Redirect to patient-profile.php
            window.location.href = 'patient-profile.php?pid=' + encodeURIComponent(patientId);
        });

        $(document).on("click", "#viewPatientBtn", function (event) {
            event.preventDefault(); // Prevent default link behavior

            // Retrieve data from the clicked row
            var patientId = $(this).closest('tr').data("id");

            // Redirect to session-profile.php
            window.location.href = 'patient-profile.php?pid=' + encodeURIComponent(patientId);
        });


        $('#editPatientForm').submit(function (event) {
            event.preventDefault(); // Prevent the default form submission

            // Serialize the form data
            var formData = $(this).serialize();
            console.log(formData)

            // Send a POST request using AJAX
            $.ajax({
                url: '../BACKEND/routes/update_patient_process.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    // Display success message using Swal.fire
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                    }).then(function () {
                        window.location.reload()
                    })
                },
                error: function (xhr, status, error) {
                    // Display error message using Swal.fire
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An error occurred while updating patient details',
                    });
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText); // Log full response for debugging
                }

            });
        });

        document.getElementById("addPatientForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent the default form submission

            var form = this; // Reference to the form element
            var formData = new FormData(form);

            // Send a POST request using fetch
            fetch('../BACKEND/routes/add_patient_process.php', {
                method: form.method,
                body: formData
            })
                .then(function (response) {
                    // Check if the response status is 200
                    if (response.status === 200) {
                        // Display success message using Swal.fire
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'New patient added successfully',
                        }).then(function () {
                            window.location.reload()
                        })
                    } else {
                        // Display error message using Swal.fire
                        //eto gawin nyong ganito oks
                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops...',
                            text: 'Birthdate cannot be in the future.',
                        });
                    }
                })
                .catch(function (error) {
                    // Handle errors
                    // Display error message using Swal.fire
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An error occurred while adding the patient',
                    });
                    console.error('Error:', error);
                });
        });

        $(document).on("click", ".delete-btn", function () {
            var patientId = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../BACKEND/routes/delete_patient_process.php',
                        method: 'POST',
                        data: {
                            id: patientId
                        },
                        success: function (response) {
                            // If the deletion is successful, remove the row from the table
                            if (response) {
                                Swal.fire(
                                    'Deleted!',
                                    'The patient has been deleted.',
                                    'success'
                                ).then(function () {
                                    window.location.reload()
                                })
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete the patient.',
                                    'error'
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'Failed to delete the patient.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>


    <script>
        $(document).ready(function () {
            $('.sendEmailBtn').on('click', function (e) {
                e.preventDefault();

                var patientId = $(this).data('id');
                var email = $(this).data('email');

                // Confirm action using SweetAlert
                Swal.fire({
                    title: 'Send Email?',
                    text: "Are you sure you want to send an email to this patient?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX request to send email
                        $.ajax({
                            url: 'send_email.php',
                            type: 'POST',
                            data: {
                                id: patientId,
                                email: email
                            },
                            success: function (response) {
                                Swal.fire(
                                    'Sent!',
                                    'Email has been sent successfully.',
                                    'success'
                                );
                            },
                            error: function () {
                                Swal.fire(
                                    'Failed!',
                                    'There was an error sending the email.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>