<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    $tid = $_POST['id'];
    
    echo $tid;

$sql = "DELETE FROM `trainingtype` WHERE `trainingtype`.`ID` = '$tid' ";
if (mysqli_query($link, $sql)) {
    
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . mysqli_error($link);
}

mysqli_close($link);
?>   
