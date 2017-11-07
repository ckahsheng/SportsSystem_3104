<?php
session_start();
$sessionUsername = $_SESSION['username'];
$sessionHashed_pw = $_SESSION['hashed_pw'];
// Include config file
ob_start();
require_once 'DBConfig.php';

$sqlID = "SELECT id,image AS imagePath FROM users WHERE userid = '$sessionUsername'";
$resultID = mysqli_query($link, $sqlID);
while ($row = mysqli_fetch_assoc($resultID)) {
    $sessionId = $row['id'];
    $imgIDPath = $row['imagePath'];

    $sql1 = "SELECT *  FROM users WHERE id = '$sessionId'";
    $result = mysqli_query($link, $sql1);
    $username_err = $password_err = $confirm_password_err = "";
    //$success = false;
    // Processing form data when form is submitted
    if (isset($_POST['SaveChanges'])) {
        //echo "$sessionUsername";
       
        //echo "$useridEdited";
//        $_SESSION['username'] = $useridEdited;

//        if ($_POST['passwordEdited'] == $sessionHashed_pw) {
//            //same password
//            echo "same password";
//            $passwordEdited1 = $sessionHashed_pw;
//        } else if (password_verify($_POST['passwordEdited'], $sessionHashed_pw)) {
//            /* Password is same */
//            $passwordEdited1 = $sessionHashed_pw;
//        } else {
//            // Validate password
//            if (empty(trim($_POST['passwordEdited']))) {
//                $password_err = "Please enter a password.";
//            } elseif (strlen(trim($_POST['passwordEdited'])) < 6) {
//                $password_err = "Password must have atleast 6 characters.";
//            } else {
//                $passwordEdited = trim($_POST['passwordEdited']);
//                $passwordEdited1 = password_hash($passwordEdited, PASSWORD_DEFAULT); // Creates a password hash
//            }
//            // Validate confirm password
//            if (empty(trim($_POST["confirm_passwordEdited"]))) {
//                $confirm_password_err = 'Please confirm password.';
//            } else {
//                $confirm_passwordEdited = trim($_POST['confirm_passwordEdited']);
//                if ($passwordEdited != $confirm_passwordEdited) {
//                    $confirm_password_err = 'Password did not match.';
//                }
//            }
//        }
//        $_SESSION['hashed_pw'] = $passwordEdited1;
        $imageFile = $_FILES["image"]["name"];
        $maxDim = 300;
        $uploadedfile = $_FILES['image']['tmp_name'];
        echo "$uploadedfile";
        list($width, $height, $type, $attr) = getimagesize($uploadedfile);
        if ($width > $maxDim || $height > $maxDim) {
            $target_filename = $uploadedfile;
            $ratio = $width / $height;
            if ($ratio > 1) {
                $new_width = $maxDim;
                $new_height = $maxDim / $ratio;
            } else {
                $new_width = $maxDim * $ratio;
                $new_height = $maxDim;
            }
            $src = imagecreatefromstring(file_get_contents($uploadedfile));
            $dst = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($src);
            imagepng($dst, $target_filename); // adjust format as needed
            imagedestroy($dst);
        }
        if (move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $imageFile)) {
            echo "Image Uploaded Successfully";
        } else {
            echo "failed";
        }
        //Add this so that when users edit their profile without updating the picture, the page will not overwrite the entire image 
        if ($imageFile === "") {
            $imageFile = $row['imagePath'];
            echo "Empty";
        }
        // Check input errors before updating in database
      
            $PwEdit = $_POST["phoneNumEdited"];
            $EAddEdit = $_POST["emailAddressEdited"];
            // Prepare an update statement
//            $sqlUpdate = "UPDATE users SET userid = '$useridEdited', password = '$passwordEdited1', phoneNumber = '$PwEdit', emailAddress = '$EAddEdit', image = '$imageFile' WHERE id = '$sessionId'";
             $sqlUpdate = "UPDATE users SET  phoneNumber = '$PwEdit', emailAddress = '$EAddEdit', image = '$imageFile' WHERE id = '$sessionId'";
            if ($stmtUpdate = mysqli_prepare($link, $sqlUpdate)) {
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmtUpdate)) {
                    // Refresh the page
                    header("location: editTraineeProfile.php");
                    $_SESSION['editProfile'] = 'Profile Updated';
                } else {
                    echo "Something went wrong. Please try again later.";
                }
            
            // Close statement
            mysqli_stmt_close($stmtUpdate);
        }
    } else if (isset($_POST['DeleteChanges'])) {
        echo "Click on delete";
    }
}

//Retrieve Bond - ys
$selectQuery = mysqli_query($link, "SELECT * FROM users WHERE userid = '" . $_SESSION['username'] . "'");
$selectResult = mysqli_fetch_array($selectQuery);
$bondQuery = mysqli_query($link, "SELECT userid FROM users WHERE id = '" . $selectResult['bondWithTrainerId'] . "'");
$bondResult = mysqli_fetch_array($bondQuery);
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
        <!--                <div class="container" style="padding-top:70px;">
                            <img class="fixed-ratio-resize" src="img/thumbnail_COVER.jpg" alt="img/thumbnail_COVER.JPG"/>
        
                        </div>-->

        <div class="container" style="padding-top:150px;">
            <center><h1>Edit Personal Info</h1></center>
            <hr>
            <div class="row">
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <!-- edit form column -->
                    <div class="col-md-9 col-md-offset-2">
                        <!--                    <div class="alert alert-info alert-dismissable">
                                                <a class="panel-close close" data-dismiss="alert">×</a>
                                                <i class="fa fa-coffee"></i>
                                                This is an <strong>.alert</strong>. Use this to show important messages to the user.
                                            </div>-->
                        <?php
                        if (isset($_SESSION['editProfile']) && $_SESSION['editProfile'] != '') {
                            ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> <?php echo $_SESSION['editProfile']; ?>
                            </div>
                            <?php
                            //unset($_SESSION['editProfile']);
                        }
                        ?>


                        <form class="form-horizontal" role="form" method="POST" action="editTraineeProfile.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="col-lg-3 control-label"></label>
                                <div class="col-lg-8"> 
                                    <img src="<?php echo 'img' . '/' . $row['image']; ?>"class="avatar img-circle"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label" for="image"></label>
                                <div class="col-lg-8">

                                    <input type="file" name="image" value="test">
                                    <!--<input type="file"class="form-control" id="image" name="image" required>-->
                                </div>
                            </div>
                                 <div class="form-group">
                                <label class="col-md-3 control-label">Username:</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name='useridEdited' value="<?php echo $row['userid']; ?>" disabled>
                                </div>
                            </div>
                            
                                  <div class="form-group">
                                <label class="col-lg-3 control-label">Email:</label>
                                <div class="col-lg-8">
                                    <input class="form-control" type="email" name='emailAddressEdited' value="<?php echo $row['emailAddress']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">Telephone:</label>
                                <div class="col-lg-8">
                                    <input class="form-control" type="number" name='phoneNumEdited' value="<?php echo $row['phoneNumber']; ?>">
                                </div>
                            </div>
                           <div class="form-group">
                                <label class="col-lg-3 control-label">Current Bond:</label>
                                <div class="col-lg-8">
                                    <?php if ($selectResult['bondWithTrainerId'] != ""){?>
                                    <u><a href="testTrainerCalendar.php?trainerName=<?php echo $bondResult['userid']; ?>" style="color:blue; font-size:18px"><?php echo $bondResult['userid']; ?></a></u>
                                    <?php } else{?>
                                    <p style="display: inline;">-</p>
                                    <?php } ?>
                                </div>
                            </div>

                       
<!--                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label class="col-md-3 control-label">Password:</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="password" name='passwordEdited' value="<?php echo $row['password']; ?>">
                                </div>
                            </div>
                            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                <label class="col-md-3 control-label">Confirm password:</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="password" name='confirm_passwordEdited' value="<?php echo $row['password']; ?>">
                                </div>
                            </div>-->
                            <div class="form-group">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-8">
                                    <input type="submit" class="btn btn-primary" name="SaveChanges" value="Save Changes">
                                    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
                                                </div>

                                                <div class="modal-body">
                                                    <p>You are about to delete your account, this is irreversible.</p>
                                                    <p>Do you want to proceed?</p>
                                                    <p class="debug-url"></p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                    <input type="button" onclick="window.location.href = 'PHPCodes/delete.php'" class="btn btn-danger" value="Confirm Delete">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="button"  class="btn btn-danger" value="Delete Account" data-href="PHPCodes/delete.php" data-toggle="modal" data-target="#confirm-delete"></a><br>
                                    <span></span>

                                </div>
                            </div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <hr>
    </body>
    <?php include("footer.html"); ?>

</html>
