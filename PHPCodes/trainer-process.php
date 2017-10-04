<?php
//session_start();
//include 'dbfunctions.inc.php';
//$cinema_id = $_GET['id'];
//
//$movie_id = $_GET['id'];
//$query = "SELECT * FROM movies WHERE movie_id = $movie_id";
//$result = mysqli_query($connection, $query);
//$row = mysqli_fetch_assoc($result);
//
//$query_showtime = "SELECT * FROM showtimes s,cinemas c WHERE s.cinema_id=c.cinema_id AND movie_id = $movie_id ORDER BY s.date,s.time";
//$showtimeInfo = mysqli_query($connection, $query_showtime);
//
//mysqli_close($connection);


// Include config file
ob_start();
require_once '../DBConfig.php';

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = '$id'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
?>

<html>
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

        <div class="container" style="padding-top:150px;">
            <h1><?php echo $row['userid']; ?></h1>
            <hr>
            <div class="row">
                    <div class="col-md-9 personal-info">      
                        
                            <?php echo "<img src='../images/".$row['image']."'>"; ?>
                        <form class="form-horizontal" role="form">
                            
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Charge Rate:</label>
                                <div class="col-lg-10">
                                    <?php echo $row['chargeRate']; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Email:</label>
                                <div class="col-lg-10">
                                    <?php echo $row['emailAddress']; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Phone number:</label>
                                <div class="col-lg-10">
                                    <?php echo $row['phoneNumber']; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Description:</label>
                                <div class="col-lg-10">
                                    <?php echo $row['description']; ?>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
        <hr>
    </body>
    <?php include("footer.html"); ?>
    
    
</html>
