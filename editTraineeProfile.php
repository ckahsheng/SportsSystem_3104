<?php
session_start();
$sessionUsername = $_SESSION['username'];
$sessionHashed_pw = $_SESSION['hashed_pw'];
// Include config file
ob_start();
require_once 'DBConfig.php';

$sqlID = "SELECT id FROM users WHERE userid = '$sessionUsername'";
$resultID = mysqli_query($link, $sqlID);
while ($row = mysqli_fetch_assoc($resultID)) {
    $sessionId = $row['id'];

    $sql1 = "SELECT * FROM users WHERE id = '$sessionId'";
    $result = mysqli_query($link, $sql1);
    $username_err = $password_err = $confirm_password_err = "";
    //$success = false;
    // Processing form data when form is submitted
    if (isset($_POST['SaveChanges'])) {
        //echo "$sessionUsername";
        if ($sessionUsername != $_POST['useridEdited']) {
            // Validate username
            if (empty(trim($_POST['useridEdited']))) {
                $username_err = "Please enter a username.";
            } else {
                // Prepare a select statement
                $userEdit = $_POST['useridEdited'];
                $sqlUni = "SELECT id FROM users WHERE userid = '$userEdit'";
                if ($stmtUni = mysqli_prepare($link, $sqlUni)) {
                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmtUni)) {
                        mysqli_stmt_store_result($stmtUni);
                        if (mysqli_stmt_num_rows($stmtUni) == 1) {
                            $username_err = "This username is already taken.";
                        } else {
                            $useridEdited = trim($_POST['useridEdited']);
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
                // Close statement
                mysqli_stmt_close($stmtUni);
            }
        } else {
            $useridEdited = $sessionUsername;
        }

        //echo "$useridEdited";
        $_SESSION['username'] = $useridEdited;

        if ($_POST['passwordEdited'] == $sessionHashed_pw) {
            //same password
            echo "same password";
            $passwordEdited1 = $sessionHashed_pw;
        } else if (password_verify($_POST['passwordEdited'], $sessionHashed_pw)) {
            /* Password is same */
            $passwordEdited1 = $sessionHashed_pw;
        } else {
            // Validate password
            if (empty(trim($_POST['passwordEdited']))) {
                $password_err = "Please enter a password.";
            } elseif (strlen(trim($_POST['passwordEdited'])) < 6) {
                $password_err = "Password must have atleast 6 characters.";
            } else {
                $passwordEdited = trim($_POST['passwordEdited']);
                $passwordEdited1 = password_hash($passwordEdited, PASSWORD_DEFAULT); // Creates a password hash
            }
            // Validate confirm password
            if (empty(trim($_POST["confirm_passwordEdited"]))) {
                $confirm_password_err = 'Please confirm password.';
            } else {
                $confirm_passwordEdited = trim($_POST['confirm_passwordEdited']);
                if ($passwordEdited != $confirm_passwordEdited) {
                    $confirm_password_err = 'Password did not match.';
                }
            }
        }
        $_SESSION['hashed_pw'] = $passwordEdited1;

        // Check input errors before updating in database
        if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
            $PwEdit = $_POST["phoneNumEdited"];
            $EAddEdit = $_POST["emailAddressEdited"];
            // Prepare an insert statement
            $sqlUpdate = "UPDATE users SET userid = '$useridEdited', password = '$passwordEdited1', phoneNumber = '$PwEdit', emailAddress = '$EAddEdit' WHERE id = '$sessionId'";
            if ($stmtUpdate = mysqli_prepare($link, $sqlUpdate)) {
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmtUpdate)) {
                    // Refresh the page
                    $_SESSION['editProfile'] = 'Profile Updated';
                    header("location: editTraineeProfile.php");
                    //exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
            }
            // Close statement
            mysqli_stmt_close($stmtUpdate);
        }
    } else if (isset($_POST['DeleteChanges'])) {
        echo "Click on delete";
    }
}
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
        <!--        <div class="container" style="padding-top:70px;">
                    <img class="fixed-ratio-resize" src="img/thumbnail_COVER.jpg" alt="img/thumbnail_COVER.JPG"/>
        
                </div>-->

        <div class="container" style="padding-top:150px;">
            <center><h1>Edit Profile</h1></center>
            <hr>
            <div class="row">
                <!-- left column -->
                <div class="col-md-3" style="padding-top:80px";>
                    <div class="text-center">
                        <img src="//placehold.it/100" class="avatar img-circle" alt="avatar">


                        <input type="file" class="form-control">
                    </div>
                </div>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <!-- edit form column -->
                    <div class="col-md-9 personal-info">
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
                        <h3><center>Personal info</center></h3>

                        <form class="form-horizontal" role="form" method="POST" action="editTraineeProfile.php">
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Telephone:</label>
                                <div class="col-lg-8">
                                    <input class="form-control" type="text" name='phoneNumEdited' value="<?php echo $row['phoneNumber']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Email:</label>
                                <div class="col-lg-8">
                                    <input class="form-control" type="text" name='emailAddressEdited' value="<?php echo $row['emailAddress']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Username:</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name='useridEdited' value="<?php echo $row['userid']; ?>">
                                </div>
                            </div>
                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
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
                            </div>
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
