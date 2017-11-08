<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT g.gymName, f.facilityName, f.facilityDesc, f.facilityCapacity, f.id FROM gymfacility f INNER JOIN gym g ON f.gymid = g.id")or die(mysqli_error($con));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'id' => $rowList['id'],
        'gymName' => $rowList['gymName'],
        'facilityName' => $rowList['facilityName'],
        'facilityDesc' => $rowList['facilityDesc'],
        'facilityCapacity' => $rowList['facilityCapacity']
  
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
