<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/MANECLICK-V.2/BACKEND/Util/paypal.php';

paypalCancelSubscription($_SESSION['paypal_sub_id']);

header('Location: /MANECLICK-V.2/FRONTEND/homepage.php');