<?php
require_once('DBconnect.php');

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM user WHERE User_ID = '$username' AND PASSWORD = '$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) != 0){
        //echo "Let him enter";
        header("Location: home.php");
    }
    else{
        echo "Wrong username or password";
        //header("Location: index.php");
    }
}
?>