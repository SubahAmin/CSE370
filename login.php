<?php
require_once('auth.php');
require_once('DBconnect.php');

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $sql      = "SELECT * FROM user WHERE EMAIL = '$username'";
    $result   = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);

        if($_POST['password'] === $row['PASSWORD']){
            $today = date('Y-m-d');
            $email = mysqli_real_escape_string($conn, $row['EMAIL']);

            // Block check — use EMAIL as it is what gets stored in BORROWER_ID
            $blocked = false;
            if(strtoupper(trim($row['BORROWER_FLAG'])) === 'YES'){
                $bc = mysqli_query($conn, "
                    SELECT COUNT(*) AS cnt
                    FROM loan l
                    JOIN requests r ON l.REQUEST_ID = r.REQUEST_ID
                    WHERE r.BORROWER_ID = '$email'
                      AND DATEDIFF('$today', l.DUE_DATE) >= 15
                      AND (l.ACTUAL_RETURN_DATE IS NULL
                           OR l.ACTUAL_RETURN_DATE = '0000-00-00'
                           OR CAST(l.ACTUAL_RETURN_DATE AS CHAR) = '0000-00-00')");
                if($bc){
                    $br      = mysqli_fetch_assoc($bc);
                    $blocked = (int)$br['cnt'] > 0;
                }
            }

            // Store email as the primary identifier — matches BORROWER_ID in DB
            $_SESSION['user_id']          = $row['EMAIL'];   // email, not USER_ID
            $_SESSION['email']            = $row['EMAIL'];
            $_SESSION['donor_flag']       = $row['DONOR_FLAG'];
            $_SESSION['borrower_flag']    = $row['BORROWER_FLAG'];
            $_SESSION['coordinator_flag'] = $row['COORDINATOR_FLAG'];
            $_SESSION['blocked']          = $blocked;

            header("Location: home.php");
            exit();
        }
        else{
            header("Location: index.php?error=1");
            exit();
        }
    }
    else{
        header("Location: index.php?error=1");
        exit();
    }
}
else{
    header("Location: index.php?error=1");
    exit();
}
?>