<?php
require_once('../DBConfig.php');
session_start();
//if (isset($_POST['trainerName']) && isset($_POST['trainingTitle']) && isset($_POST['startDate']) && isset($_POST['endDate']) && isset($_POST['color']) && isset($_POST['venue'])){
if (isset($_POST['add'])) {

    $name = $_POST['trainerName'];
    $title = $_POST['trainingTitle'];
    $startdate = $_POST['startDate'];
    $enddate = $_POST['endDate'];
    $color = $_POST['color'];
    $venue = $_POST['venue'];
    $rate = $_POST['rate'];
    $starttime = $_POST['startTime'];
    $recur = implode(",", $_POST['recurring']);

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
            $sql = "INSERT INTO trainerschedule(name, title, startdate, enddate, color, venue, rate, starttime, recur) values ('$name', '$title', '$startdate', '$enddate', '$color', '$venue', '$rate', '$starttime', '$recur')";
            //$req = $bdd->prepare($sql);
            //$req->execute();

            $query = $bdd->prepare($sql);
            if ($query == false) {
                print_r($bdd->errorInfo());
                die('error preparing');
            }
            $sth = $query->execute();
            if ($sth == false) {
                print_r($query->errorInfo());
                die('error execute');
            }
            header('Location:../testFullCalendar.php');
        }
    }
}
?>
