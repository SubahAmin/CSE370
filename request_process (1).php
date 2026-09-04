<?php
require_once('auth.php');
require_role('borrower_flag');
require_once('DBconnect.php');

$today      = date('Y-m-d');
$borrowerId = $_SESSION['email'] ?? $_SESSION['user_id'];  // email = BORROWER_ID in DB
$borrowerEsc = mysqli_real_escape_string($conn, $borrowerId);

// Block check
$bc = mysqli_query($conn, "
    SELECT COUNT(*) AS cnt FROM loan l
    JOIN requests r ON l.REQUEST_ID = r.REQUEST_ID
    WHERE r.BORROWER_ID = '$borrowerEsc'
      AND DATEDIFF('$today', l.DUE_DATE) >= 15
      AND (l.ACTUAL_RETURN_DATE IS NULL
           OR l.ACTUAL_RETURN_DATE = '0000-00-00'
           OR CAST(l.ACTUAL_RETURN_DATE AS CHAR) = '0000-00-00')");
if($bc){ $br = mysqli_fetch_assoc($bc); if((int)$br['cnt'] > 0){ header("Location: home.php"); exit(); } }

if(isset($_POST['device_type']) && isset($_POST['duration'])){

    $deviceType = mysqli_real_escape_string($conn, $_POST['device_type']);
    $duration   = (int) $_POST['duration'];

    if($duration < 1){
        header("Location: request_device.php?error=1");
        exit();
    }

    // 1. log the request
    $requestId     = 'REQ' . time();
    $insertRequest = "INSERT INTO requests (REQUEST_ID, REQUEST_DATE, ESTIMATED_DURATION, BORROWER_ID)
                      VALUES ('$requestId', '$today', $duration, '$borrowerEsc')";
    mysqli_query($conn, $insertRequest);

    // 2. find a free device
    $candidateSql = "SELECT d.DEVICE_ID, l.DUE_DATE
                     FROM device d
                     JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
                     LEFT JOIN loan l  ON l.DEVICE_ID = d.DEVICE_ID
                                      AND (l.ACTUAL_RETURN_DATE = '0000-00-00'
                                           OR l.ACTUAL_RETURN_DATE IS NULL
                                           OR CAST(l.ACTUAL_RETURN_DATE AS CHAR) = '0000-00-00')
                     WHERE dn.DEVICE_TYPE = '$deviceType'
                       AND d.STATUS != 'Maintainence'
                     ORDER BY (l.DUE_DATE IS NULL) DESC, l.DUE_DATE ASC";
    $candidates = mysqli_query($conn, $candidateSql);

    $freeDeviceId     = null;
    $nextFreeDeviceId = null;
    $nextFreeDate     = null;

    while($row = mysqli_fetch_assoc($candidates)){
        if($row['DUE_DATE'] === null){
            $freeDeviceId = $row['DEVICE_ID'];
            break;
        } elseif($nextFreeDeviceId === null){
            $nextFreeDeviceId = $row['DEVICE_ID'];
            $nextFreeDate     = $row['DUE_DATE'];
        }
    }

    if($freeDeviceId !== null){
        $loanId  = 'LOAN' . time();
        $dueDate = date('Y-m-d', strtotime("+$duration days"));
        mysqli_query($conn, "INSERT INTO loan
            (LOAN_ID, START_DATE, DUE_DATE, ACTUAL_RETURN_DATE, OVERDUE_STATUS, COORDINATOR_ID, REQUEST_ID, DEVICE_ID)
            VALUES ('$loanId','$today','$dueDate','0000-00-00','No','$borrowerEsc','$requestId','$freeDeviceId')");
        mysqli_query($conn, "UPDATE device SET STATUS='Donated' WHERE DEVICE_ID='$freeDeviceId'");
        header("Location: request_result.php?status=approved&device=".urlencode($freeDeviceId)."&due=".urlencode($dueDate));
        exit();
    }
    elseif($nextFreeDeviceId !== null){
        $posRes  = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM waitlist WHERE DEVICE_ID='$nextFreeDeviceId'"); //Counts how many people are already waiting for this specific device.
        $posRow  = mysqli_fetch_assoc($posRes); //fetches that count as an associative array
        $position = $posRow['cnt'] + 1; //My queue position = everyone ahead of me + 1. If 2 people are waiting, I get position 3.
        $waitlistId = 'WL' . time(); //generates a unique waitlist ID using the current Unix timestamp 
        mysqli_query($conn, "INSERT INTO waitlist (WAITLIST_ID, QUEUE_POSITION, NOTIFIED_FLAG, REQUEST_ID, DEVICE_ID)
            VALUES ('$waitlistId', $position, 'NO', '$requestId', '$nextFreeDeviceId')"); //Writes the waitlist row. NOTIFIED_FLAG = NO means email not yet sent. Links to REQUEST_ID so we know who is waiting and for how long (from ESTIMATED_DURATION).
        header("Location: request_result.php?status=waitlisted&device=".urlencode($nextFreeDeviceId)."&expected=".urlencode($nextFreeDate)."&position=".$position); //Redirects to the result page with position number and expected return date in the URL so the borrower sees their place in line.
        exit();
    }
    else{
        header("Location: request_result.php?status=none");
        exit();
    }
}
else{
    header("Location: request_device.php?error=1");
    exit();
}
?>