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
            <img class="fixed-ratio-resize" src="img/thumbnail_COVER.jpg" alt="img/thumbnail_COVER.JPG"/>

        </div>

        <div class="container">
            <p><?php
                if (isset($_SESSION['role'])) {

                    echo("{$_SESSION['role']}" . "<br />");
                }
                {
      
                }
                ?></p>
        </div>
    </body>
    <?php include("footer.html"); ?>

</html>
