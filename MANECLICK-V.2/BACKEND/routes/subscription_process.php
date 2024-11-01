<?php
include '../config/db.php';

// Initialize response array
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_POST['user_id'];
    $plan_type = $_POST['type']; // Change 'plan_type' to 'type'
    $cost = $_POST['plan_cost']; // Change 'cost' to 'plan_cost'
    $paypal_sub_id = $_POST['paypal_sub_id'];
    $paypal_facilitator_access_token = $_POST['paypal_facilitator_access_token'];
    $paypal_order_id = $_POST['paypal_order_id'];

    $account_number = isset($_POST['s_account_number']) ? $_POST['s_account_number'] : null;
    $account_name = isset($_POST['s_account_name']) ? $_POST['s_account_name'] : null;
    $reference_number = isset($_POST['payrefnumber']) ? $_POST['payrefnumber'] : null;

    // Insert subscription data into the database
    $sql = "INSERT INTO subscription (user_id, type, plan_cost, s_account_number, s_account_name, payrefnumber, paypal_sub_id, paypal_facilitator_access_token, paypal_order_id)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared properly
    if ($stmt) {
        // Bind parameters
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $plan_type);
        $stmt->bindParam(3, $cost);
        $stmt->bindParam(4, $account_number);
        $stmt->bindParam(5, $account_name);
        $stmt->bindParam(6, $reference_number);
        $stmt->bindParam(7, $paypal_sub_id);
        $stmt->bindParam(8, $paypal_facilitator_access_token);
        $stmt->bindParam(9, $paypal_order_id);

        if ($stmt->execute()) {
            // Subscription successfully inserted
            $response['success'] = true;
            $response['message'] = "Subscription successful";
            http_response_code(200); // OK
        } else {
            // Subscription insertion failed
            $response['success'] = false;
            $response['message'] = "Error: Subscription failed";
            http_response_code(400); // Bad Request
        }
    } else {
        // Statement preparation failed
        $response['success'] = false;
        $response['message'] = "Error: Database operation failed";
        http_response_code(500); // Internal Server Error
    }
} else {
    // Method not allowed
    $response['success'] = false;
    $response['message'] = "Method not allowed";
    http_response_code(405); // Method Not Allowed
}

// Send JSON response
echo json_encode($response);
?>