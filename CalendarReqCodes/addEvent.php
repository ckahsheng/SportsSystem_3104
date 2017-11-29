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
    $datesToStoreRecurring = array();

    // CHECKED PT
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
        
    } else { // OT
        $rate = "";
        $trainingCategory = "";
        $venue = "";
        $facility = "";
    }

    // RECURRING
    if (empty($_POST['recurring']) && empty($_POST['endDate'])) {
        $recur = "";
        $enddate = $startdate;
    } else {
        $recur = implode(",", $_POST['recurring']);
        $enddate = date('Y/m/d', strtotime($_POST['endDate']));
        $start = new DateTime($startdate);
        $end = new DateTime(date('Y/m/d', strtotime($_POST['endDate'])));
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $date) {
            if (in_array($date->format('N'), $_POST['recurring'])) {
                $result = $date->format('Y-m-d');
                //Means this are the dates we have to store into the database as well 
                //Store into arrraylist
                array_push($datesToStoreRecurring, $result);
                //do something for Monday, Wednesday and Friday
            }
        }
    }

    //Edited by Ching pin
//    $sqlRows = "SELECT count(*) FROM trainerschedule where startdate = ? and starttime = ? ";
//    $q = $bdd->prepare($sqlRows);
//
//    if (($res = $q->execute(array($startdate, $starttime))) === TRUE) {
//        $v = $q->fetchColumn();
//    } else {
//        echo 'failed';
//    }
//
//    if ($v > 10) {
//        $msg = "Slots full";
//        header("Location:../testFullCalendar.php?msg=$msg");
//    } else {
    
    // Compare start date and end date. start date cannot be bigger than end date.
    if($startdate > $enddate){
        echo '<script>';
        echo 'alert("Start date cannot be greater than end date!");';
        echo 'window.location = "../testFullCalendar.php";';
        echo '</script>';
    }
    else{
        $sqlDuplicate = "select * from ( "
                    . "select trainerschedule.trainingid,trainerschedule.startdate as 'StartDate', trainerschedule.starttime as 'StartTime',trainerschedule.name as 'TrainerName' from trainerschedule "
                    . "union all "
                    . "select grouptrainingschedule.trainerid,grouptrainingschedule.trainingDate AS 'StartDate', grouptrainingschedule.trainingTime as 'StartTime',grouptrainingschedule.trainerName as 'TrainerName' from grouptrainingschedule ) "
                    . "a "
                    . "WHERE StartDate=? AND StartTime=? AND TrainerName=?";
        $q = $bdd->prepare($sqlDuplicate);
        $q->execute(array($startdate, $starttime, $name));
        $result = $q->fetchAll(PDO::FETCH_ASSOC);

        if ($result == TRUE) {
            echo '<script>';
            echo 'alert("Duplicated slot");';
            echo 'window.location = "../testFullCalendar.php";';
            echo '</script>';
        } else {
            // INSERT TRAINING DETAILS
            if (sizeof($datesToStoreRecurring) > 0) { // INSERT WITH RECURRING
                 foreach ($datesToStoreRecurring as $value) {
                    $sql = "INSERT INTO trainerschedule(name, title, startdate, enddate,  rate, starttime, recur, eventtype, endtime, trainingCategory, facility, venue) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
                    $query = $bdd->prepare($sql);
                    if ($query == false) {
                        print_r($bdd->errorInfo());
                        die('error preparing');
                    }
                    $sth = $query->execute(array($name, $title, date('Y-m-d', strtotime($value)), date('Y-m-d', strtotime($value)), $rate, $starttime, $recur, $eventtype, $endtime, $trainingCategory, $facility, $venue));
                    if ($sth == false) {
                        print_r($query->errorInfo());
                        die('error execute');
                    }
                    if ($query->rowCount() == 1) {
                        echo '<script>';
                        echo 'alert("Added to calendar successfully!");';
                        echo 'window.location = "../testFullCalendar.php";';
                        echo '</script>';
                    }
                 }
                
            }
            else{ // INSERT NO RECURRING
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
                if ($query->rowCount() == 1) {
                    echo '<script>';
                    echo 'alert("Added to calendar successfully!");';
                    echo 'window.location = "../testFullCalendar.php";';
                    echo '</script>';
                }
            }
        }
    }
}
?>
