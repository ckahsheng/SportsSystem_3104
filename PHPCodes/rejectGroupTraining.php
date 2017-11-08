<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    $gid = $_POST['id'];
    $message=$_POST['msg'];
$sql = "UPDATE grouptrainingschedule SET trainingApprovalStatus='Rejected' ,remarks='$message' WHERE id='$gid'";

if (mysqli_query($link, $sql)) {
    echo "Group training event has been rejected";
} else {
    echo "Error updating record: " . mysqli_error($link);
}

mysqli_close($link);
?>   
