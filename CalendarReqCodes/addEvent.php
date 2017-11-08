<?php

require_once('../DBConfig.php');
session_start();

if (isset($_POST['add'])) {
    
     // VARIABLES
    $name = $_POST['trainerName'];
    $title = $_POST['trainingTitle'];
    $startdate = date('Y/m/d', strtotime($_POST['startDate']));
    $starttime = $_POST['startTime'];
    $eventtype = $_POST['eventtype'];
    $endtimestamp = strtotime($_POST['startTime']) + 60 * 60;
    $endtime = date('H:i', $endtimestamp);

    // RETRIEVE GYM NAME 
    $gymLocationQuery = mysqli_prepare($link, "SELECT gymName FROM gym WHERE id = ?");
    mysqli_stmt_bind_param($gymLocationQuery, "s", $gymLocation);
    $gymLocation = $_POST['gymLocation'];
    mysqli_stmt_execute($gymLocationQuery);
    mysqli_stmt_bind_result($gymLocationQuery, $ID);
    while ($gymLocationQuery->fetch()) {
        $venue = $ID;
    }

    // RETRIEVE Facility ID 
    $facilityQuery = mysqli_prepare($link, "SELECT id FROM gymfacility WHERE gymid = ? AND facilityName = ?");
    mysqli_stmt_bind_param($facilityQuery, "ss", $gymLocationID, $facilityName);
    $gymLocationID = $_POST['gymLocation'];
    $facilityName = $_POST['facility'];
    mysqli_stmt_execute($facilityQuery);
    mysqli_stmt_bind_result($facilityQuery, $ID);
    while ($facilityQuery->fetch()) {
        $facility = $ID;
    }

    if ($eventtype == "pt") {
        $rate = $_POST['rate'];
        
        // RETRIEVE TRAINING CATEGORY NAME
        $categoryQuery = mysqli_prepare($link, "SELECT TRAINING_NAME FROM trainingtype WHERE ID = ?");
        mysqli_stmt_bind_param($categoryQuery, "s", $category);
        $category = $_POST['trainingType'];
        mysqli_stmt_execute($categoryQuery);
        mysqli_stmt_bind_result($categoryQuery, $ID);
        while ($categoryQuery->fetch()) {
            $trainingCategory = $ID;
        }
    } else {
        $rate = "";
        $trainingCategory = "";
    }
    
    if($_POST['recurring'] == ""){
        $recur = "";
    }
    else{
        $recur = implode(",", $_POST['recurring']);
    }

    if (empty($_POST['endDate']) && $_POST['recurring'] == "") { //if no recur
        $enddate = $startdate;
    } else {
        $enddate = date('Y/m/d', strtotime($_POST['endDate']));
    }


    //Edited by Ching pin
    $sqlRows = "SELECT count(*) FROM trainerschedule where startdate = ? and starttime = ? ";
    $q = $bdd->prepare($sqlRows);

    if (($res = $q->execute(array($startdate, $starttime))) === TRUE) {
        $v = $q->fetchColumn();
    } else {
        echo 'failed';
    }

    if ($v > 10) {
        $msg = "Slots full";
        header("Location:../testFullCalendar.php?msg=$msg");
    } else {
        $sqlDuplicate = "SELECT * FROM trainerschedule where startdate = ? and starttime = ? and name = ? ";
        $q = $bdd->prepare($sqlDuplicate);
        $q->execute(array($startdate, $starttime, $name));
        $result = $q->fetchAll(PDO::FETCH_ASSOC);

        if ($result == TRUE) {
            $msg = "Duplicated slot";
            header("Location:../testFullCalendar.php?msg=$msg");
        } else {
            // INSERT TRAINING DETAILS
            $sql = "INSERT INTO trainerschedule(name, title, startdate, enddate,  rate, starttime, recur, eventtype, endtime, trainingCategory, facility, venue) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
            $query = $bdd->prepare($sql);
            if ($query == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $sth = $query->execute(array($name, $title, $startdate, $enddate, $rate, $starttime, $recur, $eventtype, $endtime, $trainingCategory, $facility, $venue));
            if ($sth == false) {
                print_r($query->errorInfo());
                die('error execute');
            }
            if($query->rowCount() == 1){
                echo '<script>';
                echo 'alert("Added to calendar successfully!");';
                echo '</script>';
                header('Location:../testFullCalendar.php');
            }

        }
    }
}
?>
