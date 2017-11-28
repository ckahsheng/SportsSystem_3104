<?php

require_once '../DBConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    // Retrieve info from trainer
    $selectQuery = mysqli_query($link, "SELECT id FROM users WHERE userid = '" . $_SESSION['username'] . "'");
    $selectResult = mysqli_fetch_array($selectQuery);
}

$sqltran1 = mysqli_query($link, "SELECT id, userid, bondApprovalStatus FROM users WHERE bondApprovalStatus='Pending' AND bondWithTrainerId = '".$selectResult['id']."'")or die(mysqli_error($con));
$arrVal = array();
$i = 1;

while ($rowList1 = mysqli_fetch_array($sqltran1)) {

    $result = array(
        'num' => $i,
        'id' => $rowList1['id'],
        'traineeName' => $rowList1['userid'],
        'trainingApprovalStatus' => $rowList1['bondApprovalStatus']
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
