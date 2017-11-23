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

        <div class="container" style="padding-top:30px;">
            
             <div class="col-md-4">
                 <center><h2>Promotion</h2></center>
                 
                        <div class="thumbnail" id="one" style="align-content:center;" >
                                
              
               <a href="promotions.php"><img class="fixed-ratio-resize" src="img/mainpromotion.jpg" alt="img/thumbnail_COVER.JPG"/>
                        </div>
                    </div>
            
            
            <div class="col-md-4">
                
                <center><h3>Training Tips</h3></center>
                        <div class="thumbnail" id="one" style="align-content:center;">
                               
                 <a href="trainingtips.php"><img class="fixed-ratio-resize" src="img/trainertips2.jpg" alt="img/thumbnail_COVER.JPG"/>
               
                        </div>
                    </div>
            
             <div class="col-md-4">
                 <center><h3>Our Vision</h3></center>
                        <div class="thumbnail" id="one" style="align-content:center;">
                                 <a href="companyInfo.php"><img class="fixed-ratio-resize" src="img/golds-gym-downtown-la-ca-weight-training.jpg" alt="img/thumbnail_COVER.JPG"/>
                
               
                        </div>
                    </div>
            
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
