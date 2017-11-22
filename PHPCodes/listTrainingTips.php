<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT trainingTipsId, trainingTipsType, trainingTipsDesc FROM trainingtips")or die(mysqli_error($con));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'trainingTipsId' => $rowList['trainingTipsId'],
        'trainingTipsType' => $rowList['trainingTipsType'],
        'trainingTipsDesc' => $rowList['trainingTipsDesc']
  
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
