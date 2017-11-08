<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT id, gymCountry, gymLocation, gymName, gymOperatingHours FROM gym")or die(mysqli_error($con));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'id' => $rowList['id'],
        'gymName' => $rowList['gymName'],
        'gymLocation' => $rowList['gymLocation'],
        'gymCountry' => $rowList['gymCountry'],
        'gymOperatingHours' => $rowList['gymOperatingHours']
  
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
