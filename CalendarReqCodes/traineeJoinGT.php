<?php 

require_once('../DBConfig.php');

if (isset($_POST['traineeId'])) {
    $trainerId = $_POST['trainerId'];
    $traineeId = $_POST['traineeId'];
    $gtId = $_POST['id'];
    $grpRecurId = $_POST['grpRecurId'];

    $sql = "INSERT INTO gttrainees (recurringId, username) VALUES ('$grpRecurId', '$traineeId')";
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
    
    

    $sqlTrainee = "SELECT count(*) FROM gttrainees WHERE recurringId = '$grpRecurId'";
    $queryTrainee = $bdd->prepare($sqlTrainee);
    $queryTrainee->execute();
    
    $traineeCount = $queryTrainee->fetchColumn();

    // echo $traineeCount;

    $update = "UPDATE grouptrainingschedule SET currentCap = $traineeCount WHERE GrpRecurrID = '$grpRecurId' ";
    $qryUpdate = $bdd->prepare($update);
    $qryUpdate->execute();

    echo "inserted";
}

?>