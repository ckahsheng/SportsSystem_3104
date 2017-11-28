<?php

require_once '../DBConfig.php';
require_once "../PHPMailer-master/PHPMailerAutoload.php";
date_default_timezone_set('Asia/Singapore');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$traineeid = $_POST['id'];
$message = $_POST['msg'];
// Retrieve info from trainees
$bondedTraineeQuery = mysqli_query($link, "SELECT emailAddress, userid FROM users WHERE id = '$traineeid'");
$bondTraineeResult = mysqli_fetch_array($bondedTraineeQuery);

//update status for trainee
$updateQuery = mysqli_query($link, "UPDATE users SET bondApprovalStatus ='', bondWithTrainerId = '' WHERE id='$traineeid'");
$row = mysqli_affected_rows($link);

if ($row == 1) {       
   
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
    $emailTextHtml .= "<p>" . $_SESSION['username'] . " declined to bond with you because '".$message."'</p>";
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
?>   
