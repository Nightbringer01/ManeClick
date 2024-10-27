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


$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT type, plan_cost FROM subscription WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$subscriptionProfile = $stmt->fetch(PDO::FETCH_ASSOC);


// Add 8 hours to the createdAt date
$createdAt = new DateTime($profile['createdAt']);
$createdAt->modify('+8 hours');
$createdAtText = $createdAt->format('Y-m-d H:i:s');

// Convert the date to text format
$createdAtText = $createdAt->format('l, F j, Y \a\t g:i A');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/index.css">

    <style>
        .gray-line {
            border-top: 4px solid #ccc;
        }
    </style>
</head>

<body>
    <div id="header">
        <?php include './component/pageHeader.php' ?>
    </div>
    <div style="display: flex; flex-direction: row; background-color: white; padding:10px; border:4px solid #133A1B; border-radius: .5rem; margin: 20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;">
        <div style="display: flex; flex-direction:column; justify-content: center; align-items:center; width:30%; height:350px;">
            <img src="../..<?php echo $profile['prc_id']; ?>" style="height: 200px; width:400px" />
            <text style="text-decoration: underline; color:steelblue; font-size:x-large"><?php echo $profile['prc_id_no']; ?></text>
        </div>
        <div class="container mt-5" style="width: 70%">
            <div class="card">
                <div class="card-header" style="font-size:xx-large; font-weight:700;">
                    SLP Profile
                </div>
                <hr class="gray-line">
                <div class="card-body" style="margin-top:1%">
                    <p style="border: 2px solid gray; font-size:x-large; font-weight:700;">
                        <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Full Name:</span></strong>
                        <span class="align-middle;"><?php echo $profile['firstname'] . ' ' . $profile['lastname']; ?></span>
                    </p>
                    <div class="card-text" style="font-size:x-large; width:100%">
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Role:</span></strong>
                            <span class="align-middle"><?php echo $profile['role']; ?></span>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Username:</span></strong>
                            <?php echo $profile['username']; ?><br>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Email:</span></strong>
                            <?php echo $profile['email']; ?><br>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Phone:</span></strong>
                            <?php echo $profile['phone']; ?><br>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Birthdate:</span></strong>
                            <?php echo $profile['birthdate']; ?><br>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Address:</span></strong>
                            <?php echo $profile['address']; ?><br>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Account Created:</span></strong>
                            <?php echo $createdAtText; ?><br>
                        </p>
                        <p style="border: 2px solid gray;">
                            <strong><span style="display: inline-block; width: 250px; border-right: 2px solid gray; padding-right: 5px; margin-left:1%;">Account Plan:</span></strong>
                            <?php echo $subscriptionProfile['type']. ', ₱' . $subscriptionProfile['plan_cost']; ?><br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

    </script>

</body>

</html>