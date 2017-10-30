<?php
// Include config file
ob_start();
require_once 'DBConfig.php';

$sql = "SELECT * FROM users WHERE role = 'Trainer'";
$result = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href='css/fullcalendar.css' rel='stylesheet' />
        <?php include("header.html"); ?>
    </head>
    <?php include("navigation.php"); ?>

    <body>
        <div class="container" style="padding-top:70px;">
            <img class="fixed-ratio-resize" src="img/personaltrainer.jpg" alt="img/thumbnail_COVER.JPG"/>
        </div>

        <div class="container" style="padding-top:20px;">
            <center><h1>Trainer List</h1></center>
            <hr>
            <div class="row">
                <!-- edit form column -->                     

                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-4">
                        <div class="thumbnail" id="one" style="align-content:center;">

                            <img src="<?php echo 'img/trainers/' . $row['image']; ?>" class="img-rounded img-responsive" style="max-height: 200px; max-width:400px;" alt=""/>
                            <br>  <h3><center><?php echo $row['userid']; ?></center></h3>

                            <!-- Trigger the modal with a button --><center>
                                <!--<button type="button" id="myBtn"  class="btn btn-primary trainerBtn" name="test" value="<?php //echo $row['userid']; ?>" data-toggle="modal" data-target="#<?php //echo $row['id']; ?>">View Trainer Profile</button></center>-->
                                <a href="testTrainerCalendar.php?trainerName=<?php echo $row['userid']; ?>" ><button type="button" class="btn btn-primary">View Trainer Profile</button></a>
                            <!-- Modal -->

               
                        </div>
                    </div>
                <?php }
                ?>

            </div>
        </div>
        <hr>
    </body>


    <?php include("calendarscripts.html"); ?>

    <script>
    </sript.
</html>
