<?php

require_once '../DBConfig.php';

$sqltran = mysqli_query($link, "SELECT * FROM grouptrainings WHERE trainingApprovalStatus='Pending'")or die(mysqli_error($con));
$arrVal = array();
$i = 1;

while ($rowList = mysqli_fetch_array($sqltran)) {

    $unavail = "";
    $recurrDate = "";
    if ($rowList['dateUnavailable'] == "") {
        $unavail = "Available for All Dates";
    } else {
        $unavail = '<b>Certain Dates Are Not available</b>';
    }

    if ($rowList['recurring'] == "") {
        $recurrDate = "Only One Session";
    } else if ($rowList['recurring'] == "1") {
        $recurrDate = 'Every Monday';
    } else if ($rowList['recurring'] == "2") {
        $recurrDate = 'Every Tuesday';
    } else if ($rowList['recurring'] == "3") {
        $recurrDate = 'Every Wednesday';
    } else if ($rowList['recurring'] == "4") {
        $recurrDate = 'Every Thursday';
    } else if ($rowList['recurring'] == "5") {
        $recurrDate = 'Every Monday';
    } else if ($rowList['recurring'] == "6") {
        $recurrDate = 'Every Saturday';
    } else if ($rowList['recurring'] == "7") {
        $recurrDate = 'Every Sunday';
    }




    $result = array(
        'num' => $i,
        'groupId' => $rowList['ID'],
        'trainerName' => $rowList['trainername'],
        'title' => $rowList['title'],
        'trainingCategory' => $rowList['trainingcategory'],
        'rate' => $rowList['trainingrate'],
        'trainingSDate' => $rowList['trainingstartdate'],
        'trainingEDate' => $rowList['trainingenddate'],
        'venue' => $rowList['trainingGym'],
        'starttime' => $rowList['trainingtime'],
        'trainingFacility' => $rowList['trainingFacility'],
        'trainingMaxCapacity' => $rowList['trainingcapacity'],
        'recurring' => $recurrDate,
        'dateUnavailable' => $unavail,
        'trainingApprovalStatus' => $rowList['trainingApprovalStatus']
    );
    array_push($arrVal, $result);
    $i++;
}
echo json_encode($arrVal);
mysqli_close($link);
?>   
