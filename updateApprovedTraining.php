<?php

require_once "PHPMailer-master/PHPMailerAutoload.php";
require_once "DBConfig.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$gid = $_POST['id'];
$query = "SELECT trainername FROM grouptrainings WHERE ID='$gid'";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$uid = $row['trainername'];
echo $uid;
$sql2 = "UPDATE grouptrainings SET trainingApprovalStatus='Verified' WHERE ID=?";
$stmt1 = mysqli_prepare($link, $sql2);
mysqli_stmt_bind_param($stmt1, "s", $param_id);
$param_id = $gid;
mysqli_stmt_execute($stmt1);

$sql = "UPDATE grouptrainingschedule SET trainingApprovalStatus='Verified' WHERE GrpRecurrID='$gid'";
if (mysqli_query($link, $sql)) {
    //Send email out upon user being verfied 
    $sql1 = "SELECT emailAddress,userid FROM users WHERE userid=?";
    if ($stmt = mysqli_prepare($link, $sql1)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $uid;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $email, $user);
                //Means the account balance is sufficient
                mysqli_stmt_fetch($stmt);
                $mail = new PHPMailer;
//Enable SMTP debugging. 
                $mail->SMTPDebug = 0;
//Set PHPMailer to use SMTP.
                $mail->isSMTP();
                $mail->CharSet = 'UTF-8';
//Set SMTP host name                          
                $mail->Host = "smtp.live.com";
                $mail->Subject = "Sports System - Your Group training request has been approved";
//Set this to true if SMTP host requires authentication to send email
                $mail->SMTPAuth = true;
//Provide username and password     
                $mail->Username = "LifeStyleSportsSystem@hotmail.com";
                $mail->Password = "LIU3104SHUANG";
//If SMTP requires TLS encryption then set it
                $mail->SMTPSecure = "tls";
//Set TCP port to connect to 
                $mail->Port = 587;
                $mail->From = "LifeStyleSportsSystem@hotmail.com";
                $mail->FromName = "Sports Management System";
                $mail->addAddress($email, "Group Training has been approved");
                $mail->isHTML(true);
//                $otpRandomPin = rand(100000, 999999);
                $emailTextHtml = "<span>Dear <b>$user</b></span>";
                $emailTextHtml .= "<table>";
//    foreach ($_POST as $key => $value) {
//        // If the field exists in the $fields array, include it in the email
//        if (isset($fields[$key])) {
//            $emailTextHtml .= "<tr><th>$fields[$key]</th><td>$value</td></tr>";
//        }
//    }
                $emailTextHtml .= "</table>";
                $emailTextHtml .= "<p>Your proposed group training has been verified, the verified group training will be make available to all trainees! </p>";
                $emailTextHtml .= "<p>If you encounter any problems, please email us at <b><i>LifeStyleSportsSystem@support.com</i></b> </p>";
                $emailTextHtml .= "<br><p>Best regards </p>";
                $emailTextHtml .= "<p>LifeStyle Sports System </p>";

                $mail->Subject = "Subject Text";
                $mail->Body = $emailTextHtml;
                $mail->AltBody = "This is the plain text version of the email content";

                if (!$mail->send()) {
                    $responseArray = array('type' => 'danger', 'message' => '');
                } else {
                    $responseArray = array('type' => 'success', 'message' => '');
                }
            } else if (mysqli_stmt_affected_rows($stmt) == 0) {
                //Account balance is insufficnet
                echo "Error ";
            }
        }
        mysqli_stmt_close($stmt);
    } else if (mysqli_stmt_affected_rows($stmt1) == 0) {
        //Account balance is insufficnet


        mysqli_stmt_close($stmt1);
    }
    echo "Group traning has been Approved, email has been Sent";
} else {
    echo "Error updating record: " . mysqli_error($link);
}

mysqli_close($link);
?>   
