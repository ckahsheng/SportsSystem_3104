<?php

include_once('../DBConfig.php');
session_start();

if (isset($_SESSION['username'])) {

    $username = $_SESSION['username'];
    echo $username;

    $sql = "DELETE FROM `users` WHERE `userid` ='$username'";
    $result = mysqli_query($link, $sql) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
        echo "Record deleted successfully";
        header('Location:../index.php');
    } else {
        echo "Error deleting record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    mysqli_close($link);
    session_destroy();
}
?>




