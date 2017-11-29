<?php

require_once '../DBConfig.php';
require_once "../PHPMailer-master/PHPMailerAutoload.php";
date_default_timezone_set('Asia/Singapore');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    // Retrieve info from trainer
    $selectQuery = mysqli_query($link, "SELECT id, userid FROM users WHERE userid = '" . $_SESSION['username'] . "'");
    $selectResult = mysqli_fetch_array($selectQuery);
}

$traineeid = $_POST['id'];

// Retrieve info from trainees
$bondedTraineeQuery = mysqli_query($link, "SELECT emailAddress, userid FROM users WHERE id = '$traineeid'");
$bondTraineeResult = mysqli_fetch_array($bondedTraineeQuery);

//update status for trainee
$updateQuery = mysqli_query($link, "UPDATE users SET bondApprovalStatus ='Approved' WHERE id='$traineeid'");
$row = mysqli_affected_rows($link);

// the date after 2 days
$currentDate = date("Y-m-d", strtotime("+2 days"));
$groupTrainers = array();
if ($row == 1) {
    $retrieveNBTrainerSchedule = mysqli_query($link, "SELECT userid, emailAddress, DATE_FORMAT(startdate, '%d-%m-%Y') AS startdate FROM users U INNER JOIN trainerschedule TS ON TS.name = U.userid WHERE traineeid = '" . $bondTraineeResult['userid'] . "' AND id <> '" . $selectResult['id'] . "' AND startdate >= '" . $currentDate . "'");
    $updateQuery1 = mysqli_query($link, "UPDATE trainerschedule TS INNER JOIN users U ON TS.name = U.userid SET traineeid ='" . NULL . "' WHERE traineeid = '" . $bondTraineeResult['userid'] . "' AND id <> '" . $selectResult['id'] . "' AND startdate >= '" . $currentDate . "'");
    echo '<script language="javascript">';
    echo 'alert("Bonded Sucessfully!");';
    echo 'window.location.reload(history.go(-1));';
    echo '</script>';

    // group trainer name and email address to their start date of training session.
    while ($row = mysqli_fetch_assoc($retrieveNBTrainerSchedule)) {
        $groupTrainers[$row['userid'] . "^" . $row['emailAddress']][] = $row;
    }
//    echo '<pre>'; print_r($groupTrainers); echo '</pre>';
    // send email to non-bonded trainers to notify the cancellation of training
    foreach ($groupTrainers as $trainers => $values):
        // split trainer and name. 
        $trainerNE = explode("^", $trainers);

        $mail = new PHPMailer;
        $mail->SMTPDebug = 0;    //Enable SMTP debugging. 
        $mail->isSMTP();        //Set PHPMailer to use SMTP.
        $mail->CharSet = 'UTF-8';
        $mail->Host = "smtp.live.com";  //Set SMTP host name   
        $mail->SMTPAuth = true;     //Set this to true if SMTP host requires authentication to send email
        //Provide username and password     
        $mail->Username = "LifeStyleSportsSystem@hotmail.com";
        $mail->Password = "LIU3104SHUANG3104";
        $mail->SMTPSecure = "tls";  //If SMTP requires TLS encryption then set it
        $mail->Port = 587;          //Set TCP port to connect to 
        $mail->From = "LifeStyleSportsSystem@hotmail.com";
        $mail->FromName = "Sports Management System";
        $mail->addAddress($trainerNE[1]);
        $mail->isHTML(true);
        $emailTextHtml = "<span>Dear <b>" . $trainerNE[0] . "</b></span>";
        $emailTextHtml .= "<p>" . $bondTraineeResult['userid'] . " has cancelled his/her personal training session with you.</p>";
        $emailTextHtml .= "<p>The date(s) of training session is/are <br/>";

        foreach ($values as $trainingDates) {
            $emailTextHtml .= $trainingDates['startdate'] . "<br/>";
        }

        $emailTextHtml .= "</p>";
        $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
        $emailTextHtml .= "<p>Best regards </p>";
        $emailTextHtml .= "<p>LifeStyle Sports System </p>";

        $mail->Subject = "Sports System - Personal Training Cancelled";
        $mail->Body = $emailTextHtml;

        if (!$mail->send()) {
            $responseArray = array('type' => 'danger', 'message' => '');
            echo '<script language="javascript">';
            echo 'alert("Something went wrong. Please try again later.Email not sent out");';
            echo 'window.location.reload(history.go(-1));';
            echo '</script>';
        } else {
            $responseArray = array('type' => 'success', 'message' => '');
        }
    endforeach;

    // send email to trainees to notify that he/she has been boneded 
    // and the cancellation of training with non-bonded trainers 
    if (empty($groupTrainers)) { //if trainee got training session with non-bonded trainers
        $mail = new PHPMailer;
        $mail->SMTPDebug = 0;    //Enable SMTP debugging. 
        $mail->isSMTP();        //Set PHPMailer to use SMTP.
        $mail->CharSet = 'UTF-8';
        $mail->Host = "smtp.live.com";  //Set SMTP host name   
        $mail->SMTPAuth = true;     //Set this to true if SMTP host requires authentication to send email
        //Provide username and password     
        $mail->Username = "LifeStyleSportsSystem@hotmail.com";
        $mail->Password = "LIU3104SHUANG3104";
        $mail->SMTPSecure = "tls";  //If SMTP requires TLS encryption then set it
        $mail->Port = 587;          //Set TCP port to connect to 
        $mail->From = "LifeStyleSportsSystem@hotmail.com";
        $mail->FromName = "Sports Management System";
        $mail->addAddress($bondTraineeResult['emailAddress']);
        $mail->isHTML(true);
        $emailTextHtml = "<span>Dear <b>" . $bondTraineeResult['userid'] . "</b></span>";
        $emailTextHtml .= "<p>You are successfully bonded with " . $selectResult['userid'] . " now. </p>";
        $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
        $emailTextHtml .= "<p>Best regards </p>";
        $emailTextHtml .= "<p>LifeStyle Sports System </p>";

        $mail->Subject = "Sports System - Bonded with a Trainer";
        $mail->Body = $emailTextHtml;

        if (!$mail->send()) {
            $responseArray = array('type' => 'danger', 'message' => '');
            echo '<script language="javascript">';
            echo 'alert("Something went wrong. Please try again later.Email not sent out");';
            echo 'window.location.reload(history.go(-1));';
            echo '</script>';
        } else {
            $responseArray = array('type' => 'success', 'message' => '');
        }
    } else { // send email to trainees to notify that he/she has been boneded 
        foreach ($groupTrainers as $trainers => $values):
            // split trainer and name. 
            $trainerNE = explode("^", $trainers);
            $mail = new PHPMailer;
            $mail->SMTPDebug = 0;    //Enable SMTP debugging. 
            $mail->isSMTP();        //Set PHPMailer to use SMTP.
            $mail->CharSet = 'UTF-8';
            $mail->Host = "smtp.live.com";  //Set SMTP host name   
            $mail->SMTPAuth = true;     //Set this to true if SMTP host requires authentication to send email
            //Provide username and password     
            $mail->Username = "LifeStyleSportsSystem@hotmail.com";
            $mail->Password = "LIU3104SHUANG3104";
            $mail->SMTPSecure = "tls";  //If SMTP requires TLS encryption then set it
            $mail->Port = 587;          //Set TCP port to connect to 
            $mail->From = "LifeStyleSportsSystem@hotmail.com";
            $mail->FromName = "Sports Management System";
            $mail->addAddress($bondTraineeResult['emailAddress']);
            $mail->isHTML(true);
            $emailTextHtml = "<span>Dear <b>" . $bondTraineeResult['userid'] . "</b></span>";
            $emailTextHtml .= "<p>You are successfully bonded with " . $selectResult['userid'] . " now. </p>";
            $emailTextHtml .= "<p>Please take note the follow training session(s) that are not bonded with " . $selectResult['userid'] . " has been cancelled.</p>";
            $emailTextHtml .= "<p>The date(s) of training session is/are <br/>";

            foreach ($values as $trainingDates) {
                $emailTextHtml .= $trainingDates['startdate'] . ", by " . $trainerNE[0] . "<br/>";
            }

            $emailTextHtml .= "</p>";
            $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
            $emailTextHtml .= "<p>Best regards </p>";
            $emailTextHtml .= "<p>LifeStyle Sports System </p>";

            $mail->Subject = "Sports System - Bonded with a Trainer";
            $mail->Body = $emailTextHtml;

            if (!$mail->send()) {
                $responseArray = array('type' => 'danger', 'message' => '');
                echo '<script language="javascript">';
                echo 'alert("Something went wrong. Please try again later.Email not sent out");';
                echo 'window.location.reload(history.go(-1));';
                echo '</script>';
            } else {
                $responseArray = array('type' => 'success', 'message' => '');
            }
        endforeach;
    }
} else {
    echo '<script language="javascript">';
    echo 'alert("Something went wrong. Please try again later.");';
    echo 'window.location.reload(history.go(-1));';
    echo '</script>';
}
?>