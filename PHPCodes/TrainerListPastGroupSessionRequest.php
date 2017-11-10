<?php
require_once '../DBConfig.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $trainerName= $_SESSION['username'];

$sqltran = mysqli_query($link, "SELECT * FROM grouptrainingschedule WHERE trainerName='$trainerName' && trainingApprovalStatus!='Pending'")or die(mysqli_error($link));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'title'=>$rowList['trainingTitle'],
               'created_at'=>$rowList['created_at'],
        'trainername' => $rowList['trainerName'],
        'category' => $rowList['trainingCategory'],
        'rate' => $rowList['trainingRate'],
        'gym' => $rowList['trainingGym'],
        'facility' => $rowList['trainingFacility'],
        'capacity' => $rowList['trainingMaxCapacity'],
        'status'=>$rowList['trainingApprovalStatus'],
        'remarks'=>$rowList['remarks']
 
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
