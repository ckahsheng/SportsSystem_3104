<?php

require_once '../DBConfig.php';
require_once "../PHPMailer-master/PHPMailerAutoload.php";
date_default_timezone_set('Asia/Singapore');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    // Retrieve info from trainees
    $selectQuery = mysqli_query($link, "SELECT id, bondWithTrainerId, userid FROM users WHERE userid = '" . $_SESSION['username'] . "'");
    $selectResult = mysqli_fetch_array($selectQuery);
}

// Retrieve info from trainers
$bondedTrainerQuery = mysqli_query($link, "SELECT emailAddress, userid FROM users WHERE id = '" . $selectResult['bondWithTrainerId'] . "'");
$bondTrainerResult = mysqli_fetch_array($bondedTrainerQuery);

// the date after 2 days
$currentDate = date("Y-m-d", strtotime("+2 days"));

if (isset($_POST['bond'])) {
    $updateQuery = mysqli_query($link, "UPDATE users SET bondWithTrainerId ='" . $_POST['trainerId'] . "', bondApprovalStatus = 'Pending' WHERE id='" . $selectResult['id'] . "'");
    echo '<script language="javascript">';
    echo 'alert("Wait for bonding approval! Please check your profile for the bond status.");';
    echo 'window.location.reload(history.go(-1));';
    echo '</script>';
//    $updateQuery = mysqli_query($link, "UPDATE users SET bondWithTrainerId ='" . $_POST['trainerId'] . "' WHERE id='" . $selectResult['id'] . "'");
    
//    $row = mysqli_affected_rows($link);
//    if ($row == 1) {       
//        $retrieveTraineeSchedule = mysqli_query($link, "SELECT userid, emailAddress, DATE_FORMAT(startdate, '%d-%m-%Y') AS startdate FROM users U INNER JOIN trainerschedule TS ON TS.name = U.userid WHERE traineeid = '" . $_SESSION['username'] . "' AND id <> '".$_POST['trainerId']."' AND startdate >= '".$currentDate ."'");
//        $updateQuery1 = mysqli_query($link, "UPDATE trainerschedule TS INNER JOIN users U ON TS.name = U.userid SET traineeid ='".NULL."' WHERE traineeid = '" . $_SESSION['username'] . "' AND id <> '".$_POST['trainerId']."' AND startdate >= '".$currentDate ."'");
//        echo '<script language="javascript">';
//        echo 'alert("Bonded Sucessfully!");';
//        echo 'window.location.reload(history.go(-1));';
//        echo '</script>';
//        
//        $groupTrainers = array();
//        // group trainer name and email address to their start date of training session.
//        while ($row = mysqli_fetch_assoc($retrieveTraineeSchedule)) {     
//            $groupTrainers[$row['userid']."^".$row['emailAddress']][] = $row;
//        }      
//        echo '<pre>'; print_r($groupTrainers); echo '</pre>';
//        
//        foreach($groupTrainers as $trainers => $values):
//            // split trainer and name. 
//            $trainerNE = explode("^", $trainers);
//                       
//            $mail = new PHPMailer;
//            $mail->SMTPDebug = 0;    //Enable SMTP debugging. 
//            $mail->isSMTP();        //Set PHPMailer to use SMTP.
//            $mail->CharSet = 'UTF-8';
//            $mail->Host = "smtp.live.com";  //Set SMTP host name   
//            $mail->SMTPAuth = true;     //Set this to true if SMTP host requires authentication to send email
//            //Provide username and password     
//            $mail->Username = "LifeStyleSportsSystem@hotmail.com";
//            $mail->Password = "LIU3104SHUANG";
//            $mail->SMTPSecure = "tls";  //If SMTP requires TLS encryption then set it
//            $mail->Port = 587;          //Set TCP port to connect to 
//            $mail->From = "LifeStyleSportsSystem@hotmail.com";
//            $mail->FromName = "Sports Management System";
//            $mail->addAddress($trainerNE[1]);
//            $mail->isHTML(true);
//            $emailTextHtml  = "<span>Dear <b>" .$trainerNE[0] . "</b></span>";
//            $emailTextHtml .= "<p>" . $selectResult['userid'] . " has cancelled his/her personal training session with you.</p>";
//            $emailTextHtml .= "<p>The date(s) of training session is/are <br/>";
//            
//            foreach($values as $trainingDates) {
//                $emailTextHtml .= $trainingDates['startdate']."<br/>";
//            }
//            
//            $emailTextHtml .= "</p>";
//            $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
//            $emailTextHtml .= "<p>Best regards </p>";
//            $emailTextHtml .= "<p>LifeStyle Sports System </p>";
//
//            $mail->Subject = "Sports System - Personal Training Cancelled";
//            $mail->Body = $emailTextHtml;
//
//            if (!$mail->send()) {
//                $responseArray = array('type' => 'danger', 'message' => '');
//                echo '<script language="javascript">';
//                echo 'alert("Something went wrong. Please try again later.Email not sent out");';
//                echo 'window.location.reload(history.go(-1));';
//                echo '</script>';
//            } else {
//                $responseArray = array('type' => 'success', 'message' => '');
//            }
//        endforeach;

//    } else {
//        echo '<script language="javascript">';
//        echo 'alert("Something went wrong. Please try again later.");';
//        echo 'window.location.reload(history.go(-1));';
//        echo '</script>';
//    }
}


if (isset($_POST['endBond'])) {
    $updateQuery = mysqli_query($link, "UPDATE users SET bondWithTrainerId ='' WHERE id='" . $selectResult['id'] . "' AND bondApprovalStatus = 'Approved'");
    $row = mysqli_affected_rows($link);
    if ($row == 1) {
        echo '<script language="javascript">';
        echo 'alert("Bond Ended Sucessfully!");';
        echo 'window.location.reload(history.go(-1));';
        echo '</script>';

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
        $mail->addAddress($bondTrainerResult['emailAddress']);
        $mail->isHTML(true);
        $emailTextHtml = "<span>Dear <b>" . $bondTrainerResult['userid'] . "</b></span>";
        $emailTextHtml .= "<p>" . $selectResult['userid'] . " has ended his/her bond with you on " . $currentDate . ".</p>";
        $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
        $emailTextHtml .= "<p>Best regards </p>";
        $emailTextHtml .= "<p>LifeStyle Sports System </p>";

        $mail->Subject = "Sports System - Bond has been ended";
        $mail->Body = $emailTextHtml;

        if (!$mail->send()) {
            $responseArray = array('type' => 'danger', 'message' => '');
            echo '<script language="javascript">';
            echo 'alert("Something went wrong. Please try again later.");';
            echo 'window.location.reload(history.go(-1));';
            echo '</script>';
        } else {
            $responseArray = array('type' => 'success', 'message' => '');
        }
    } else {
        echo '<script language="javascript">';
        echo 'alert("Something went wrong. Please try again later.");';
        echo 'window.location.reload(history.go(-1));';
        echo '</script>';
    }
}

if (isset($_POST['bondPending'])) {
    $updateQuery = mysqli_query($link, "UPDATE users SET bondWithTrainerId ='', bondApprovalStatus='' WHERE id='" . $selectResult['id'] . "'");
    $row = mysqli_affected_rows($link);
    if ($row == 1) {
        echo '<script language="javascript">';
        echo 'alert("Cancelled Bond Successfully");';
        echo 'window.location.reload(history.go(-1));';
        echo '</script>';
    }
  
}
?>

