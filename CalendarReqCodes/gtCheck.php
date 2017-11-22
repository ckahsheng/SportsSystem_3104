<?php 

// retrieve data from POST
require_once('../DBConfig.php');

$trainerId = $_POST['trainerId'];
$traineeId = $_POST['traineeId'];
$gtId = $_POST['id'];

// check for sufficient space for training (maximum capacity - current signed up numbers)

// retrieve max capacity and recurring id of training by id (gtId)
$sql = "SELECT trainingMaxCapacity, GrpRecurrID FROM grouptrainingschedule WHERE id = '$gtId'";
$query = $bdd->prepare($sql);
$query->execute();
$row = $query->fetch();

$capacity = $row[0]; // max capacity
$grpRecurId = $row[1]; // group recurring id


// get count of trainees tagged with recurring id
$sqlTrainee = "SELECT count(*) FROM gttrainees WHERE recurringId = '$grpRecurId'";
$queryTrainee = $bdd->prepare($sqlTrainee);
$queryTrainee->execute();

$traineeCount = $queryTrainee->fetchColumn(); // number of trainees that signed up for the same group training


// then compare, and return appropriate values - below under the 'RETURNING HERE' comment


// check if the trainee has already joined this session
$sqlExists = "SELECT count(*) FROM gttrainees WHERE username = '$traineeId' AND recurringId = '$grpRecurId'";
$queryExists = $bdd->prepare($sqlExists);
$queryExists->execute();

$traineeExists = $queryExists->fetchColumn();

// RETURNING HERE
if ($traineeExists == 0) { // trainee haven't joined

    // comparing the max capacity of the training against current numbers
    if ($traineeCount >= $capacity) {
        echo 'full';
    } else if ($traineeCount < $capacity) {
        echo 'free';
    }

} else if ($traineeExists >= 1) { // trainee alr joined
    echo 'exists';
}


?>