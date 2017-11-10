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

    // VARIABLES
    $id = $_POST['id'];
    $name = $_POST['editName'];
    $title = $_POST['editTitle'];
    $startdate = date('Y/m/d', strtotime($_POST['editStartDate']));
    $starttime = $_POST['editStartTime'];
    $endtimestamp = strtotime($_POST['editStartTime']) + 60 * 60;
    $endtime = date('H:i', $endtimestamp);
    $recur = implode(",", $_POST['editRecurring']);
//    $venue = $_POST['editGymLocation'];
    // END DATE
    if ($_POST['editEndDate'] == $startdate && $recur == "") { //if no recur
        $enddate = $startdate;
    } else {
        $enddate = date('Y/m/d', strtotime($_POST['editEndDate']));
    }

    // RATE AND TRAINING CATEGORY
    if ($_POST['editEventType'] == "Personal Training") {
        $rate = $_POST['editRate'];
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
        
    } else {
        $rate = "";
        $trainingCategory = "";
        $facility = "";
        $venue = "";
    }

    // EVENT TYPE
    if ($_POST['editEventType'] == "Personal Training") {
        $eventtype = "pt";
    } else {
        $eventtype = "ot";
    }

    //get number of rows from the selected date and time
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
        //check for duplicate
        $sqlDuplicate = "SELECT * FROM trainerschedule where startdate = ? and starttime = ? and name = ? ";
        $q = $bdd->prepare($sqlDuplicate);
        $q->execute(array($startdate, $starttime, $name));
        $result = $q->fetchAll(PDO::FETCH_ASSOC);

        if ($result == TRUE) {
            $msg = "Duplicated slot";
            header("Location:../testFullCalendar.php?msg=$msg");
        } else {
            // UPDATE TRAINING DETAILS
            $sql = "UPDATE trainerschedule SET name = ?, title = ?, startdate = ?, enddate = ?, venue = ?, starttime = ?, endtime = ?, rate = ?, recur = ?, eventType = ?, trainingCategory = ?, facility = ?  WHERE trainingid = ? ";

            $query = $bdd->prepare($sql);
            if ($query == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $sth = $query->execute(array($name, $title, $startdate, $enddate, $venue, $starttime, $endtime, $rate, $recur, $eventtype, $trainingCategory, $facility, $id));
            if ($sth == false) {
                print_r($query->errorInfo());
                die('error execute');
            }
            header('Location:../testFullCalendar.php');
        }
    }
}

//header('Location: index.php');
?>
