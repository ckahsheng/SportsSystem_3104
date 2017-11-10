<?php

require_once "PHPMailer-master/PHPMailerAutoload.php";
require_once "DBConfig.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//Fetch credential of user who cancel the event
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$trainingId = $_POST['id'];


//If role is Trainer 
if ($role == "Trainer") {
    //Change Trainerschedule record to Cancelled
    $sql = "UPDATE trainerschedule SET trainingstatus='Cancelled' WHERE trainingid=$trainingId";
    $result = mysqli_query($link, $sql)
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count == 1) {
        echo "Record update successfully";
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
    //If a trainee exist, fetch trainee email address and send email to user to notify them that training has been cancelled 
    //Before we proceed, fetch details of the trainee 
    $sql = "SELECT users.emailAddress, trainerschedule.name,trainerschedule.title,trainerschedule.startdate,trainerschedule.starttime FROM users INNER JOIN trainerschedule ON users.userid = trainerschedule.traineeid WHERE trainerschedule.trainingid=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_trainingId);
        $param_trainingId = $trainingId;
        if (mysqli_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $traineeEmail, $trainerName, $trainingTitle, $trainingDate, $trainingTime);
            $stmt->fetch();
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                //Trainee found 
                echo $traineeEmail;
                //Send email to Trainee
                $mail = new PHPMailer;
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->CharSet = 'UTF-8';
                $mail->Host = "smtp.live.com";
                $mail->Subject = "Sports System -Training Session with $trainerName as has been Cancelled";
                $mail->SMTPAuth = true;
                $mail->Username = "LifeStyleSportsSystem@hotmail.com";
                $mail->Password = "LIU3104SHUANG";
                $mail->SMTPSecure = "tls";
                $mail->Port = 587;
                $mail->From = "LifeStyleSportsSystem@hotmail.com";
                $mail->FromName = "Sports Management System";
                $mail->addAddress($traineeEmail, "Training has been Cancelled");
                $mail->isHTML(true);
                $emailTextHtml = "<span>Dear <b>.$traineeEmail.</b></span>";
                $emailTextHtml .= "<table>";
                $emailTextHtml .= "</table>";
                $emailTextHtml .= "<p>Your training $trainingTitle on $trainingDate : $trainingTime has been cancelled ! </p>";
                $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
                $emailTextHtml .= "<br><p>Best regards </p>";
                $emailTextHtml .= "<p>LifeStyle Sports System </p>";
                $mail->Subject = "Trainer Cancelled Personal Training Session";
                $mail->Body = $emailTextHtml;
                $mail->AltBody = "This is the plain text version of the email content";

                if (!$mail->send()) {
                    $responseArray = array('type' => 'danger', 'message' => '');
                } else {
                    $responseArray = array('type' => 'success', 'message' => '');
                }
            } else {
                //No trainee- No point sending email
                echo "No Trainee found";
            }
        }
    }
    //Write to cancel table to log this record 
    $sql = "INSERT INTO cancelrecord ( trainingid, name,title,startdate,enddate,venue,starttime,endtime,rate,recur,eventType,trainingstatus,traineeid,rolewhodeletedrecord ) "
            . "SELECT trainerschedule.trainingid, trainerschedule.name, trainerschedule.title, trainerschedule.startdate, trainerschedule.enddate,trainerschedule.venue,trainerschedule.starttime,trainerschedule.endtime,trainerschedule.rate,trainerschedule.recur,trainerschedule.eventType,trainerschedule.trainingstatus,trainerschedule.traineeid,'Trainer' "
            . "FROM trainerschedule WHERE trainerschedule.trainingid = $trainingId";
    $result = mysqli_query($link, $sql) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
        echo "Updated";
        header('Location:testFullCalendar.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
}
//If user who cancel training is Trainee
else if ($role == "Trainee") {
    //If trainee cancel, set training session to Available as other trainees can still sign up for session 
    $sql = "UPDATE trainerschedule SET trainingstatus='Available', traineeid='' WHERE trainingid=$trainingId";
    $result = mysqli_query($link, $sql)
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count == 1) {
        echo "Record update successfully";
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
    //Send email to trainer 
    $sql = "SELECT users.emailAddress, users.userid,trainerschedule.title,trainerschedule.startdate,trainerschedule.starttime FROM users INNER JOIN trainerschedule ON users.userid = trainerschedule.name WHERE trainerschedule.trainingid=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_trainingId);
        $param_trainingId = $trainingId;
        if (mysqli_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $trainerEmail, $traineeName, $trainingTitle, $trainingDate, $trainingTime);
            $stmt->fetch();
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                //Trainee found 
                echo $trainerEmail;
                //Send email to Trainee
                $mail = new PHPMailer;
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->CharSet = 'UTF-8';
                $mail->Host = "smtp.live.com";
                $mail->Subject = "Sports System -Training Session with $traineeName as has been Cancelled";
                $mail->SMTPAuth = true;
                $mail->Username = "LifeStyleSportsSystem@hotmail.com";
                $mail->Password = "LIU3104SHUANG";
                $mail->SMTPSecure = "tls";
                $mail->Port = 587;
                $mail->From = "LifeStyleSportsSystem@hotmail.com";
                $mail->FromName = "Sports Management System";
                $mail->addAddress($trainerEmail, "Trainee has Cancelled Training");
                $mail->isHTML(true);
                $emailTextHtml = "<span>Dear  <b>.$trainerEmail.</b></span>";
                $emailTextHtml .= "<table>";
                $emailTextHtml .= "</table>";
                $emailTextHtml .= "<p>Your training $trainingTitle on $trainingDate : $trainingTime has been cancelled ! </p>";
                $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
                $emailTextHtml .= "<br><p>Best regards </p>";
                $emailTextHtml .= "<p>LifeStyle Sports System </p>";
                $mail->Subject = "Trainee Cancelled Personal Training Session";
                $mail->Body = $emailTextHtml;
                $mail->AltBody = "This is the plain text version of the email content";

                if (!$mail->send()) {
                    $responseArray = array('type' => 'danger', 'message' => '');
                } else {
                    $responseArray = array('type' => 'success', 'message' => '');
                }
            } else {
                //No trainee- No point sending email
                echo "No Trainee found";
            }
        }
    }

   $sql = "INSERT INTO cancelrecord ( trainingid, name,title,startdate,enddate,venue,starttime,endtime,rate,recur,eventType,trainingstatus,traineeid,rolewhodeletedrecord ) "
            . "SELECT trainerschedule.trainingid, trainerschedule.name, trainerschedule.title, trainerschedule.startdate, trainerschedule.enddate,trainerschedule.venue,trainerschedule.starttime,trainerschedule.endtime,trainerschedule.rate,trainerschedule.recur,trainerschedule.eventType,'Cancelled',trainerschedule.traineeid,'Trainee' "
            . "FROM trainerschedule WHERE trainerschedule.trainingid = $trainingId";
    $result = mysqli_query($link, $sql) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
        echo "Updated";
        header('Location:testFullCalendar.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
    }
}





























