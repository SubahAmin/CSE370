<?php
require_once('DBconnect.php');

if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['role'])){

    $rawPassword = $_POST['password'];
    $rawConfirm  = $_POST['confirm_password'];
    $role        = $_POST['role'];

    $roleToFlag = [
        'Donor'       => 'DONOR_FLAG',
        'Borrower'    => 'BORROWER_FLAG',
        'Coordinator' => 'COORDINATOR_FLAG',
    ];

    if(!isset($roleToFlag[$role])){
        header("Location: signup.php?error=general");
        exit();
    }
    $flagColumn = $roleToFlag[$role];

    if($rawPassword !== $rawConfirm){
        header("Location: signup.php?error=mismatch");
        exit();
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $rawPassword);

    // does an account with this email already exist?
    $checkSql = "SELECT * FROM user WHERE EMAIL = '$email'";
    $checkResult = mysqli_query($conn, $checkSql);

    if($checkResult && mysqli_num_rows($checkResult) > 0){
        $existing = mysqli_fetch_assoc($checkResult);

       
        if($existing['PASSWORD'] !== $rawPassword){
            header("Location: signup.php?error=dup");
            exit();
        }

        
        if($existing[$flagColumn] === 'YES'){
            header("Location: signup.php?error=role");
            exit();
        }

        // Valid case: same person, adding a role they don't have yet
        // (e.g. an existing Donor signing up as Borrower too).
        $updateSql = "UPDATE user SET $flagColumn = 'YES' WHERE EMAIL = '$email'";
        if(mysqli_query($conn, $updateSql)){
            header("Location: index.php?upgraded=1");
        }
        else{
            header("Location: signup.php?error=general");
        }
        exit();
    }
    else{
        
        $donorFlag       = ($role === 'Donor')       ? 'YES' : 'NO';
        $borrowerFlag    = ($role === 'Borrower')    ? 'YES' : 'NO';
        $coordinatorFlag = ($role === 'Coordinator') ? 'YES' : 'NO';

        $sql = "INSERT INTO user (EMAIL, PASSWORD, DONOR_FLAG, BORROWER_FLAG, COORDINATOR_FLAG)
                VALUES ('$email', '$password', '$donorFlag', '$borrowerFlag', '$coordinatorFlag')";

        if(mysqli_query($conn, $sql)){
            header("Location: index.php");
        }
        else{
            header("Location: signup.php?error=general");
        }
        exit();
    }
}
else{
    header("Location: signup.php?error=general");
    exit();
}
?>