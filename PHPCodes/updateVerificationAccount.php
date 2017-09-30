<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    $uid = $_POST['id'];
    

    // Do whatever you want with the $uid

$sql = "UPDATE users SET verified='Verified' WHERE userid='$uid'";

if (mysqli_query($link, $sql)) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($link);
?>   
