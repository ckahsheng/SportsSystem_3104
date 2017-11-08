<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['role'] != 'admin') {
    //Rediret him back index 
    header("location: index.php");
}
?>
<?php
// Include config file
require_once 'DBConfig.php';

// Define variables and initialize with empty values
$emailAddress = $tel_number = $username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
// Processing form data when form is submitted
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (!empty($_POST['create_traineracc_submit'])) {
// Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE userid = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = trim($_POST["username"]);
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST['password']);
    }
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password != $confirm_password) {
            $confirm_password_err = 'Password did not match.';
        }
    }
    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (userid, password,role,phoneNumber,emailAddress,verified) VALUES (?, ?,?,?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_role, $param_telephone, $param_email,$param_verified);
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_role = "Trainer";
            $param_telephone = $_POST["telephone"];
            $param_email = $_POST["email"];
            $param_verified = "Not Verified";
            //
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                session_start();
                $_SESSION['createdTrainerMsg'] = 'Trainer Account Created';
                header("location: adminpanel.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    //mysqli_close($link);
}
//Create gym location
// Define variables and initialize with empty values
    $gymName = $gymLocation = $gymCountry = $gymOperatingHours = $gymName_err = "";
// Processing form data when form is submitted
if (!empty($_POST['create_gym_submit'])) {
// Validate gymName
    if (empty(trim($_POST["gymName"]))) {
        $gymName_err = "Please enter a Gym Name.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM gym WHERE gymName = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_gymName);
            // Set parameters
            $param_gymName = trim($_POST["gymName"]);
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $gymName_err = "This Gym Name is already taken.";
                } else {
                    $gymName = trim($_POST["gymName"]);
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Check input errors before inserting in database
    if (empty($gymName_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO gym (gymName, gymLocation,gymCountry,gymOperatingHours) VALUES (?,?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_gymName, $param_gymLocation, $param_gymCountry, $param_gymOperatingHours);
            // Set parameters
            $param_gymName = $gymName;
            $param_gymLocation = $_POST["gymLocation"];
            $param_gymCountry = $_POST["gymCountry"];
            $param_gymOperatingHours = $_POST["gymOperatingHours"];
            //
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                session_start();
                $_SESSION['createdGymMsg'] = 'Gym Location Created';
                header("location: adminpanel.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    //mysqli_close($link);
}

//Create gym facility
// Define variables and initialize with empty values
$facilityName = $facilityDesc = $facilityCapacity = $errorMessage = "";

// Processing form data when form is submitted
if (!empty($_POST['create_gymFacility_submit'])) {
// Validate gymName
//    if (empty(trim($_POST["gymid"]))) {
//        $gymid_err = "Please enter a Gym Name.";
//    }
    
    //Fetch Gym 
        $sql = "SELECT id FROM gym WHERE id = ?";
        //$sql = "SELECT gymName FROM gym WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_gymid);
            // Set parameters
            $param_gymid = $_POST['gymLocationDropDown'];
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $ID);
                    while ($stmt->fetch()) {
                        $gym = $ID;
                    }
                } else {
                    $errorMessage = "Invalid Gym";
                    $_SESSION['errorMessage'] = "Please try again later";
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
                $errorMessage = "Please try again later";
                $_SESSION['errorMessage'] = "Please try again later";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
        
// Check input errors before inserting in database (errorMessage)
    if (empty($errorMessage)) {
    //if (empty($gymid_err) && empty($facilityCapacity_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO gymfacility (gymid, facilityName,facilityDesc,facilityCapacity) VALUES (?,?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_Gym, $param_facilityName, $param_facilityDesc, $param_facilityCapacity);
            // Set parameters
            $param_Gym = $gym;
            $param_facilityName = $_POST["facilityName"];
            $param_facilityDesc = $_POST["facilityDesc"];
            $param_facilityCapacity = $_POST["facilityCapacity"];
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                session_start();
                $_SESSION['createdGymFacilityMsg'] = 'Facility Location Created';
                header("location: adminpanel.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
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
        <div class="container" style="padding-top:70px;">
            <img class="fixed-ratio-resize" src="img/adminbanner.jpg" alt="img/thumbnail_COVER.JPG"/>
        </div>
        <div class="container" style="padding-top:20px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-primary" id="tabs">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs" >
                                <?php
                                require_once 'DBConfig.php';
                                /* check connection */
                                if ($result = mysqli_query($link, "SELECT userid,role,created_at,emailAddress,phoneNumber,chargeRate,verified FROM users WHERE role !='admin' && verified='Not Verified'")) {
                                    /* determine number of rows result set */
                                    $row_cnt = mysqli_num_rows($result);
                                    /* close result set */
                                    mysqli_free_result($result);
                                }
                                /* close connection */
//                                mysqli_close($link);
                                ?>
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Manage Users<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab2primary" data-toggle="tab">Verify New Users(<?php echo($row_cnt) ?>)</a></li>
                                        <li><a href="#tab1primary" data-toggle="tab">View All Users</a></li>
                                        <li><a href="#tab3primary" data-toggle="tab">Register New Trainers</a></li>

                                    </ul>
                                </li>
<!--                                <li><a href="#tab2primary" data-toggle="tab">Verify New Users (<?php echo($row_cnt) ?>)</a></li>-->
                                <!--<li><a href="#tab3primary" data-toggle="tab">Register New Trainer</a></li>-->
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Mange Gym Facilities<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                         <li><a href="#tab5primary" data-toggle="tab">Add Gym</a></li>
                                        <li><a href="#tab6primary" data-toggle="tab">Add Gym Facility</a></li>
                                        <li><a href="#tab9primary" data-toggle="tab">Delete Gym</a></li>
                                        <li><a href="#tab8primary" data-toggle="tab">Delete Gym Facility Limit</a></li>
                                        <li><a href="#tab4primary" data-toggle="tab">View All Gym</a></li>
                                        <li><a href="#tab7primary" data-toggle="tab">View All Gym Facility</a></li>
                                       
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Manage Group Training Plans<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab11primary" data-toggle="tab">Pending Group Training Plans</a></li>
                                        <li><a href="#tab12primary" data-toggle="tab">Verified Group Training Plans</a></li>
                                        <li><a href="#tab13primary" data-toggle="tab">Rejected Group Training Plans</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tab1primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Users</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllVerifiedUsers"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="tab-pane fade" id="tab2primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px; ">
                                        <div class="col-md-12">
                                            <div class="panel panel-danger">
                                                <div class="panel-heading "> 
                                                    <b>New Registered Users</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableNotVerifiedUsers"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>

                                <div class="tab-pane fade" id="tab3primary">  <div class="container" style="padding-left:50px; padding-right:200px;" >
                                        <h2>Create Account For Trainer</h2>
                                        <?php
                                        if (isset($_SESSION['createdTrainerMsg']) && $_SESSION['createdTrainerMsg'] != '') {
                                            ?>
                                            <div class="alert alert-success">
                                                <strong>Success!</strong> <?php echo $_SESSION['createdTrainerMsg']; ?>
                                            </div>
                                            <?php
                                            unset($_SESSION['createdTrainerMsg']);
                                        }
                                        ?>

                                        <form  class="form-horizontal" role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                                <label>Trainer ID:<sup>*</sup></label>
                                                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                                                <span class="help-block"><?php echo $username_err; ?></span>
                                            </div>    
                                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                                <label>Password:<sup>*</sup></label>
                                                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                                                <span class="help-block"><?php echo $password_err; ?></span>
                                            </div>
                                            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                                <label>Confirm Password:<sup>*</sup></label>
                                                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                                                <span class="help-block"><?php echo $confirm_password_err; ?></span>
                                            </div>
                                            <div class="form-group ">
                                                <label>Trainer Telephone Number<sup></sup></label>
                                                <input type="tel" name="telephone" class="form-control" value="<?php echo $tel_number; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group ">
                                                <label>Trainer Email Address<sup></sup></label>
                                                <input type="email" name="email" class="form-control" value="<?php echo $emailAddress; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group">

                                                <input type="submit"  name="create_traineracc_submit"  class="btn btn-primary" value="Create">
                                                <input type="reset" class="btn btn-default" value="Reset">
                                            </div>
                                        </form>

                                    </div> <!-- ./container --></div>
                                
                                <div class="tab-pane fade" id="tab4primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Gyms</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllGyms"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                
                                 <div class="tab-pane fade" id="tab5primary">  <div class="container" style="padding-left:50px; padding-right:200px;" >
                                        <h2>Create Gym</h2>
                                        
                                        <?php
                                        if (isset($_SESSION['createdGymMsg']) && $_SESSION['createdGymMsg'] != '') {
                                            ?>
                                            <div class="alert alert-success">
                                                <strong>Success!</strong> <?php echo $_SESSION['createdGymMsg']; ?>
                                            </div>
                                            <?php
                                            unset($_SESSION['createdGymMsg']);
                                        }
                                        ?>
                                        
                                        <form  class="form-horizontal" role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-group <?php echo (!empty($gymName_err)) ? 'has-error' : ''; ?>">
                                                <label>Gym name:<sup>*</sup></label>
                                                <input type="text" name="gymName"class="form-control" value="<?php echo $gymName; ?>">
                                                <span class="help-block"><?php echo $gymName_err; ?></span>
                                            </div>   
                                            <div class="form-group ">
                                                <label>Gym Location<sup></sup></label>
                                                <input type="text" name="gymLocation" class="form-control" value="<?php echo $gymLocation; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group ">
                                                <label>Gym Country<sup></sup></label>
                                                <input type="text" name="gymCountry" class="form-control" value="<?php echo $gymCountry; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group ">
                                                <label>Gym Operating Hours<sup></sup></label>
                                                <input type="text" name="gymOperatingHours" class="form-control" value="<?php echo $gymOperatingHours; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group">

                                                <input type="submit"  name="create_gym_submit"  class="btn btn-primary" value="Create">
                                                <input type="reset" class="btn btn-default" value="Reset">
                                            </div>
                                        </form>

                                    </div> <!-- ./container --></div>
                                
                                <div class="tab-pane fade" id="tab6primary">  <div class="container" style="padding-left:50px; padding-right:200px;" >
                                        <h2>Create Gym Facility</h2>
                                         <?php
                                        if (isset($_SESSION['createdGymFacilityMsg']) && $_SESSION['createdGymFacilityMsg'] != '') {
                                            ?>
                                            <div class="alert alert-success">
                                                <strong>Success!</strong> <?php echo $_SESSION['createdGymFacilityMsg']; ?>
                                            </div>
                                            <?php
                                            unset($_SESSION['createdGymFacilityMsg']);
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

                                        <form  class="form-horizontal" role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-group ">
                                                            <select class="form-control" name="gymLocationDropDown" id="gymLocationDropDown">
                                                                <?php
                                                            $sql = "SELECT * FROM gym ";
                                                            $res = mysqli_query($link, $sql);
                                                            //mysqli_close($link);
                                                            ?>
                                                                <?php
                                                                while ($row = $res->fetch_assoc()) {
                                                                    echo '<option value=" ' . $row['id'] . ' "> ' . $row['gymName'] . ' </option>';
                                                                }
                                                                ?>
                                                            </select>
                                            </div>
                                            
                                            <div class="form-group ">
                                                <label>Facility Name<sup></sup></label>
                                                <input type="text" name="facilityName" class="form-control" value="<?php echo $facilityName; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group ">
                                                <label>Facility Description<sup></sup></label>
                                                <input type="text" name="facilityDesc" class="form-control" value="<?php echo $facilityDesc; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group ">
                                                <label>Facility Capacity<sup></sup></label>
                                                <input type="number" min="1" name="facilityCapacity" class="form-control" value="<?php echo $facilityCapacity; ?>">
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group">

                                                <input type="submit"  name="create_gymFacility_submit"  class="btn btn-primary" value="Create">
                                                <input type="reset" class="btn btn-default" value="Reset">
                                            </div>
                                        </form>

                                    </div> <!-- ./container --></div>
                                

                                <div class="tab-pane fade" id="tab7primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Gyms Facility</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllGymsFacility"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                
                                <div class="tab-pane fade" id="tab8primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Users</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllFacilityDelete"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                
                                <div class="tab-pane fade" id="tab9primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Users</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllGymsDelete"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                
                               
                                
                                <!--                                <div class="tab-pane fade" id="tab4primary">Primary 4</div>-->

                                <div class="tab-pane fade" id="tab11primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Pending Group Training</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tablePendingGroupTraining"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>

                                <div class="tab-pane fade" id="tab12primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Verified Group Training</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableVerifiedGroupTraining"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>

                                <div class="tab-pane fade" id="tab13primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Rejected Group Training</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableRejectedGroupTraining"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                
                              
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <?php include("footer.html"); ?>
    <!--This is the Javascript for the table for view all user details--> 
    <script type="text/javascript">
        function sendAjaxRequest(value, urlToSend) {

            $.ajax({type: "POST",
                url: urlToSend,
                data: {id: value},
                success: function (result) {
                    // alert('ok');
                    // alert(value);
                    alert(result);
                    location.reload();
                },
                error: function (result)
                {
                    alert('error');
                }
            });
        }
        
        function sendAjax(id,msg, urlToSend) {

            $.ajax({type: "POST",
                url: urlToSend,
                data: {id: id,
                msg:msg},
                success: function (result) {
                    // alert('ok');
                    // alert(value);
                    alert(result);
                    location.reload();
                },
                error: function (result)
                {
                    alert('error');
                }
            });
        }
        //To deactivate valid accounts
        window.operateEventDeactivate = {
            'click .remove': function (e, value, row, index) {
                var userid = '';
                var x = 'userid';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('userid') == 0) // or any other index.
                            userid = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/rejectUserAccount.php';
                sendAjaxRequest(userid, linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };

        //For the adding and removal for the new users 
        window.operateEvents = {
            'click .like': function (e, value, row, index) {
                var userid = '';
                var x = 'userid';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('userid') == 0) // or any other index.
                            userid = row[key];
                    }
                }
                var linkToUpdate = 'updateVerificationAccount.php';
                sendAjaxRequest(userid, linkToUpdate);
                // alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .remove': function (e, value, row, index) {
                var userid = '';
                var x = 'userid';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('userid') == 0) // or any other index.
                            userid = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/rejectUserAccount.php';
                sendAjaxRequest(userid, linkToUpdate);
                // alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };
        var $table = $('#tableAllVerifiedUsers');
        $table.bootstrapTable({
            url: 'PHPCodes/listusers.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'userid',
                    title: 'User Name',
                    sortable: true,
                }, {
                    field: 'role',
                    title: 'Role',
                    sortable: true,
                }, {
                    field: 'created',
                    title: 'Created Date',
                    sortable: true,
                }, {
                    field: 'email',
                    title: 'Email',
                    sortable: true,
                }, {
                    field: 'phoneNumber',
                    title: 'Telephone',
                    sortable: true,
                }, {
                    field: 'rate',
                    title: 'Charge Rate',
                    sortable: true,
                },
                {
                    field: 'description',
                    title: 'Description',
                    sortable: true,
                },
                {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Deactivate Account',
                    align: 'center',
                    events: operateEventDeactivate,
                    formatter: operateFormatterDeactivate

                },
            ],
        });
        function sendAjaxRequest(value, urlToSend) {

            $.ajax({type: "POST",
                url: urlToSend,
                data: {id: value},
                success: function (result) {
                    //alert('ok');
                    //alert(value);
                    alert(result);
                    var $table = $('#tableNotVerifiedUsers');
                    $table.bootstrapTable('refresh');
                    location.reload();
                },
                error: function (result)
                {
                    // alert('error');
                }
            });
        }

        var $table = $('#tableNotVerifiedUsers');
        $table.bootstrapTable({
            url: 'PHPCodes/listNonVerifiedUsers.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'userid',
                    title: 'User Name',
                    sortable: true,
                }, {
                    field: 'role',
                    title: 'Role',
                    sortable: true,
                }, {
                    field: 'created',
                    title: 'Created Date',
                    sortable: true,
                }, {
                    field: 'email',
                    title: 'Email',
                    sortable: true,
                }, {
                    field: 'phoneNumber',
                    title: 'Telephone',
                    sortable: true,
                }, {
                    field: 'rate',
                    title: 'Charge Rate',
                    sortable: true,
                },
                {
                    field: 'description',
                    title: 'Description',
                    sortable: true,
                },
                {
                    field: 'verified',
                    title: 'Verification',
                    sortable: true,
                },
                {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Approve User',
                    align: 'center',
                    events: operateEvents,
                    formatter: operateFormatter
                },
            ],
        });
        function operateFormatter(value, row, index) {
            return [
                '<a class="like" href="javascript:void(0)" title="Like">',
                '<i class="glyphicon glyphicon-ok"></i>',
                '</a>  ',
                '<a class="remove" href="javascript:void(0)" title="Remove">',
                '<i class="glyphicon glyphicon-remove"></i>',
                '</a>'
            ].join('');
        }

        function operateFormatterDeactivate(value, row, index) {
            return [
                '<a class="remove" href="javascript:void(0)" title="Remove">',
                '<i class="glyphicon glyphicon-remove"></i>',
                '</a>'
            ].join('');
        }

        var $table = $('#tableAllGymsFacility');
        $table.bootstrapTable({
            url: 'PHPCodes/listGymFacility.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'gymName',
                    title: 'Gym Name',
                    sortable: true,
                }, {
                    field: 'facilityName',
                    title: 'Facility Name',
                    sortable: true,
                }, {
                    field: 'facilityDesc',
                    title: 'Facility Description',
                    sortable: true,
                }, {
                    field: 'facilityCapacity',
                    title: 'Facility Capacity',
                    sortable: true,
                },
            ],
        });

        //To reject ongoing group training
        window.operateEventDisapprove = {
            'click .remove': function (e, value, row, index) {
                var x = 'groupId';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('groupId') == 0) // or any other index.
                            groupId = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/rejectGroupTraining.php';
                sendAjaxRequest(groupId, linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };

        var $table = $('#tableVerifiedGroupTraining');
        $table.bootstrapTable({
            url: 'PHPCodes/listVerifiedGroupTraining.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'groupId',
                    title: 'Group id',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainerName',
                    title: 'Trainer Name',
                    sortable: true,
                }, {
                    field: 'title',
                    title: 'Title',
                    sortable: true,
                }, {
                    field: 'trainingCategory',
                    title: 'Training Category',
                    sortable: true,
                }, {
                    field: 'rate',
                    title: 'Rate',
                    sortable: true,
                }, {
                    field: 'trainingDescription',
                    title: 'Description',
                    sortable: true,
                }, {
                    field: 'trainingDate',
                    title: 'Date',
                    sortable: true,
                }, {
                    field: 'venue',
                    title: 'Venue',
                    sortable: true,
                }, {
                    field: 'starttime',
                    title: 'Time',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainingFacility',
                    title: 'Facility',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainingMaxCapacity',
                    title: 'Max Capacity',
                    sortable: true,
                }, {
                    field: 'trainingApprovalStatus',
                    title: 'Approval Status',
                    sortable: true,
                }, {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Disapprove Group Training',
                    align: 'center',
                    events: operateEventDisapprove,
                    formatter: operateFormatterDeactivate

                },
            ],
        });

        window.operateApproveDisapprove = {
            'click .like': function (e, value, row, index) {
                var groupId = '';
                var x = 'groupId';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('groupId') == 0) // or any other index.
                            groupId = row[key];
                    }
                }
                var linkToUpdate = 'updateApprovedTraining.php';
                sendAjaxRequest(groupId, linkToUpdate);
                // alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .remove': function (e, value, row, index) {
                var msg=window.prompt("Reason for rejecting:","");
                var x = 'groupId';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('groupId') == 0) // or any other index.
                            groupId = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/rejectGroupTraining.php';
                sendAjax(groupId,msg,linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };

        var $table = $('#tablePendingGroupTraining');
        $table.bootstrapTable({
            url: 'PHPCodes/listPendingTraining.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'groupId',
                    title: 'Group id',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainerName',
                    title: 'Trainer Name',
                    sortable: true,
                }, {
                    field: 'title',
                    title: 'Title',
                    sortable: true,
                }, {
                    field: 'trainingCategory',
                    title: 'Training Category',
                    sortable: true,
                }, {
                    field: 'rate',
                    title: 'Rate',
                    sortable: true,
                }, {
                    field: 'trainingDescription',
                    title: 'Description',
                    sortable: true,
                }, {
                    field: 'trainingDate',
                    title: 'Date',
                    sortable: true,
                }, {
                    field: 'venue',
                    title: 'Venue',
                    sortable: true,
                }, {
                    field: 'starttime',
                    title: 'Time',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainingFacility',
                    title: 'Facility',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainingMaxCapacity',
                    title: 'Max Capacity',
                    sortable: true,
                }, {
                    field: 'trainingApprovalStatus',
                    title: 'Approval Status',
                    sortable: true,
                }, {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Disapprove Group Training',
                    align: 'center',
                    events: operateApproveDisapprove,
                    formatter: operateFormatter
                },
            ],
        });

        var $table = $('#tableRejectedGroupTraining');
        $table.bootstrapTable({
            url: 'PHPCodes/listRejectedGroupTraining.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'groupId',
                    title: 'Group id',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainerName',
                    title: 'Trainer Name',
                    sortable: true,
                }, {
                    field: 'title',
                    title: 'Title',
                    sortable: true,
                }, {
                    field: 'trainingCategory',
                    title: 'Training Category',
                    sortable: true,
                }, {
                    field: 'rate',
                    title: 'Rate',
                    sortable: true,
                }, {
                    field: 'trainingDescription',
                    title: 'Description',
                    sortable: true,
                }, {
                    field: 'trainingDate',
                    title: 'Date',
                    sortable: true,
                }, {
                    field: 'venue',
                    title: 'Venue',
                    sortable: true,
                }, {
                    field: 'starttime',
                    title: 'Time',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainingFacility',
                    title: 'Facility',
                    sortable: true,
                    visible: false,
                }, {
                    field: 'trainingMaxCapacity',
                    title: 'Max Capacity',
                    sortable: true,
                }, {
                    field: 'trainingApprovalStatus',
                    title: 'Approval Status',
                    sortable: true,
                },
            ],
        });
        
         var $table = $('#tableAllGyms');
        $table.bootstrapTable({
            url: 'PHPCodes/listGym.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'id',
                    title: 'Gym ID',
                    sortable: true,
                    visible: false,
                },
                {
                    field: 'gymName',
                    title: 'Gym Name',
                    sortable: true,
                }, {
                    field: 'gymLocation',
                    title: 'Gym Location',
                    sortable: true,
                }, {
                    field: 'gymCountry',
                    title: 'Gym Country',
                    sortable: true,
                }, {
                    field: 'gymOperatingHours',
                    title: 'Gym Operating Hours',
                    sortable: true,
                },
            ],
        });
        
        window.operateEventDeactivateGym = {
            'click .remove': function (e, value, row, index) {
                var id = '';
                var x = 'id';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('id') == 0) // or any other index.
                            id = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/deleteGym.php';
                sendAjaxRequest(id, linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };
        
        var $table = $('#tableAllGymsDelete');
        $table.bootstrapTable({
            url: 'PHPCodes/listGym.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                },{
                    field: 'id',
                    title: 'Gym ID',
                    sortable: true,
                    visible: false,
                }, 
                {
                    field: 'gymName',
                    title: 'Gym Name',
                    sortable: true,
                }, {
                    field: 'gymLocation',
                    title: 'Gym Location',
                    sortable: true,
                }, {
                    field: 'gymCountry',
                    title: 'Gym Country',
                    sortable: true,
                }, {
                    field: 'gymOperatingHours',
                    title: 'Gym Operating Hours',
                    sortable: true,
                },
                {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Delete Gym',
                    align: 'center',
                    events: operateEventDeactivateGym,
                    formatter: operateFormatterDeactivate
                }
            ],
        });
        
        //To delete facility
        window.operateEventDeactivateFacility = {
            'click .remove': function (e, value, row, index) {
                var id = '';
                var x = 'id';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('id') == 0) // or any other index.
                            id = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/deleteFacility.php';
                sendAjaxRequest(id, linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };
        
        var $table = $('#tableAllFacilityDelete');
        $table.bootstrapTable({
            url: 'PHPCodes/listGymFacility.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [{
                    field: 'num',
                    title: '#',
                    sortable: true,
                }, {
                    field: 'id',
                    title: 'Gym ID',
                    sortable: true,
                    visible: false,
                },
                {
                    field: 'gymName',
                    title: 'Gym Name',
                    sortable: true,
                }, {
                    field: 'facilityName',
                    title: 'Facility Name',
                    sortable: true,
                }, {
                    field: 'facilityDesc',
                    title: 'Facility Description',
                    sortable: true,
                }, {
                    field: 'facilityCapacity',
                    title: 'Facility Capacity',
                    sortable: true,
                },
                {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Delete Facility',
                    align: 'center',
                    events: operateEventDeactivateFacility,
                    formatter: operateFormatterDeactivate
                }
            ],
        });

 

    </script>

</html>
