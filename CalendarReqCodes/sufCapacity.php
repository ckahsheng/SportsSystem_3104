<?php

require_once('../DBConfig.php');

$venue = intval($_POST['venue']);
$starttime = $_POST['starttime'];
$startdate = $_POST['startdate'] . " 00:00:00";

$sqlLocation = "SELECT count(*) FROM trainerschedule WHERE venue = '$venue' AND startdate = '$startdate' AND starttime = '$starttime' AND bookedTraineeId IS NOT NULL";
$queryLocation = $bdd->prepare($sqlLocation);
$queryLocation->execute();

$currentPairs = $queryLocation->fetchColumn();
// $currentPairs = 49;

$sqlGym = "SELECT facilityCapacity FROM gymfacility WHERE id = '$venue'";
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
?>