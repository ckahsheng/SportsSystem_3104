<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT ID, TRAINING_NAME, TRAINING_RATE FROM trainingtype")or die(mysqli_error($con));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'ID' => $rowList['ID'],
        'TRAINING_NAME' => $rowList['TRAINING_NAME'],
        'TRAINING_RATE' => $rowList['TRAINING_RATE']
  
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
