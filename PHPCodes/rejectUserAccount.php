<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    $uid = $_POST['id'];
$sql = "UPDATE users SET verified='Rejected' WHERE userid='$uid'";

if (mysqli_query($link, $sql)) {
    echo "User has been rejected";
} else {
    echo "Error updating record: " . mysqli_error($link);
}

mysqli_close($link);
?>   
