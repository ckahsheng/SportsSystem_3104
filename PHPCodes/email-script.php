<?php

include_once('../DBConfig.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 

echo "script called";
$username = $_SESSION['username'];
    //echo $username;
    $role= $_SESSION['role'];
    echo  $role;
  $id = $_POST['id'];  
  // $id= $_; //use to simulate join together for trainer and trainee schedule of PT
    echo $id;
     
      
if ($_SESSION['role'] == 'Trainer') {
   //SQL to change training status to cancelled -  Database(trainerschedule) add new column trainingstatus //                              
    $sql = "UPDATE `trainerschedule` SET `trainingstatus` = 'Cancelled' WHERE `trainerschedule`.`trainingid` = $id";
   
    
    $result = mysqli_query($link, $sql) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {  
        echo "Record update successfully";
     //  header('Location:../index.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    
    //Database(training schedule) add new column traineeid//
    //SQL to select emailaddress of trainee who is bounded to that training event//
     
    $sql2 =  "SELECT emailAddress FROM users INNER JOIN trainerschedule ON users.userid = trainerschedule.traineeid WHERE trainerschedule.trainingid = $id";
    $result = mysqli_query($link, $sql2) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
     $email = $result->fetch_assoc();
       foreach($email as $email){
     // to split the query into data so i can assign to values//     
       }
        print_r($email);
        
        //Email Function here//
        //Load composer's autoloader
        require 'vendor/autoload.php';
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'keegan898@gmail.com';                 // SMTP username
    $mail->Password = 'dowellinschool';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('keegan898@gmail.com', 'TrainingScheduler');
    $mail->addAddress($email, 'user');     // Add a recipient
  //  $mail->addAddress('reciver2@gmail.com');               // Name is optional
   // $mail->addReplyTo('replayto@gmail.com', 'Information');
   // $mail->addCC('cc.....@egmail.com');
   // $mail->addBCC('bcc......@gmail.com');

    //Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Training Cancellation';
    $mail->Body    = 'This is the notifcation email to notify you that due the trainer has been  <b>Cancelled!</b>';
    $mail->AltBody = 'Your training scheduled has been cancelled';
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
     //  header('Location:../index.php')1;
    } 
    else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    //Database need to add new table "cancelrecord" which is a copy of training schedule//
 //SQL to insert delete record into cancelrecord//   
    $sql3 = "INSERT INTO cancelrecord ( trainingid, name,title,startdate,enddate,venue,starttime,endtime,rate,recur,eventType,trainingstatus,traineeid,rolewhodeletedrecord ) SELECT trainerschedule.trainingid, trainerschedule.name, trainerschedule.title, trainerschedule.startdate, trainerschedule.enddate,trainerschedule.venue,trainerschedule.starttime,trainerschedule.endtime,trainerschedule.rate,trainerschedule.recur,trainerschedule.eventType,trainerschedule.trainingstatus,trainerschedule.trainingid,'Trainer' FROM trainerschedule WHERE trainerschedule.trainingid = $id";
    
    $result = mysqli_query($link, $sql3) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
         
        echo "Inserted";
     //  header('Location:../index.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    
     
  

  }
  
//If session user now is a trainee  
  
   if ($_SESSION['role'] == 'Trainee') {
        
    //sql to change training status//
    
    $sql = "UPDATE `trainerschedule` SET `trainingstatus` = 'Cancelled' WHERE `trainerschedule`.`trainingid` = $id";
   
     
   
      
    
    $result = mysqli_query($link, $sql) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
         
        echo "Record update successfully";
     //  header('Location:../index.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    
    
    
  $sql2 = "INSERT INTO cancelrecord ( trainingid, name,title,startdate,enddate,venue,starttime,endtime,rate,recur,eventType,trainingstatus,traineeid,rolewhodeletedrecord ) SELECT trainerschedule.trainingid, trainerschedule.name, trainerschedule.title, trainerschedule.startdate, trainerschedule.enddate,trainerschedule.venue,trainerschedule.starttime,trainerschedule.endtime,trainerschedule.rate,trainerschedule.recur,trainerschedule.eventType,trainerschedule.trainingstatus,trainerschedule.trainingid,'Trainee' FROM trainerschedule WHERE trainerschedule.trainingid = $id";
 
     
   
      
    
    $result = mysqli_query($link, $sql2) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
         
        echo "insert update successfully";
     //  header('Location:../index.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    } 
    
    
    
    
    
    
    
      $sql3 ="SELECT emailAddress FROM users INNER JOIN trainerschedule ON users.userid = trainerschedule.name WHERE trainerschedule.trainingid = $id";
    
     $result = mysqli_query($link, $sql3) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
          
              
        $email = $result->fetch_assoc();
       foreach($email as $email){
          
       }
        print_r($email);
        
 
require 'vendor/autoload.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'keegan898@gmail.com';                 // SMTP username
    $mail->Password = 'dowellinschool';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('keegan898@gmail.com', 'Mailer');
    $mail->addAddress($email, 'User');     // Add a recipient
  //  $mail->addAddress('reciver2@gmail.com');               // Name is optional
   // $mail->addReplyTo('replayto@gmail.com', 'Information');
   // $mail->addCC('cc.....@egmail.com');
   // $mail->addBCC('bcc......@gmail.com');

    //Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Training Cancellation from traineer';
    $mail->Body    = 'This is the notifcation email to notify you that due the trainer has been  <b>Cancelled!</b>';
    $mail->AltBody = 'Your training scheduled has been cancelled';

    $mail->send();
    echo 'Message has been sent';
	
	 
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
   
        
     
    
        
        
        
        
        
        
       } else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    
  ///sql 3 to update the status in training schedule
    
    
     
    
    
    $sql4= "UPDATE `trainerschedule` SET `trainingstatus` = 'Available' WHERE `trainerschedule`.`trainingid` = $id";
        
    
    $result = mysqli_query($link, $sql4) // Note using mysqli_query -- not mysql_query
            or die(mysqli_error($link));
    if ($result) {
        $count = mysqli_affected_rows($link);
    }
    if ($count > 0) {
         
        echo "Update  particular traing to available";
     //  header('Location:../index.php');
    } else {
        echo "Error updating record: " . mysqli_error($link);
        //Create session variable to let user know not able to create 
    }
    
    
   
    
    
    }

    header('Location:../testFullCalendar.php');
    
?>