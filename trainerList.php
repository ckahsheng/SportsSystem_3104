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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#<?php echo $row['id']; ?>">View Trainer Profile</button></center>

                            <!-- Modal -->
                      
                            <div id="<?php echo $row['id']; ?>" class="modal fade" role="dialog" >
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content" id="p1" >
                                        <div class="modal-header">
                                            <center>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h3 class="modal-title"><?php echo $row['userid']; ?></h3>
                                            </center>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                            <h3><u>Description</u></h3>
                                            <p>
                                                <?php echo $row['description']; ?>
                                            </p>
                                            <h3><u>Charge Rate</u></h3>
                                            <p>
                                                <?php echo $row['chargeRate']; ?>
                                            </p>
                                            <p><strong>Personal Training Schedule</strong><br>
                                                Insert Calendar Here 

                                            <p><strong>Group Training Classes</strong><br>
                                                Insert Calendar Here 
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn white" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                ?>

            </div>
        </div>
        <hr>
    </body>
    <?php include("footer.html"); ?>

</html>
