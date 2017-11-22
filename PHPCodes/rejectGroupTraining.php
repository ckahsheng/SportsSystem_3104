<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$gid = $_POST['id'];
$message = $_POST['msg'];
$sql2 = "UPDATE grouptrainings SET trainingApprovalStatus='Rejected' WHERE ID=?";
$stmt1 = mysqli_prepare($link, $sql2);
mysqli_stmt_bind_param($stmt1, "s", $param_id);
$param_id = $gid;
mysqli_stmt_execute($stmt1);
$sql = "UPDATE grouptrainingschedule SET trainingApprovalStatus='Rejected' ,remarks='$message' WHERE GrpRecurrID='$gid'";
if (mysqli_query($link, $sql)) {
    echo "Group training event has been rejected";
} else {
    echo "Error updating record: " . mysqli_error($link);
}

mysqli_close($link);
?>   
