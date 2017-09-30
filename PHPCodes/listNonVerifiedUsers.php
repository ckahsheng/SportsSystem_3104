<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT userid,role,created_at,emailAddress,phoneNumber,chargeRate,verified,description FROM users WHERE role !='admin' && verified='Not Verified'")or die(mysqli_error($con));
$arrVal = array();
$i = 1;
while ($rowList = mysqli_fetch_array($sqltran)) {

    $result = array(
        'num' => $i,
        'userid' => $rowList['userid'],
        'role' => $rowList['role'],
        'created' => $rowList['created_at'],
        'email' => $rowList['emailAddress'],
        'phoneNumber' => $rowList['phoneNumber'],
        'rate' => $rowList['chargeRate'],
        'description'=>$rowList['description'],
        'verified'=>$rowList['verified']
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
