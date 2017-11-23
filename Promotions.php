<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();


    require_once 'DBConfig.php';
    $sql = "SELECT * FROM tbl_images";
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
        

        <div class="container" style="padding-top:90px;">
            <center><h1>Promotion</h1></center>
         
            <div class="row">
                <!-- edit form column -->                     

                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-4">
                        <div class="thumbnail" id="one" style="align-content:center;">
                              <?php echo '<img src="data:image/jpeg;base64,'.base64_encode( $row['name'] ).'"/>'; ?>      
                            <br>  <h3><center><?php echo $row['title']; ?></center></h3>
                            <?php echo $row['description']; ?> 
                          

               
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



