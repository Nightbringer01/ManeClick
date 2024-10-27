<?php
session_start(); // Start the session to manage user login state
include '../BACKEND/config/db.php';

// Check if the user is not logged in (no session exists)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is not an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: homepage.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'SLP'");
$stmt->execute();
$slpUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM patients");
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$todayDateTime = date('Y-m-d H:i:s');

$todayDate = date('Y-m-d', strtotime($todayDateTime));
$this_month = date('Y-m');

// Query to count the rows in the users table where createdAt date is today
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE DATE(createdAt) = :today_date");
$stmt->bindParam(':today_date', $todayDate);
$stmt->execute();
$userCountToday = $stmt->fetchColumn();

// Query to count the rows in the patients table where createdAt date is today
$stmt = $conn->prepare("SELECT COUNT(*) FROM patients WHERE DATE(created_at) = :today_date");
$stmt->bindParam(':today_date', $todayDate);
$stmt->execute();
$patientCountToday = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM subscription WHERE DATE_FORMAT(createdAt, '%Y-%m') = :this_month");
$stmt->bindParam(':this_month', $this_month);
$stmt->execute();
$subcriptionCount = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT SUM(plan_cost) FROM subscription WHERE DATE_FORMAT(createdAt, '%Y-%m') = :this_month");
$stmt->bindParam(':this_month', $this_month);
$stmt->execute();
$subscriptionSum = $stmt->fetchColumn();

$todayDate = date('Y-m-d');
date_default_timezone_set('Asia/Manila');

try {
    // Prepare the SQL statement
    $stmt = $conn->prepare("
        SELECT 
            action, 
            createdAt AS date
        FROM 
            audit_logs
        WHERE 
            DATE(createdAt) = :todayDate
        ORDER BY 
            createdAt DESC
    ");

    // Bind the parameter to the query
    $stmt->bindParam(':todayDate', $todayDate);

    // Execute the statement
    $stmt->execute();

    // Fetch all results
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format date and time
    foreach ($logs as &$log) {
        $dateTime = new DateTime($log['date']);
        $dateTime->setTimezone(new DateTimeZone('Asia/Manila')); 
        $dateTime->add(new DateInterval('PT8H'));
        $log['date'] = $dateTime->format('Y-m-d h:i:s A'); 
    }
    
} catch (PDOException $e) {
    // Handle any errors that occur
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

?>


<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
</head>

    <style>
    .bg{

    }
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: green;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }
    </style>

<body>
    <div id="header">
        <?php include './component/pageHeader.php' ?>
    </div>

    <div id="loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.7); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
            <img src="../FRONTEND/img/load.gif" alt="Loader" style="width:20px; height:20px">
            <p>Loading...</p>
        </div>
    </div>
    <div style="padding:20px;background: linear-gradient(135deg, #E4DEAE, #F5F5F5); font-family: Arial, sans-serif;">
        <div style="display: flex; justify-content:space-between; margin-bottom:10px;">
            <h1>Welcome, Admin</h1>
            <label class="switch">
                <input type="checkbox" id="toggleView">
                <span class="slider round"></span>
            </label>
        </div>
        <div id="infoSection">
            <div style="display: flex; margin-top: 1%; justify-content: space-between;">
                <div style="color: white; border: 1px solid white; padding: 10px; width: 30%;  margin-left: 5%; border-radius:.5rem; background-color:#B7BF96; box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset;">
                    <h2 style="font-weight:700; color:white; text-align: center;">Daily User Report</h2>
                    <div style="display: flex; color:#133A1B;">
                        <div style="display: flex; justify-content: space-between; width:90%;">
                            <div>
                                <div style="font-size: 20px; padding: 10px; margin-bottom: 10px;">New Registered SLP</div>
                                <div style="font-size: 20px; padding: 10px; ">New Registered Patient</div>
                            </div>
                            <div>
                                <div style="font-size: 20px; padding: 10px; background-color: white; justify-content: center; text-align: center; margin-bottom: 10px; border-radius:.2rem;"><?php echo $userCountToday; ?></div>
                                <div style="font-size: 20px; padding: 10px; background-color: white; justify-content: center; text-align: center; border-radius:.2rem;"><?php echo $patientCountToday; ?></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div style="color:white; border: 1px solid white; padding: 10px; width: 30%; margin-left: 5%; border-radius:.5rem; background-color:#133A1B; box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset;">
                    <h2 style="font-weight:700; color:white; text-align: center;">Monthly Plans Availed</h2>
                    <div style="display: flex;">
                        <div style="display: flex; justify-content: space-between; width:90%;">
                            <div>
                                <div style="font-size: 20px; padding: 10px; margin-bottom: 10px;">No. of Subscription Availed</div>
                                <div style="font-size: 20px; padding: 10px; ">Total Subscription</div>
                            </div>
                            <div style="color: #133A1B;">
                                <div style="font-size: 20px; padding: 10px; background-color: white; justify-content: center; text-align: center; margin-bottom: 10px; border-radius:.2rem;"><?php echo $subcriptionCount; ?></div>
                                <div style="font-size: 20px; padding: 10px; background-color: white; justify-content: center; text-align: center; border-radius:.2rem;">₱ <?php echo $subscriptionSum; ?></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div style="color: white; border: 1px solid white; padding: 10px; max-height: 200px; width: 40%; margin-left: 5%; border-radius: .5rem; background-color: #B7BF96; box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset; overflow-y: auto;">
                    <h2 style="font-weight: 700; color: white; text-align: center;">Today's Audit Logs</h2>
                    <div style="display: flex; flex-direction: column; color: #133A1B;">
                        <!-- Loop through the $logs array to display actions and dates -->
                        <?php if (is_array($logs) && !empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <div style="display: flex; justify-content: space-between; width: 100%; margin-bottom: 10px; border-bottom: 1px solid #ccc;">
                                    <div style="font-size: 15px; padding: 5px; background-color: white; text-align: justify; border-radius: .2rem; width:70%;">
                                        <?php echo htmlspecialchars($log['action']); ?>
                                    </div>
                                    <div style="font-size: 12px; padding: 5px; background-color: white; text-align: center; border-radius: .2rem; width:30%;">
                                        <?php echo htmlspecialchars($log['date']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="font-size: 20px; padding: 10px; background-color: white; text-align: center; border-radius: .2rem;">
                                No logs available for today
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <div id="slpTableContainer" class="table-responsive" style="margin-top: 50px; width: 100%; max-height: 580px; overflow-y: auto; background-color:white; padding:10px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; border-radius:.5rem;">
                <table class="table table-bordered" id="userTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Birthdate</th>
                            <th>PRC ID Number</th>
                            <th>Subcription</th>

                            <th>Status</th>
                            <th>Email Verified</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slpUsers as $user) : ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td><?php echo $user['firstname']; ?></td>
                                <td><?php echo $user['lastname']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['gender']; ?></td>
                                <td><?php echo $user['birthdate']; ?></td>
                                <td><a href="#" style="text-decoration: underline;" onclick="showPrcIdImage('<?php echo $user['prc_id']; ?>')"><?php echo $user['prc_id_no']; ?></a></td>
                                <td>
                                    <?php
                                    // Check if the user exists in the subscription table
                                    $stmt = $conn->prepare("SELECT COUNT(*) FROM subscription WHERE user_id = :user_id");
                                    $stmt->bindParam(':user_id', $user['id']);
                                    $stmt->execute();
                                    $subscriptionExists = $stmt->fetchColumn();

                                    // Render the "View" button if the subscription exists
                                    if ($subscriptionExists) {
                                        echo '<a href="#" style="text-decoration: underline;" onclick="viewSubscriptionDescription(' . $user['id'] . ')">View</a>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($user['status'] == 0) : ?>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" style="background-color: #133A1B;" type="button" id="dropdownMenuButton<?php echo $user['id']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Pending
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $user['id']; ?>">
                                            <a class="dropdown-item" href="#" onclick="confirmStatusUpdate(<?php echo $user['id']; ?>, 1, '<?php echo $user['email']; ?>')">Approve</a>
<a class="dropdown-item" href="#" onclick="confirmStatusUpdate(<?php echo $user['id']; ?>, 2, '<?php echo $user['email']; ?>')">Reject</a>

                                              <a class="dropdown-item" href="#" onclick="confirmDelete(<?php echo $user['id'];?>)">Terminate</a>
                                            </div>
                                        </div>
                                    <?php elseif ($user['status'] == 1) : ?>
                                        Approved
                                    <?php elseif ($user['status'] == 2) : ?>
                                        Rejected
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $user['is_verified']; ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($user['createdAt'] . ' +8 hours')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="chartsSection" style="display: none;">
            <div style="text-align: center; margin-bottom: 10px;">
                <h2 style="font-size: 28px; font-weight: bold; color: #333;">Data Analytics Report For Maneclick Users</h2>
                <div style="font-size: 16px; color: #555;">
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" name="startDate">
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" name="endDate">
                    <button onclick="updateReport()">Generate Report</button>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="flex: 1; display: flex; justify-content: space-around;">
                    <div style="width: 800px; height: 400px; background-color: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <canvas id="chart1" style="max-width: 100%; max-height: 100%;"></canvas>
                    </div>
                    <div style="width: 800px; height: 400px; background-color: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <canvas id="chart2" style="max-width: 100%; max-height: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div style="flex: 1; display: flex; justify-content: space-around;">
                    <div style="width: 600px; height: 400px; background-color: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <canvas id="chart3" style="max-width: 100%; max-height: 100%;"></canvas>
                    </div>
                    <div style="width: 600px; height: 400px; background-color: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <canvas id="chart4" style="max-width: 100%; max-height: 100%;"></canvas>
                    </div>
                    <div style="width: 600px; height: 400px; background-color: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <canvas id="chart5" style="max-width: 100%; max-height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="prcIdModal" tabindex="-1" aria-labelledby="prcIdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="prcIdModalLabel">PRC ID Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="prcIdImage" src="" class="img-fluid" alt="PRC ID Image" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subscriptionModalLabel">Subscription Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="subscriptionDetails">
                    <!-- Subscription details will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

    <script>

    document.getElementById('toggleView').addEventListener('change', function() {
        var infoSection = document.getElementById('infoSection');
        var chartsSection = document.getElementById('chartsSection');

        if (this.checked) {
            infoSection.style.display = 'none';
            chartsSection.style.display = 'block';
            // Allow DOM to update before generating charts
            setTimeout(generateCharts, 100);
        } else {
            infoSection.style.display = 'block';
            chartsSection.style.display = 'none';
        }
    });

        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });

        $(document).ready(function() {
            $('#userTable').DataTable();
        });

        $(document).ready(function() {
            $('#patientsTable').DataTable();
        });


        var slpTableContainer = document.getElementById('slpTableContainer');
        var userTypeSelector = document.getElementById('userTypeSelector');

        function showPrcIdImage(prcIdFilePath) {
            // Set the image source attribute
            console.log(prcIdFilePath)
            document.getElementById('prcIdImage').src = prcIdFilePath;
            // Show the modal
            $('#prcIdModal').modal('show');
        }

        function confirmStatusUpdate(userId, newStatus,email) {
            var confirmation = confirm("Are you sure you want to change the status?");
            if (confirmation) {
                // Construct the request body
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('new_status', newStatus);
                formData.append('email', email);
                // console.log(newStatus)

                // Send the fetch request
                fetch('../BACKEND/routes/update_status_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.status === 200) {
                            // Show success message using SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'User status updated successfully.'
                            }).then((result) => {
                                // Reload the page after successful update
                                location.reload();
                            });
                        } else {
                            // Show error message using SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update user status.'
                            });
                        }
                    })
                    .catch(error => {
                        // Show error message using SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.'
                        });
                        console.error('Error:', error);
                    });
            }
        }

        function confirmDelete(userId) {
            var confirmation = confirm("Are you sure you want to delete this user?");
         
            if (confirmation) {
                const formData = new FormData();
                formData.append('id', userId);
                console.log(formData)

                // Send the fetch request
                fetch('../BACKEND/routes/delete_slp_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.status === 200) {
                            // Show success message using SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'User deleted successfully.'
                            }).then((result) => {
                                // Reload the page after successful update
                                location.reload();
                            });
                        } else {
                            // Show error message using SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete user.'
                            });
                        }
                    })
                    .catch(error => {
                        // Show error message using SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.'
                        });
                        console.error('Error:', error);
                    });
            }
        }

        function viewSubscriptionDescription(userId) {
            // Make AJAX request to get subscription information
            $.ajax({
                url: '../BACKEND/routes/get_subscription.php',
                type: 'GET',
                data: {
                    user_id: userId
                },
                dataType: 'json',
                success: function(response) {
                    // Update the Bootstrap modal with subscription details
                    if (response.error) {
                        alert('Error: ' + response.error);
                    } else {
                        var createdAtDate = new Date(response.createdAt);

                        createdAtDate.setHours(createdAtDate.getHours() + 8);
                        var formattedCreatedAt = createdAtDate.toLocaleString();
                        // Construct the HTML for the subscription details
                        var html = '<p><strong>Subscription Type:</strong> ' + response.type + '</p>';
                        html += '<p><strong>Subscription Cost:</strong> ' + response.plan_cost + '</p>';
                        // html += '<p><strong>Account Number:</strong> ' + response.s_account_number + '</p>';
                        // html += '<p><strong>Account Name:</strong> ' + response.s_account_name + '</p>';
                        // html += '<p><strong>Payment Reference Number:</strong> ' + response.payrefnumber + '</p>';
                        html += '<p><strong>Created At:</strong> ' + formattedCreatedAt + '</p>';

                        // Update the modal body with the subscription details
                        $('#subscriptionDetails').html(html);

                        // Show the Bootstrap modal
                        $('#subscriptionModal').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while fetching subscription information.');
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
    <!-- script to get the chart data -->
    <script>
        var chart1, chart2, chart3, chart4, chart5;

        function updateReport() {
            // Get the start and end dates from the inputs
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            console.log(startDate)
            console.log(endDate)

            // // Update the date range display
            // const dateRangeText = document.getElementById('dateRangeText');
            // console.log(dateRangeText)
            // dateRangeText.innerText = `Date Range: ${startDate} - ${endDate}`;

            // Fetch new data and update the charts
            fetchChartData(startDate, endDate).then(({ data1, data2, data3, data4, data5 }) => {
                updateCharts(data1, data2, data3, data4, data5);
            }).catch(error => {
                console.error('Error fetching chart data:', error);
            });
        }

        function fetchChartData(startDate, endDate) {
        // Fetch data from the server based on the date range
        return Promise.all([
            fetch(`../BACKEND/routes/fetchsubscription.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Fetched data for Chart 1:", data); // Log the fetched data
                    return data;
                })
                .catch(error => {
                    console.error("Error fetching Chart 1 data:", error);
                    return []; // Return an empty array or appropriate fallback
                }),
            fetch(`../BACKEND/routes/loginuser.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .catch(error => {
                    console.error("Error fetching Line Chart data:", error);
                    return [];
                }),
            fetch(`../BACKEND/routes/monthlyrevenue.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .catch(error => {
                    console.error("Error fetching Pie Chart data:", error);
                    return [];
                }),
            fetch(`../BACKEND/routes/fetchusers.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .catch(error => {
                    console.error("Error fetching Doughnut Chart data:", error);
                    return [];
                }),
            fetch(`../BACKEND/routes/subslist.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .catch(error => {
                    console.error("Error fetching Radar Chart data:", error);
                    return [];
                })
        ]).then(([data1, data2, data3, data4, data5]) => {
            console.log("d",data2)
            console.log("f",data4)
            // console.log("Data received for all charts:", { data1, data2, data3, data4, data5 });
            return {
                data1: formatChartData(data1, `Subscriptions from ${startDate} to ${endDate}`, 'bar'),
                data2: formatChartData2(data2, `Line Chart from ${startDate} to ${endDate}`, 'line'),
                data3: formatChartData(data3, `Pie Chart from ${startDate} to ${endDate}`, 'pie'),
                data4: formatChartData(data4, `Doughnut Chart from ${startDate} to ${endDate}`, 'doughnut'),
                data5: formatChartData5(data5, `Radar Chart from ${startDate} to ${endDate}`, 'radar')
            };
        }).catch(error => {
            console.error("Error processing chart data:", error);
        });
    }

    function formatChartData2(data, label, chartType) {
        const labels = data.map(item => item.date);
        const counts = data.map(item => item.role_count);
 
            return {
                labels: labels,
                datasets: [{
                    label: "Daily User Login",
                    data: counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            };
        }

    function formatChartData5(data, label, chartType) {
        const labels = data.map(item => item.type);
        const counts = data.map(item => item.count);
 
            return {
                labels: labels,
                datasets: [{
                    label: "Maneclick Plans Availed Comparison",
                    data: counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            };
        }

        function formatChartData(data, label, chartType) {

                const labels = data.map(item => {
                    // Format month into a more readable format
                    const date = new Date(item.month + '-01');
                    return date.toLocaleString('default', { month: 'long' }); 
                });
                const counts = data.map(item => item.count);

                const labeled = chartType === 'bar' ? 'User Subscription Per Month' : 
                   (chartType === 'pie' ? 'Revenue' :
                    (chartType === 'doughnut' ? 'Newly Registered User' :
                     (chartType === 'radar' ? 'Performance' : 'Data')));

 
            return {
                labels: labels,
                datasets: [{
                    label: labeled,
                    data: counts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            };
        }

        function updateCharts(data1, data2, data3, data4, data5) {
            // Destroy existing charts if they exist
            if (chart1) chart1.destroy();
            if (chart2) chart2.destroy();
            if (chart3) chart3.destroy();
            if (chart4) chart4.destroy();
            if (chart5) chart5.destroy();

            // Generate new charts with updated data
            generateCharts(data1, data2, data3, data4, data5);
        }

        function generateCharts(data1, data2, data3, data4, data5) {
            if (chart1) chart1.destroy();
            if (chart2) chart2.destroy();
            if (chart3) chart3.destroy();
            if (chart4) chart4.destroy();
            if (chart5) chart5.destroy();
            // Get context for each chart
            var ctx1 = document.getElementById('chart1').getContext('2d');
            var ctx2 = document.getElementById('chart2').getContext('2d');
            var ctx3 = document.getElementById('chart3').getContext('2d');
            var ctx4 = document.getElementById('chart4').getContext('2d');
            var ctx5 = document.getElementById('chart5').getContext('2d');

            chart1 = new Chart(ctx1, {
                type: 'bar',
                data: data1,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            chart2 = new Chart(ctx2, {
                type: 'line',
                data: data2,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            chart3 = new Chart(ctx3, {
            type: 'pie',
            data: data3,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Maneclick Monthly Revenue',
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

            // Create Chart 4 with updated data
            chart4 = new Chart(ctx4, {
                type: 'doughnut',
                data: data4,
                options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Newly Registered Users',
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

            chart5 = new Chart(ctx5, {
            type: 'radar',
            data: data5,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'User-Favorite Plans',
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
        }

        // Initial chart generation with real data
        document.addEventListener("DOMContentLoaded", function() {
            // Default date range could be set here, or you could initialize with specific dates
            const startDate = '2024-01-01'; // Example start date
            const endDate = '2024-12-31';   // Example end date

            fetchChartData(startDate, endDate).then(({ data1, data2, data3, data4, data5 }) => {
                generateCharts(data1, data2, data3, data4, data5);
            }).catch(error => {
                console.error('Error fetching initial chart data:', error);
            });
        });
    </script>

</body>

</html>