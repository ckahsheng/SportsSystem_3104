<?php
require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT userid,role,created_at,emailAddress,phoneNumber,chargeRate FROM users WHERE role !='admin'")or die(mysqli_error($con));
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
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
