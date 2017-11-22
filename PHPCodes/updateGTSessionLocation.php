<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$gid = $_POST['id'];
$updatedFacility = $_POST['updatedFac'];
$sql = "UPDATE grouptrainingschedule SET trainingFacility='$updatedFacility', invalidLocation='NO' WHERE id='$gid'";
if (mysqli_query($link, $sql)) {
    $countInvalid = "";
    $grpRecurrID="";
    $sql = "SELECT COUNT(id) as 'invalidLoc'
FROM grouptrainingschedule
WHERE GrpRecurrID = (SELECT GrpRecurrID from grouptrainingschedule WHERE id=?) AND invalidLocation='YES'";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $gid;
        if (mysqli_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $valueCount);
            mysqli_stmt_fetch($stmt);
            $countInvalid = $valueCount;
           echo $countInvalid; 
           if($countInvalid<=0){
               $sql="SELECT GrpRecurrID from grouptrainingschedule WHERE id=?";
               $stmt1=mysqli_prepare($link, $sql);
               mysqli_stmt_bind_param($stmt1, "i", $param_id);
               $param_id = $gid;
               mysqli_execute($stmt1);
               mysqli_stmt_store_result($stmt1);
               mysqli_stmt_bind_result($stmt1, $recurr);
               mysqli_stmt_fetch($stmt1);
               $empty="";
               $sql = "UPDATE grouptrainings SET dateUnavailable='$empty' WHERE ID='$recurr'";
                if (mysqli_query($link, $sql)) {
//                    echo "";
                }
           }
        }
        echo "Location has been updated,please refresh to see the changes ";
    } else {
        echo "Error updating location- Please try again later: " . mysqli_error($link);
    }
}
mysqli_close($link);
?>   
