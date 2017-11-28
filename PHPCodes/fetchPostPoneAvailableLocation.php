<?php
//Display the location that is available to be postponed into the calendar
include_once('../DBConfig.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$trainingID = $_POST['GrpTrainingID'];
$startDate = (date('Y-m-d', strtotime($_POST['startDate'])));
$startTime = $_POST['startTime'];
$gymId = $_POST['gymId'];
$gymLocation = "";



$data = array();
//Fetch name using gymID
$sql = "SELECT gymName from gym WHERE id=?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_gymId);
    $param_gymId = $gymId;
    if (mysqli_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $gymName);
        mysqli_stmt_fetch($stmt);
        $gymLocation = $gymName;
    } else if (mysqli_stmt_affected_rows($stmt) == 0) {
        
    }
}



$sql = "SELECT facilityName,facilityCapacity from gymfacility 
WHERE gymid = ( SELECT ID from gym WHERE gym.gymName=?)
AND facilityName not in 
( SELECT LEFT(trainingFacility,LOCATE(' (',trainingFacility) - 1)  as 'name' from grouptrainingschedule gts WHERE 
                         trainingDate=? AND trainingTime=? AND trainingGym=?)
AND facilityCapacity >= ( select trainingMaxCapacity from grouptrainingschedule WHERE ID=?)";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ssssi", $param_gymName, $param_trainingDate, $param_time, $param_gym, $paramid);
//                , $param_trainingDate, $param_time, $param_gym, $param_trainingid);
    $param_gymName = $gymLocation;
    $param_trainingDate = $startDate;
    $param_time = $startTime;
    $param_gym = $gymLocation;
    $paramid = $trainingID;
//        $param_trainingid = $trainingID;
    if (mysqli_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $facilityName, $facilityCapacity);
        while ($stmt->fetch()) {
            $facilityDetail = $facilityName . " (Room Size:" . $facilityCapacity . ")";
            $data[] = $facilityDetail;
        }
        echo json_encode($data);
    } else if (mysqli_stmt_affected_rows($stmt) == 0) {
        //Account balance is insufficnet
//        echo "No Facilities Available yet ";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>




