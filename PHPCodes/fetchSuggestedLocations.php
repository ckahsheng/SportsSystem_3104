<?php

include_once('../DBConfig.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$trainingID = $_POST['trainingUID'];
$data = array();
$gym = "";
$trainingDate = "";
$trainingTime = "";
$facilityCapacity = "";
$sql = "SELECT trainingGym,trainingDate,trainingTime,trainingMaxCapacity from grouptrainingschedule WHERE id=?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $trainingID;
    if (mysqli_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $gym1, $trainingDate1, $trainingTime1, $facilityCapacity1);
        mysqli_stmt_fetch($stmt);
        $gym = $gym1;
        $trainingDate = $trainingDate1;
        $trainingTime = $trainingTime1;
        $facilityCapacity = $facilityCapacity1;
    } else if (mysqli_stmt_affected_rows($stmt) == 0) {
        
    }

////Fetch only suggested locations
//    $sql = "SELECT facilityName,facilityCapacity from gymfacility 
//WHERE gymid = ( SELECT ID from gym WHERE gym.gymName=?)
//AND facilityName not in 
//( SELECT LEFT(trainingFacility,LOCATE(' (',trainingFacility) - 1)  as 'name' from grouptrainingschedule gts WHERE 
//                         trainingDate=? AND trainingTime=? AND trainingGym=?)
//AND facilityCapacity >= ( select trainingMaxCapacity from grouptrainingschedule WHERE ID=?";
    $sql = "SELECT facilityName,facilityCapacity from gymfacility 
WHERE gymid = ( SELECT ID from gym WHERE gym.gymName=?)
AND facilityName not in 
( SELECT LEFT(trainingFacility,LOCATE(' (',trainingFacility) - 1)  as 'name' from grouptrainingschedule gts WHERE 
                         trainingDate=? AND trainingTime=? AND trainingGym=?)
AND facilityCapacity >= ( select trainingMaxCapacity from grouptrainingschedule WHERE ID=?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $param_gymName, $param_trainingDate, $param_time, $param_gym, $paramid);
//                , $param_trainingDate, $param_time, $param_gym, $param_trainingid);
        $param_gymName = $gym;
        $param_trainingDate = $trainingDate;
        $param_time = $trainingTime;
        $param_gym = $gym;
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
}
mysqli_close($link);
?>




