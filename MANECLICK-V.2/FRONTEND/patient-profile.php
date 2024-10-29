<?php
session_start(); // Start the session to manage user login state
include '../BACKEND/config/db.php';
include_once '../BACKEND/routes/encryption.php';
$encryption_key = $_SESSION['encrypt_key'];

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

$patientId = isset($_GET['pid']) ? $_GET['pid'] : null;

$stmt = $conn->prepare("SELECT COUNT(*) FROM therapy WHERE patient_id = :patientId");
$stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
$stmt->execute();
// Fetch the count
$existTherapy = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = :patientId");
$stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
$stmt->execute();
$patientDetails = $stmt->fetch(PDO::FETCH_ASSOC);

$patientDetails['fname'] = decrypt($patientDetails['fname'], $encryption_key);
$patientDetails['lname'] = decrypt($patientDetails['lname'], $encryption_key);
$patientDetails['email'] = decrypt($patientDetails['email'], $encryption_key);
$patientDetails['disorder'] = decrypt($patientDetails['disorder'], $encryption_key);
$patientDetails['sex'] = decrypt($patientDetails['sex'], $encryption_key);
$patientDetails['address'] = decrypt($patientDetails['address'], $encryption_key);
$patientDetails['guardian'] = decrypt($patientDetails['guardian'], $encryption_key);
$patientDetails['province'] = decrypt($patientDetails['province'], $encryption_key);
$patientDetails['barangay'] = decrypt($patientDetails['barangay'], $encryption_key);
$patientDetails['city'] = decrypt($patientDetails['city'], $encryption_key);

$stmt = $conn->prepare("SELECT * FROM therapy WHERE patient_id = :patientId");
$stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
$stmt->execute();
$therapyDets = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$userDets = $stmt->fetch(PDO::FETCH_ASSOC);


$sessionstmt = $conn->prepare("SELECT patient_id, DSI, DATE_FORMAT(DATE_ADD(createdAt, INTERVAL 8 HOUR), '%Y-%m-%d') AS createdAt FROM t_sessions WHERE patient_id = :patientId");
$sessionstmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
$sessionstmt->execute();
$sessionsHistory = $sessionstmt->fetchAll(PDO::FETCH_ASSOC);

$distinctDates = array();

// Iterate through the fetched data to extract distinct dates
foreach ($sessionsHistory as $session) {
    $createdAt = $session['createdAt'];
    // Check if the date is not already in the distinctDates array
    if (!in_array($createdAt, $distinctDates)) {
        // Add the date to the distinctDates array
        $distinctDates[] = $createdAt;
    }
}

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
        #patientInfo p {
            padding: 10px;
            background-color: white;
            font-size: x-large;
            width: 90%;
            border: 2px solid #B7BF96;
        }

        #therapyHistory p {
            padding: 10px;
            background-color: white;
            font-size: x-large;
            width: 90%;
            border: 2px solid #B7BF96;
            text-decoration: underline;
            color: blue;
        }

        #therapyHistory p:hover {
            cursor: pointer;
        }

        #patientInfo h3 {
            padding: 10px;
            text-align: center;
            font-size: 23px;
            width: 100%;
            /* border: 4px solid #B7BF96; */
        }

        .btn {
            border: 4px solid white;
            border-radius: .5rem;
            color: white;
            background-color: #133A1B;
            font-size: xx-large;
            width: 75%;
        }

        .btn2 {
            font-size: x-large;
            width: 30%;
            height: 50px;
            background-color: #133A1B;
            border-radius: .5rem;
        }

        .btn2:hover {
            background-color: #2F603B;
        }

        .btn3 {
            font-size: large;
            width: 30%;
            height: 50px;
            background-color: #133A1B;
            border-radius: .5rem;
        }

        .btn3:hover {
            background-color: #2F603B;
        }

        .btn:hover {
            background-color: #415E35;
            color: white;
        }

        body {
            background-color: #E4DEAE;
        }

        .modal-dialog {
            border: 1px solid gray;
            width: 1000px;
        }

        .btn[disabled] {
            /* Apply your desired styles here */
            background-color: #ccc;
            /* Change background color to gray */
            color: #666;
            /* Change text color to a darker shade */
            cursor: not-allowed;
            /* Change cursor to indicate not clickable */
        }

        .gray-line {
            border-top: 1px solid #ccc;
        }

        .align-middle {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div id="header">
        <?php include './component/pageHeader.php' ?>
    </div>
    <div style="display:flex; padding:40px">
        <div class="d-flex flex-column" style="width:30%; align-items:center">
            <!-- Three buttons using Bootstrap classes -->
            <button type="button" class="btn mb-2" id="informationBtn">Information</button>
            <button type="button" class="btn mb-2" id="newTherapyBtn" <?php if ($existTherapy === "1")
                echo "disabled"; ?>>New Therapy</button>
            <button type="button" class="btn mb-2" id="addSessionBtn" <?php if ($existTherapy === "0")
                echo "disabled"; ?>>Add Session</button>
            <button type="button" class="btn mb-2" id="viewTherapyBtn" <?php if ($existTherapy === "0")
                echo "disabled"; ?>>View Therapy Details</button>
            <button type="button" class="btn mb-2" id="viewGenerateReportBtn" <?php if ($existTherapy === "0")
                echo "disabled"; ?>>Generate Report</button>
        </div>

        <div id="informationTab" style="display: flex; width:50%; border:4px solid #B7BF96">
            <div id="patientInfo"
                style="display:flex; flex-direction:column; background-color:white; padding:10px 5px; width:50%; justify-content:center; align-items: center;">

            </div>
            <div id="therapyHistory"
                style="display:flex; flex-direction:column; background-color:white; padding:10px 5px; width:50%;  align-items: center; text-align:center">
                <h3 style="margin-bottom: 15px; margin-top:5px;">Session History</h3>
                <p style="font-size:larger; "><?php
                // Define an array for month names
                $monthNames = array(
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December'
                );

                // Display the distinct dates
                foreach ($distinctDates as $date) {
                    // Convert the date string to a timestamp
                    $timestamp = strtotime($date);

                    // Extract components of the date
                    $day = date('d', $timestamp);
                    $month = date('n', $timestamp); // Numeric representation of the month
                    $year = date('Y', $timestamp);

                    $wordedDate = $monthNames[$month] . ' ' . $day . ', ' . $year;

                    // Output the worded date
                    echo "<a href='session-profile.php?patient_id=$patientId&date=$date'>$wordedDate</a><br><br>";
                }
                ?>
                </p>
            </div>
        </div>

        <div id="addSessionTab"
            style="display: none; width:50%; border:4px solid #B7BF96; background-color:whitesmoke;">
            <form id="sessionForm"
                style="display:flex; flex-direction:column; width:100%; padding: 10px; justify-content:center; align-items:center"
                method="post">
                <h4>Session Form</h4>
                <div class="form-group" style="width:50%">
                    <textarea class="form-control" placeholder="Goal for this session" id="goal" name="goal" rows="4"
                        required></textarea>
                </div>
                <div id="wordPrompts" style="width:80%">
                    <!-- This div will contain dynamically added word prompt sections -->
                </div>
                <div style="display:flex; flex-direction:row; justify-content: space-evenly; width:80%">
                    <button type="button" id="addWordPrompt" class="btn3 btn-primary">Add Word Prompt</button>
                    <button type="submit" class="btn3 btn-primary">Submit</button>
                </div>
            </form>
        </div>

        <div id="addTherapyTab" style="display: none; width:50%; border:4px solid #B7BF96; background-color:white;">
            <form id="therapyForm"
                style="display:flex; flex-direction:column; width:100%; padding: 10px; justify-content:center; align-items:center">
                <h4>THERAPY FORM</h4>
                <div style="display:flex; flex-direction: row; justify-content:space-evenly; width:100%">
                    <div style="width:48%;">
                        <div class="form-group">
                            <label for="name">Patient Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?php echo htmlspecialchars($patientDetails['fname']) . ' ' . htmlspecialchars($patientDetails['lname']); ?>"
                                readonly required>
                        </div>
                        <div class="form-group">
                            <label for="sex">Sex</label>
                            <!-- <select class="form-control" id="sex" name="sex" required> -->
                            <input type="text" class="form-control" id="sex" name="sex"
                                value="<?php echo htmlspecialchars($patientDetails['sex']); ?>" readonly required>
                            <!-- <option value="Male" <?php if ($patientDetails['sex'] === 'male')
                                echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if ($patientDetails['sex'] === 'female')
                                    echo 'selected'; ?>>Female</option> -->
                            <!-- </select> -->
                        </div>
                        <div class="form-group">
                            <label for="slp">Speech-Language Pathologist (SLP)</label>
                            <input type="text" class="form-control" id="slp" name="SLP"
                                value="<?php echo htmlspecialchars($userDets['firstname']) . ' ' . htmlspecialchars($userDets['lastname']); ?>"
                                readonly required>
                        </div>
                        <div class="form-group">
                            <label for="disorders">Disorders</label>
                            <input type="text" class="form-control" id="disorders" name="disorders"
                                value="<?php echo htmlspecialchars($patientDetails['disorder']); ?>" readonly required>
                        </div>
                    </div>
                    <div style="width:48%;">
                        <div class="form-group">
                            <label for="dateOfEvaluation">Date of Evaluation</label>
                            <input type="date" class="form-control" id="dateOfEvaluation" name="DOE" required>
                        </div>
                        <div class="form-group">
                            <label for="validUntil">Valid Until</label>
                            <input type="date" class="form-control" id="validUntil" name="valid_until" required>
                        </div>
                        <div class="form-group">
                            <label for="firstTherapyDate">First Therapy Date</label>
                            <input type="date" class="form-control" id="firstTherapyDate" name="FTD" required>
                        </div>
                        <div class="form-group">
                            <label for="targetFinishDate">Target Finish Date</label>
                            <input type="date" class="form-control" id="targetFinishDate" name="TFD" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn2 btn-primary">Submit</button>
            </form>
        </div>

        <div id="viewTherapyDetails"
            style="display: none; width:50%; border:4px solid #B7BF96; background-color:white;">
            <div class="card" style="border:1px solid black; width:100%">
                <div class="card-header bg-light">
                    <h5 class="card-title"
                        style="font-size: x-large; font-weight:800; color:#415E35; font-family: 'Roboto', sans-serif;">
                        Therapy Details <span><?php echo $therapyDets['DSI'] ?></span></h5>
                </div>
                <div class="card-body" style="font-size: x-large; font-family: 'Roboto', sans-serif;">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Patient
                                Name:</span></strong>
                        <span class="align-middle"><?php echo $therapyDets['name']; ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Sex:</span></strong>
                        <span class="align-middle"><?php echo $therapyDets['sex']; ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">SLP:</span></strong>
                        <span class="align-middle"><?php echo $therapyDets['SLP']; ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Disorder:</span></strong>
                        <span class="align-middle"><?php echo $therapyDets['disorders']; ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Created
                                At:</span></strong>
                        <span
                            class="align-middle"><?php echo date('F j, Y', strtotime($therapyDets['createdAt'])); ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Valid
                                Until:</span></strong>
                        <span
                            class="align-middle"><?php echo date('F j, Y', strtotime($therapyDets['valid_until'])); ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">First
                                Therapy Date:</span></strong>
                        <span class="align-middle"><?php echo date('F j, Y', strtotime($therapyDets['FTD'])); ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Target
                                Finish Date:</span></strong>
                        <span class="align-middle"><?php echo date('F j, Y', strtotime($therapyDets['TFD'])); ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Date
                                of Evaluation;:</span></strong>
                        <span class="align-middle"><?php echo date('F j, Y', strtotime($therapyDets['DOE'])); ?></span>
                    </p>
                    <hr class="gray-line">
                    <p style="border: 2px solid gray;">
                        <strong><span
                                style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px;">Status:</span></strong>
                        <span class="align-middle"><?php echo $therapyDets['status']; ?></span>
                    </p>
                </div>
            </div>
        </div>

        <div id="viewGenerateReport"
            style="display: none; width:50%; border:4px solid #B7BF96; background-color:white;">
            <div class="card" style="border:1px solid black; width:100%">
                <div class="card-header bg-light">
                    <h5 class="card-title"
                        style="font-size: x-large; font-weight:800; color:#415E35; font-family: 'Roboto', sans-serif;">
                        Generate Report </h5>
                </div>
                <div class="card-body">
                    <form id="arimaForm" method="get">
                        <button type="submit" class="btn3 btn-primary">GENERATE REPORT</button>
                    </form>
                </div>
                <!-- Empty div to hold the chart -->
                <div id="chartContainer">

                </div>
                <button id="printButton" style="display:none;" onclick="openPrintPage()">GO TO PRINT PAGE</button>



            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-core-api"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateOfEvaluation').setAttribute('min', today);
        document.getElementById('validUntil').setAttribute('min', today);
        document.getElementById('firstTherapyDate').setAttribute('min', today);
        document.getElementById('targetFinishDate').setAttribute('min', today);

        document.getElementById('sex').addEventListener('focus', function () {
            this.blur();
        });

        var patient_id = <?php echo json_encode($patientId); ?>;

        var logs = <?php echo json_encode($therapyDets); ?>

        function showAddTherapyTab() {
            document.getElementById("informationTab").style.display = "none";
            document.getElementById("addTherapyTab").style.display = "flex";
            document.getElementById("viewTherapyDetails").style.display = "none";
            document.getElementById("viewGenerateReport").style.display = "none";
            document.getElementById("addSessionTab").style.display = "none";
        }

        function showGenerateReport() {
            document.getElementById("informationTab").style.display = "none";
            document.getElementById("addTherapyTab").style.display = "none";
            document.getElementById("viewGenerateReport").style.display = "flex";
            document.getElementById("viewTherapyDetails").style.display = "none";
            document.getElementById("addSessionTab").style.display = "none";
        }

        // Function to show information tab and hide add therapy tab
        function showInformationTab() {
            document.getElementById("viewGenerateReport").style.display = "none";
            document.getElementById("addTherapyTab").style.display = "none";
            document.getElementById("informationTab").style.display = "flex";
            document.getElementById("viewTherapyDetails").style.display = "none";
            document.getElementById("addSessionTab").style.display = "none";
        }

        function showTherapyDetails() {
            document.getElementById("viewGenerateReport").style.display = "none";
            document.getElementById("addTherapyTab").style.display = "none";
            document.getElementById("informationTab").style.display = "none";
            document.getElementById("viewTherapyDetails").style.display = "flex";
            document.getElementById("addSessionTab").style.display = "none";
        }

        function showAddSession() {
            document.getElementById("viewGenerateReport").style.display = "none";
            document.getElementById("addSessionTab").style.display = "flex";
            document.getElementById("addTherapyTab").style.display = "none";
            document.getElementById("informationTab").style.display = "none";
            document.getElementById("viewTherapyDetails").style.display = "none";
        }



        // FOR VIEWING OF DIFFERENT TABS IN PROFILE
        document.getElementById("newTherapyBtn").addEventListener("click", function () {
            showAddTherapyTab(); // Call the function to show add therapy tab
        });

        document.getElementById("addSessionBtn").addEventListener("click", function () {
            showAddSession();
        });

        document.getElementById("viewTherapyBtn").addEventListener("click", function () {
            showTherapyDetails();
        });

        document.getElementById("informationBtn").addEventListener("click", function () {
            showInformationTab(); // Call the function to show information tab
        });

        document.getElementById("viewGenerateReportBtn").addEventListener("click", function () {
            showGenerateReport();
        });


        // fetch patient information
        $.ajax({
            url: '../BACKEND/routes/get_patient_profile.php',
            type: 'GET',
            data: {
                patient_id: patient_id
            },
            success: function (response) {
                var createdAt = new Date(response.created_at);
                createdAt.setHours(createdAt.getHours() + 8);
                var formattedDate = getWordedDate(createdAt);

                function getWordedDate(date) {
                    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    var month = months[date.getMonth()];
                    var day = date.getDate();
                    var year = date.getFullYear();
                    return month + " " + day + ", " + year;
                }

                // Handle successful response
                $('#patientInfo').html(`
                <h3>PATIENT INFORMATION</h3>
                <p> ${response.lname}</p>
                <p> ${response.fname}</p>
                <p> ${response.email}</p>
                <p>${response.sex}</p>
                <p>${response.address}</p>
                <p> ${response.birthdate}</p>
                <p> ${response.guardian}</p>
                <p>${formattedDate}</p>
            `);
            },
            error: function (xhr, status, error) {
                // Handle error
                console.error(xhr.responseText);
            }
        });

        $('#therapyForm').submit(function (event) {
            event.preventDefault(); // Prevent default form submission

            // Retrieve form data
            var formData = {
                name: $('#name').val(),
                sex: $('#sex').val(),
                SLP: $('#slp').val(),
                disorders: $('#disorders').val(),
                DOE: $('#dateOfEvaluation').val(),
                valid_until: $('#validUntil').val(),
                FTD: $('#firstTherapyDate').val(),
                TFD: $('#targetFinishDate').val(),
                patient_id: patient_id
            };

            // Send AJAX request
            $.ajax({
                url: '../BACKEND/routes/add_therapy_process.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response === "Therapy information inserted successfully.") {
                        // Therapy information inserted successfully
                        Swal.fire({
                            icon: 'success',
                            title: 'Therapy Started Successfully!',
                            text: 'You have successfully started a therapy.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function () {
                            window.location.reload()
                        })
                    } else {
                        // Therapy information insertion failed
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to start therapy. Please try again later.'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error inserting therapy information:', error);
                    // Error handling
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to start therapy. Please try again later.'
                    });
                }
            });
        });

        $(document).ready(function () {
            var maxWords = 10;
            var wordCount = 0;

            // Function to add a new word prompt section
            function addWordPrompt() {
                if (wordCount < maxWords) {
                    var wordPromptHtml = `
                    <div class="col-md-6">
                        <div class="word-prompt">
                            <div class="form-group">
                                <label for="word${wordCount}">Word ${wordCount + 1}</label>
                                <input type="text" class="form-control" id="word${wordCount}" name="word[]" required>
                            </div>
                            <div class="form-group">
                                <label for="prompt${wordCount}">Prompt</label>
                                <select class="form-control" id="prompt${wordCount}" name="prompt[]" required>
                                    <option value="">Select Prompt Type</option>
                                    <option value="Correct independent production">Correct independent production</option>
                                    <option value="Visual prompt">Visual prompt</option>
                                    <option value="Verbal prompt">Verbal prompt</option>
                                    <option value="Tactile prompt">Tactile prompt</option>
                                    <option value="Hand under hand assistance">Hand under hand assistance</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="interpretation${wordCount}">Interpretation</label>
                                <textarea class="form-control" id="interpretation${wordCount}" name="interpretation[]"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="remarks${wordCount}">Remarks</label>
                                <textarea class="form-control" id="remarks${wordCount}" name="remarks[]"></textarea>
                            </div>
                        </div>
                    </div>
                `;
                    if (wordCount % 2 === 0) {
                        $('#wordPrompts').append(`<div class="row"></div>`);
                    }
                    $('#wordPrompts .row:last-child').append(wordPromptHtml);
                    wordCount++;
                } else {
                    alert("You have reached the maximum number of word prompts.");
                }
            }

            // Event listener for the add word prompt button
            $('#addWordPrompt').click(function () {
                addWordPrompt();
            });

            // function handleFormSubmit(event) {
            //     event.preventDefault(); // Prevent the default form submission

            //     Swal.fire({
            //         title: 'Is the goal reached?',
            //         icon: 'question',
            //         showCancelButton: true,
            //         confirmButtonText: 'Yes',
            //         cancelButtonText: 'No'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             document.getElementById('sessionForm').submit(); // Submit the form if "Yes" is clicked
            //         }
            //     });
            // 
        });
        $(document).ready(function () {
            $('#sessionForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                Swal.fire({
                    title: 'Is the goal reached?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    var isGoalReached = result.isConfirmed ? 'yes' : 'no';

                    $.ajax({
                        type: 'POST',
                        url: '../BACKEND/routes/goal_process.php',
                        data: {
                            isGoalReached: isGoalReached,
                            goal: $('#goal').val(),
                            patient_id: patient_id
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                // Proceed with session data submission
                                var formData = $('#sessionForm').serialize();
                                formData += "&patient_id=" + patient_id;

                                $.ajax({
                                    type: 'POST',
                                    url: '../BACKEND/routes/add_session_process.php',
                                    data: formData,
                                    success: function (response) {
                                        var jsonResponse = JSON.parse(response);
                                        if (jsonResponse.status === 'success') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: jsonResponse.message,
                                            }).then(function () {
                                                window.location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'success',
                                                text: jsonResponse.message,
                                            })
                                                .then(function () {
                                                    window.location.reload();
                                                });
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'An unexpected error occurred.',
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: jsonResponse.message,
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An unexpected error occurred.',
                            });
                        }
                    });
                });
            });
        });
        $(document).ready(function () {
            let responseData; // Declare responseData at a higher scope

            $('#arimaForm').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Retrieve values of p, q, and d from the form inputs
                var p = 2
                var q = 1
                var d = 1

                // Retrieve patient_id from URL parameter pid
                var patient_id = getUrlParameter('pid');
                $.ajax({
                    type: 'POST',
                    url: '../BACKEND/routes/audit_logs.php',
                    success: function (response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            // alert('Log entry created successfully!');
                        } else {
                            // alert('Error: ' + result.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });

                // AJAX request
                $.ajax({
                    type: 'GET',
                    url: 'http://54.252.254.132:8080/api/arima',
                    contentType: 'application/json',
                    data: ({ // Convert data to JSON string
                        patient_id: patient_id,
                        p_value: p,
                        q_value: q,
                        d_value: d
                    }),
                    success: function (response) {

                        // Check if there is data available
                        if (Object.keys(response.Prompts).length > 0) {

                            // Clear the existing chart container
                            $('#chartContainer').empty();

                            // Assign response data to the higher scoped variable
                            responseData = response;

                            // Extracting dates and values from the response data
                            const dates = Object.keys(responseData.Prompts);
                            const values = Object.values(responseData.Prompts);

                            // Extract the predicted prompt value
                            const predictedPrompt = responseData.predicted_prompt;

                            const descriptions = {
                                100: "Showing Correct Independent Production",
                                80: "Visual Prompt",
                                60: "Verbal Prompt",
                                40: "Tactile Prompt",
                                20: "Hand under Hand Assistance"
                            };

                            // Create a new canvas element dynamically
                            const canvas = document.createElement('canvas');
                            canvas.id = 'myChart';
                            document.getElementById('chartContainer').appendChild(canvas); // Append canvas to the chartContainer div

                            // Create a new Chart instance
                            const ctx = document.getElementById('myChart').getContext('2d');
                            const myChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: [...dates, 'Predicted'],
                                    datasets: [{
                                        label: 'Prompt Values',
                                        data: [...values, predictedPrompt],
                                        backgroundColor: (context) => {
                                            // Set different colors for actual prompts and the predicted prompt
                                            return context.raw === predictedPrompt ? 'rgba(255, 99, 132, 0.2)' : 'rgba(75, 192, 192, 0.2)';
                                        },
                                        borderColor: (context) => {
                                            // Set different border colors for actual prompts and the predicted prompt
                                            return context.raw === predictedPrompt ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)';
                                        },
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: 'Prompts Average & Forecasted Value for Next Session'
                                        },
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    },
                                    tooltips: {
                                        callbacks: {
                                            title: function (tooltipItems) {
                                                return 'Predicted Value: ' + tooltipItems[0].label;
                                            },
                                            label: function (tooltipItem) {
                                                const predicted = tooltipItem.raw;
                                                return 'Description: ' + descriptions[predicted];
                                            }
                                        }
                                    }
                                }
                            });

                            // Show the chart container
                            document.getElementById('chartContainer').style.display = 'block';
                            document.getElementById('printButton').style.display = 'block';
                        } else {
                            // Display "No data available" message if no data is available
                            document.getElementById('chartContainer').innerHTML = '<p>No data available</p>';
                        }
                    },

                    error: function (xhr, status, error) {
                        // Handle error
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while processing your request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
            // Function to handle the print page navigation
            function openPrintPage() {
                const dates = Object.keys(responseData.Prompts);
                const values = Object.values(responseData.Prompts);
                const predictedPrompt = responseData.predicted_prompt;

                // Combine dates and values into a JSON string
                const chartData = JSON.stringify({ dates: [...dates, 'Predicted'], values: [...values, predictedPrompt] });

                // Store chart data in session storage
                sessionStorage.setItem('chartData', chartData);

                const patient_id = getUrlParameter('pid');
                window.location.href = 'print-profile.php?pid=' + patient_id;
            }
            $('#printButton').click(openPrintPage);
        })
        // Function to get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

    </script>

    <script>
        function confirmGoalReached() {
            return window.confirm("Is the goal reached?");
        }
    </script>
</body>


</html>