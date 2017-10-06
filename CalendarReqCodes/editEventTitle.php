<?php

require_once('../DBConfig.php');

if (isset($_POST['delete']) && isset($_POST['id'])) {

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
} elseif (isset($_POST['savechanges'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $date = $_POST['date'];
    $title = $_POST['title'];
    $color = $_POST['color'];
    $time = $_POST['startTime'];

    //get number of rows from the selected date and time
    $sqlRows = "SELECT count(*) FROM trainerschedule where startdate = ? and starttime = ? ";
    $q = $bdd->prepare($sqlRows);
    if (($res = $q->execute(array($date, $time))) === TRUE) {
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
        $q->execute(array($date, $time, $name));
        $result = $q->fetchAll(PDO::FETCH_ASSOC);
        if ($result == TRUE) {
            $msg = "Duplicated slot";
            header("Location:../testFullCalendar.php?msg=$msg");
        } else {
            $sql = "UPDATE trainerschedule SET startdate = '$date', title = '$title', color = '$color', starttime = '$time' WHERE trainingid = $id ";

            echo $sql;

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

//header('Location: index.php');
?>
