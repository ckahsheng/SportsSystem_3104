<?php
session_start();
//Ensure only admin is able to access
//if (isset($_SESSION['role'])) {
// //   If user is logged in, they will be able to access their own calendar 
//    if (($_SESSION['role'] == 'Admin') || ($_SESSION['role'] == 'Customer')){
//        
//    } else {
//        header("location: index.php");
//    }
//} else {
//    header("location: index.php");
//}

$sessionUsername = $_SESSION['username'];
// Include config file
ob_start();
require_once 'DBConfig.php';
$current_pw_err = $password_err = $confirm_password_err = "";
//$success = false;
// Processing form data when form is submitted
if (isset($_POST['SaveChanges'])) {

    if (empty(trim($_POST['current_Password']))) {
        $current_pw_err = "Please enter current password.";
        $_SESSION['errorMessage']="Please Enter Current Password";
        
    } else if (empty(trim($_POST['new_Password']))) {

        $password_err = "Please enter a password.";
        $_SESSION['errorMessage']="Please enter a password!";
       // echo $password_err;
    } else if (empty(trim($_POST['confirm_passwordEdited']))) {
        $confirm_password_err = "Please enter confirm password";
        $_SESSION['errorMessage']="Please enter confirm password";
      //  echo $confirm_password_err;
    } elseif (strlen(trim($_POST['new_Password'])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
        $_SESSION['errorMessage']="Password must have atleast 6 characters!";
        //echo $password_err;
    } else {
        $currentPassword = $_POST['current_Password'];
       
        $sql1 = "SELECT password FROM users WHERE userid =?";
        if ($stmt = mysqli_prepare($link, $sql1)) {
// Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username1);
// Set parameters
            $param_username1 = $sessionUsername;
// Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
             //   echo "Executed";
// Store result
                mysqli_stmt_store_result($stmt);
// Check if pin exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
// Bind result variables
                    mysqli_stmt_bind_result($stmt, $hashedPw);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($currentPassword, $hashedPw)) {
                        //    echo "Verified";
//If same then carry on editing the password    
                            if ($_POST['new_Password'] == ($_POST['confirm_passwordEdited'])) {
                                if (empty($current_pw_err) && empty($password_err) && empty($confirm_password_err)) {
                                    $sqlUpdate = "UPDATE users SET  password = ? WHERE userid = ?";
                                    $stmt2 = mysqli_prepare($link, $sqlUpdate);
                                    mysqli_stmt_bind_param($stmt2, 'ss', $param_passwordHash, $param_userid);
                                    $param_passwordHash =htmlspecialchars((password_hash($_POST['new_Password'], PASSWORD_DEFAULT)), ENT_QUOTES, 'UTF-8'); 
                                    $param_userid = $sessionUsername;
// Attempt to execute the prepared statement
                                    if (mysqli_stmt_execute($stmt2)) {
// Refresh the page
                                        //header("location: changePassword.php");
                                        $_SESSION['editProfile'] = 'Password Changed';
                                    } else {
                                        echo "Something went wrong. Please try again later.";
                                        $_SESSION['errorMessage']="Error!";
                                    }

// Close statement
                                    mysqli_stmt_close($stmt2);
                                } else {
                                    echo "Something went wrong. Please try again later.";
                                }
                            } else {
                                $password_err = "Passwords mis-match";
                                $_SESSION['errorMessage']="Passwords mis-match!";
                            }
                        } else {
                         
                            $current_pw_err = "Incorrect Current Password";
                            $_SESSION['errorMessage']="Incorrect Current Password!";
                        }
                    }
                }
            }
        } else {
            echo "fail sql";
        }
//same password
    }
// Check input errors before updating in database
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
        <div class="container" style="padding-top:150px;">
            <center><h1>Change Password</h1></center>
            <hr>
            <div class="row">
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
                        unset($_SESSION['editProfile']);
                    }
                    ?>
                    <?php
                    if (isset($_SESSION['errorMessage']) && $_SESSION['errorMessage'] != '') {
                        ?>
                        <div class="alert alert-danger">
                            <strong>Error!</strong> <?php echo $_SESSION['errorMessage']; ?>
                        </div>
                        <?php
                        unset($_SESSION['errorMessage']);
                    }
                    ?>
                    <form class="form-horizontal" role="form" method="POST" action="changePassword.php" enctype="multipart/form-data">

                        <div class="form-group <?php echo (!empty($current_pw_err)) ? 'has-error' : ''; ?>">

                            <label class="col-md-3 control-label">Current Password:</label>
                            <div class="col-md-8">
                                <input class="form-control" type="password" name='current_Password' >

                            </div>
                        </div>
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                            <label class="col-md-3 control-label">New Password:</label>

                            <div class="col-md-8">
                                <input class="form-control" type="password" name='new_Password'>

                            </div>
                        </div>
                        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                            <label class="col-md-3 control-label">Confirm password:</label>
                            <div class="col-md-8">
                                <input class="form-control" type="password" name='confirm_passwordEdited'>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-8">
                                <input type="submit" class="btn btn-primary" name="SaveChanges" value="Save Changes">

                                <span></span>
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
