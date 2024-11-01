<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/BACKEND/config/db.php';

$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2');
$dotenv->load();

function paypalGetAccessToken(): string
{
    if (isset($_SESSION['PaypalToken']) && isset($_SESSION['PaypalTokenExpiry'])) {
        if (strtotime($_SESSION['PaypalTokenExpiry']) > time()) {
            return $_SESSION['PaypalToken'];
        }
    }

    $ch = curl_init($_ENV['Paypal_ApiEndpoint'] . "/v1/oauth2/token");

    //The data you want to send via POST
    $fields = [
        'grant_type' => 'client_credentials',
    ];

    $fields_string = http_build_query($fields);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_USERPWD, $_ENV['Paypal_ClientID'] . ':' . $_ENV['Paypal_ClientSecret']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

    //execute post
    $result = json_decode(curl_exec($ch));

    curl_close($ch);

    $_SESSION['PaypalToken'] = $result->access_token;
    $_SESSION['PaypalTokenExpiry'] = date("Y-m-d H:i:s", time() + $result->expires_in - 30);


    return $result->access_token;
}

function paypalGetSubscriptionInfo($subscriptionID): object
{
    paypalGetAccessToken();

    $ch = curl_init($_ENV['Paypal_ApiEndpoint'] . "/v1/billing/subscriptions/" . $subscriptionID);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['PaypalToken'],
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //execute post
    $result = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($result);

    if (isset($data->billing_info->next_billing_time)) {
        $nextpaymentdate = date('Y-m-d', strtotime($data->billing_info->next_billing_time ?? '0000-00-00'));

        $updateDBData = [
            'status' => $data->status == 'ACTIVE' ? 1 : 0,
            'next_payment_date' => $nextpaymentdate,
            'subscriptionID' => $subscriptionID
        ];

        global $conn;
        // Query to update subscription information based on subcriptionID
        $stmt = $conn->prepare("UPDATE subscription SET status = :status, next_payment_date = :next_payment_date WHERE paypal_sub_id = :subscriptionID");
    } else {
        $updateDBData = [
            'status' => $data->status == 'ACTIVE' ? 1 : 0,
            'subscriptionID' => $subscriptionID
        ];

        global $conn;
        // Query to update subscription information based on subcriptionID
        $stmt = $conn->prepare("UPDATE subscription SET status = :status WHERE paypal_sub_id = :subscriptionID");
    }


    $stmt->execute($updateDBData);

    return $data;
}

function paypalCancelSubscription($subscriptionID)
{
    paypalGetAccessToken();

    $ch = curl_init($_ENV['Paypal_ApiEndpoint'] . "/v1/billing/subscriptions/" . $subscriptionID . "/cancel");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['PaypalToken'],
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    //The data you want to send via POST
    $fields = [
        'reason' => 'user unsubscribed.'
    ];

    $fields_string = json_encode($fields);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

    //execute post
    $result = curl_exec($ch);

    curl_close($ch);

    $updateDBData = [
        'status' => 0,
        'subscriptionID' => $subscriptionID
    ];

    global $conn;
    // Query to update subscription information based on subcriptionID
    $stmt = $conn->prepare("UPDATE subscription SET status = :status WHERE paypal_sub_id = :subscriptionID");
    $stmt->execute($updateDBData);

}