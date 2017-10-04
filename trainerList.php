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

        <div class="container" style="padding-top:150px;">
            <center><h1>Trainer List</h1></center>
            <hr>
            <div class="row">
                <!-- edit form column -->                     

<?php
$i = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $i++;
    if ($i % 4 == 0) {
        ?>
                        <div class="row">
                            <div class="col-sm-3">
                                <p><?php echo "<img src='images/" . $row['image'] . "'>"; ?>
                                    <br>
        <?php echo $row['userid'] ?>
                                    <br>
                                    <a href="PHPCodes/trainer-process.php?id=<?php echo $row['id'] ?>" class="label label-success">More Details</a>
                                </p>
                            </div>

    <?php } else { ?>
                            <div class="col-sm-3">
                                <p>
                                    <img src="images/<?php echo $row['image']; ?>" class="img-responsive " style="max-height:1000px" alt=""/>
                                    <br>
        <?php echo $row['userid'] ?>

                                    <br>
                                    <a href="PHPCodes/trainer-process.php?id=<?php echo $row['id'] ?>" class="label label-success">More Details</a></p>
                            </div>

        <?php
    }
}
?>
                </div>

            </div>
        </div>
        <hr>
    </body>
<?php include("footer.html"); ?>

</html>
