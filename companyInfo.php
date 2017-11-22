<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();


    require_once 'DBConfig.php';
    $sql = "SELECT * FROM companyinfo";
    $result = mysqli_query($link, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            #container {padding: 34px;}
        </style>
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
            <img class="fixed-ratio-resize" src="img/adminbanner.jpg" alt="img/thumbnail_COVER.JPG"/>
        </div>
        <div class="container" style="padding-top:20px;">
            <div class="row">
              
                <div class="col-xs-6">
                    <center> <h1>ABOUT US</h1></center>
                    <p align="left"><br/><?php
    while ($row = $result->fetch_assoc()) {
        echo $row['companyInfoDesc'];
    }
    ?></p>
                </div>

                <div class="col-xs-6" id="container">
                    <img src="img/gym/gym.jpg " class="pull-right" height="250" width="500">
                </div>
            </div>

        </div>

    </body>
        <?php include("footer.html"); ?>
</html>



