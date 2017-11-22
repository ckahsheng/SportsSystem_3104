<?php

//fetch.php  
require_once '../DBConfig.php';

if (isset($_POST["groupTrainingID"])) {
    $result = mysqli_query($link, "SELECT * FROM grouptrainingschedule WHERE GrpRecurrID = '" . $_POST["groupTrainingID"] . "'")or die(mysqli_error($con));
    $rows = [];
    while ($row = mysqli_fetch_array($result)) {
        $rows[] = $row;
    }
//    $row = mysqli_fetch_array($result);
    echo json_encode($rows);
}
mysqli_close($link);
?>