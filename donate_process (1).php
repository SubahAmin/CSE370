<?php
require_once('auth.php');
require_role('donor_flag');
require_once('DBconnect.php');

if(isset($_POST['device_type']) && isset($_POST['device_condition'])){

    $deviceType = mysqli_real_escape_string($conn, $_POST['device_type']);
    $descriptionText = mysqli_real_escape_string($conn, $_POST['description_text'] ?? '');
    $deviceCondition = mysqli_real_escape_string($conn, $_POST['device_condition']);
    $donorId = $_SESSION['user_id']; // who's logged in IS the donor, no more manual picking
    $today = date('Y-m-d');

    if($deviceType === '' || $deviceCondition === ''){
        header("Location: donate.php?error=1");
        exit();
    }

    // 1. log the donation itself
    $donationId = 'D' . time();
    $insertDonation = "INSERT INTO donations (DONATION_ID, DONATION_DATE, DEVICE_TYPE, DESCRIPTION_TEXT, DONOR_ID)
                        VALUES ('$donationId', '$today', '$deviceType', '$descriptionText', '$donorId')";
    mysqli_query($conn, $insertDonation);

    // 2. create the physical device row tied to that donation, ready to lend
    $deviceId = 'DEV' . time();
    $insertDevice = "INSERT INTO device (DEVICE_ID, DEVICE_CONDITION, STATUS, DONATION_ID)
                      VALUES ('$deviceId', '$deviceCondition', 'Storage', '$donationId')";
    mysqli_query($conn, $insertDevice);

    header("Location: donate_success.php");
    exit();
}
else{
    header("Location: donate.php?error=1");
    exit();
}
?>