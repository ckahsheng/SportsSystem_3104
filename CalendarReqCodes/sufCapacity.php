<?php

require_once('../DBConfig.php');

$facility = intval($_POST['facility']);
$starttime = $_POST['starttime'];
$startdate = $_POST['startdate'] . " 00:00:00";
$userid = $_POST['userid'];

$sqlLocation = "SELECT count(*) FROM trainerschedule WHERE facility = '$facility' AND startdate = '$startdate' AND starttime = '$starttime' AND bookedTraineeId IS NOT NULL";
$queryLocation = $bdd->prepare($sqlLocation);
$queryLocation->execute();

$currentPairs = $queryLocation->fetchColumn();
// $currentPairs = 49;

$sqlGym = "SELECT facilityCapacity FROM gymfacility WHERE id = '$facility'";
$queryGym = $bdd->prepare($sqlGym);
$queryGym->execute();

$capacity = $queryGym->fetchAll();
foreach ($capacity as $cap) {
    $gymCapacity = $cap['facilityCapacity'] / 2;
}

// $gymCapacity = 1;

// TODO: echo the result back to the original page - testTrainerCalendar.php
if ($currentPairs >= $gymCapacity) {
    echo "nope"; // No space
} else {
    echo "have"; // Have space
}

$sqlDup = "SELECT trainingid FROM trainerschedule WHERE (name = '$userid' OR traineeid = '$userid') AND starttime = '$starttime' AND startdate = '$startdate'";
$queryDup = $bdd->prepare($sqlDup);
$queryDup->execute();

$duplicate = $queryDup->fetchAll();

if ($duplicate == true) {
    echo "-exists";
} else {
    echo "-free";
}
?>