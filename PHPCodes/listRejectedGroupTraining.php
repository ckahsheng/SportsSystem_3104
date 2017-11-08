<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT * FROM grouptrainingschedule WHERE trainingApprovalStatus='Rejected'")or die(mysqli_error($con));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'groupId' => $rowList['id'],
        'trainerName' => $rowList['trainerName'],
        'title' => $rowList['trainingTitle'],
        'trainingCategory' => $rowList['trainingCategory'],
        'rate'=>$rowList['trainingRate'],
        'trainingDescription' => $rowList['trainingDescription'],
        'trainingDate' => $rowList['trainingDate'],
        'venue'=>$rowList['trainingGym'],
        'starttime'=>$rowList['trainingTime'],
        'trainingFacility'=>$rowList['trainingFacility'],
        'trainingMaxCapacity'=>$rowList['trainingMaxCapacity'],
        'trainingApprovalStatus'=>$rowList['trainingApprovalStatus']
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
