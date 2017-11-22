<?php

require_once('../DBConfig.php');

// DELETE FUNCTION 
if (isset($_POST['delete'])) {

    $id = $_POST['id'];

    $sql = "DELETE FROM trainerschedule WHERE trainingid = $id";
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
    header('Location:../testFullCalendar.php');
}
// EDIT FUNCTION
elseif (isset($_POST['savechanges'])) {

    if ($_POST['editEventType'] == "Personal Training") { //PT
        $rate = $_POST['editRate'];
        $eventtype = "pt";
        // RETRIEVE TRAINING CATEGORY NAME
        $categoryQuery = mysqli_prepare($link, "SELECT TRAINING_NAME FROM trainingtype WHERE ID = ?");
        mysqli_stmt_bind_param($categoryQuery, "s", $category);
        $category = $_POST['editTrainingType'];
        mysqli_stmt_execute($categoryQuery);
        mysqli_stmt_bind_result($categoryQuery, $ID);
        while ($categoryQuery->fetch()) {
            $trainingCategory = $ID;
        }

        // RETRIEVE GYM NAME 
        $gymLocationQuery = mysqli_prepare($link, "SELECT gymName FROM gym WHERE id = ?");
        mysqli_stmt_bind_param($gymLocationQuery, "s", $gymLocation);
        $gymLocation = $_POST['editGymLocation'];
        mysqli_stmt_execute($gymLocationQuery);
        mysqli_stmt_bind_result($gymLocationQuery, $ID);
        while ($gymLocationQuery->fetch()) {
            $venue = $ID;
        }

        // RETRIEVE Facility ID 
        $facilityQuery = mysqli_prepare($link, "SELECT id FROM gymfacility WHERE gymid = ? AND facilityName = ?");
        mysqli_stmt_bind_param($facilityQuery, "ss", $gymLocationID, $facilityName);
        $gymLocationID = $_POST['editGymLocation'];
        $facilityName = $_POST['editFacility'];
        mysqli_stmt_execute($facilityQuery);
        mysqli_stmt_bind_result($facilityQuery, $ID);
        while ($facilityQuery->fetch()) {
            $facility = $ID;
        }
        $id = $_POST['id'];
        $name = $_POST['editName'];
        $title = $_POST['editTitle'];
        $startdate = date('Y/m/d', strtotime($_POST['editStartDate']));
        $starttime = $_POST['editStartTime'];
        $endtimestamp = strtotime($_POST['editStartTime']) + 60 * 60;
        $endtime = date('H:i', $endtimestamp);
    } 
    else if ($_POST['editEventType'] == "Own Training"){ //OT
        $id = $_POST['id'];
        $name = $_POST['editName'];
        $title = $_POST['editTitle'];
        $rate = "";
        $trainingCategory = "";
        $facility = "";
        $venue = "";
        $eventtype = "ot";
        $startdate = date('Y/m/d', strtotime($_POST['editStartDate']));
        $starttime = $_POST['editStartTime'];
        $endtimestamp = strtotime($_POST['editStartTime']) + 60 * 60;
        $endtime = date('H:i', $endtimestamp);
    } 
    else if ($_POST['editEventType'] == "Group Training"){ //GT
        $id = $_POST['id'];
        $title = $_POST['editTitle'];
        $description = $_POST['editDescription'];
        
        $startdate = date('Y/m/d', strtotime($_POST['editStartDate']));
        $starttime = "";
        $name = $_POST['editName'];
        if(empty($_POST['editRecurring'])){
            $recur = "";
        }
        else{
            $recur = implode(",", $_POST['editRecurring']);
        }
        
    }

    //check for duplicate
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
        // UPDATE TRAINING DETAILS
        if ($_POST['editEventType'] == "Personal Training" || $_POST['editEventType'] == "Own Training") {
            $sql = "UPDATE trainerschedule SET name = ?, title = ?, startdate = ?, venue = ?, starttime = ?, endtime = ?, rate = ?, eventType = ?, trainingCategory = ?, facility = ?  WHERE trainingid = ? ";

            $query = $bdd->prepare($sql);
            if ($query == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $sth = $query->execute(array($name, $title, $startdate, $venue, $starttime, $endtime, $rate, $eventtype, $trainingCategory, $facility, $id));
            if ($sth == false) {
                print_r($query->errorInfo());
                die('error execute');
            }
            if ($query->rowCount() == 1) {
                echo '<script>';
                echo 'alert("Updated to calendar successfully!");';
                echo 'window.location = "../testFullCalendar.php";';
                echo '</script>';
            }
        }
         
        else if ($_POST['editEventType'] == "Group Training" && $recur != ""){
            $GTSql = "UPDATE grouptrainings GT INNER JOIN grouptrainingschedule GTS ON GT.ID = GTS.GrpRecurrID SET title = ? WHERE GT.recurring = ? AND GTS.id = ?";

            $GTquery = $bdd->prepare($GTSql);
            if ($GTquery == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $GTsth = $GTquery->execute(array($title, $recur, $id));
            if ($GTsth == false) {
                print_r($GTquery->errorInfo());
                die('error execute');
            }

            // To retrieve the GrpRecurrID
            $recurQuery = mysqli_prepare($link, "SELECT GrpRecurrID FROM grouptrainingschedule WHERE id = ?");
            mysqli_stmt_bind_param($recurQuery, "i", $id);
            mysqli_stmt_execute($recurQuery);
            $recurResult = $recurQuery->get_result();
            $recurring = mysqli_fetch_assoc($recurResult);
            
            // Update the records based on GrpRecurrID
            $GTSSql = "UPDATE grouptrainingschedule GTS INNER JOIN grouptrainings GT ON GT.ID = GTS.GrpRecurrID SET trainingTitle = ?, trainingDescription = ?  WHERE GT.ID = ?";
            $GTSquery = $bdd->prepare($GTSSql);
            if ($GTSquery == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $GTSsth = $GTSquery->execute(array($title, $description, $recurring['GrpRecurrID']));
            if ($GTSsth == false) {
                print_r($GTSquery->errorInfo());
                die('error execute');
            }

            if ($GTquery->rowCount() == 1 && $GTSquery->rowCount() > 1 ) {
                echo '<script>';
                echo 'alert("Updated to calendar successfully2!");';
                echo 'window.location = "../testFullCalendar.php";';
                echo '</script>';
            }       
        }
        else if ($_POST['editEventType'] == "Group Training" && $recur == ""){
            $GTSSql = "UPDATE grouptrainingschedule SET trainingTitle = ?, trainingDescription = ?  WHERE id = ? ";

            $GTSquery = $bdd->prepare($GTSSql);
            if ($GTSquery == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $GTSsth = $GTSquery->execute(array($title, $description, $id));
            if ($GTSsth == false) {
                print_r($GTSquery->errorInfo());
                die('error execute');
            }
            if ($GTSquery->rowCount() == 1) {
                echo '<script>';
                echo 'alert("Updated to calendar successfully1!");';
                echo 'window.location = "../testFullCalendar.php";';
                echo '</script>';
            }       
        }
    }
}
?>
