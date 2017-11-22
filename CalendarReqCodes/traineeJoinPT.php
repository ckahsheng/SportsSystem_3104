<?php

require_once('../DBConfig.php');

if (isset($_POST['traineeId'])) {
    $traineeId = $_POST['traineeId'];
    $ptId = $_POST['id'];
    $trainerId = $_POST['trainerId'];

    // echo "\nTrainee Id: " . $traineeId;
    // echo "\nPT Id: " . $ptId;
    // echo "\nTrainer Id: " . $trainerId;

    $sql = "UPDATE trainerschedule SET traineeid = '$traineeId', trainingstatus = 'Assigned' WHERE trainingid = $ptId ";
    $query = $bdd->prepare($sql);

    if ($query == false) {
        print_r($bdd->errorInfo());
        die('Error prepare');
    }

    $res = $query->execute();

    if ($res == false) {
        print_r($query->errorInfo());
        die('Error execute');
    }
    
    echo "OK";
}

?>