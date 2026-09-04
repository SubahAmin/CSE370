<?php
require_once('auth.php');
require_role('borrower_flag');
require_once('DBconnect.php');

if(isset($_POST['loan_id']) && isset($_POST['condition_note']) && isset($_POST['cleared_for_lending'])){

    $loanId = mysqli_real_escape_string($conn, $_POST['loan_id']);
    $conditionNote = mysqli_real_escape_string($conn, $_POST['condition_note']);
    $cleared = mysqli_real_escape_string($conn, $_POST['cleared_for_lending']);

    $coordinatorId = $_SESSION["email"] ?? $_SESSION["user_id"];
    $borrowerIdEsc = mysqli_real_escape_string($conn, $_SESSION["email"] ?? $_SESSION["user_id"]);
    $today = date('Y-m-d');

    // verify this loan actually belongs to a request the logged-in borrower
    // made - otherwise anyone could POST a different LOAN_ID and close out
    // (and log a condition note against) a device that isn't theirs
    $loanSql = "SELECT l.DEVICE_ID
                FROM loan l
                JOIN requests r ON l.REQUEST_ID = r.REQUEST_ID
                WHERE l.LOAN_ID = '$loanId' AND r.BORROWER_ID = '$borrowerIdEsc'";
    $loanResult = mysqli_query($conn, $loanSql);
    $loanRow = $loanResult ? mysqli_fetch_assoc($loanResult) : null;

    if(!$loanRow){
        header("Location: return_device.php?error=1");
        exit();
    }
    $deviceId = $loanRow['DEVICE_ID'];

    // 1. close out the loan
    $updateLoan = "UPDATE loan SET ACTUAL_RETURN_DATE = '$today', OVERDUE_STATUS = 'No' WHERE LOAN_ID = '$loanId'";
    mysqli_query($conn, $updateLoan);

    // 2. refresh the device's condition note + status
    //    (this is the "condition gets updated every time it's returned" part)
    $newStatus = ($cleared === 'Yes') ? 'Storage' : 'Maintainence'; //Only auto-promote if the device is actually usable. If cleared=No, device needs maintenance first — no point assigning it to anyone.
    $updateDevice = "UPDATE device SET DEVICE_CONDITION = '$conditionNote', STATUS = '$newStatus' WHERE DEVICE_ID = '$deviceId'";
    mysqli_query($conn, $updateDevice);

    // 3. keep a permanent maintenance history entry
    $maintId = 'M' . time();
    $insertMaint = "INSERT INTO maintainence (MAINTAINENCE_ID, LOG_DATE, CLEARED_FOR_LENDING, NOTES, COORDINATOR_ID, DEVICE_ID)
                    VALUES ('$maintId', '$today', '$cleared', '$conditionNote', '$coordinatorId', '$deviceId')";
    mysqli_query($conn, $insertMaint);

    header("Location: show_devices.php");
    exit();
}
else{
    header("Location: return_device.php?error=1");
    exit();
}
?>