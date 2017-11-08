<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    $gid = $_POST['id'];

$sql = "DELETE FROM `gym` WHERE `gym`.`id` = '$gid' ";
if (mysqli_query($link, $sql)) {
    
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . mysqli_error($link);
}

mysqli_close($link);
?>   
