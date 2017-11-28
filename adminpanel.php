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
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_role, $param_telephone, $param_email, $param_verified);
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

//Create training types
// Define variables and initialize with empty values
$TRAINING_NAME = $TRAINING_RATE = $TRAINING_NAME_err = "";
// Processing form data when form is submitted
if (!empty($_POST['create_trainingType_submit'])) {
// Validate TRAINING_NAME
    if (empty(trim($_POST["TRAINING_NAME"]))) {
        $TRAINING_NAME_err = "Please enter a Training Name.";
    } else {
        // Prepare a select statement
        $sql = "SELECT ID FROM trainingtype WHERE TRAINING_NAME = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_TRAINING_NAME);
            // Set parameters
            $param_TRAINING_NAME = trim($_POST["TRAINING_NAME"]);
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $TRAINING_NAME_err = "This Training Name is already taken.";
                } else {
                    $TRAINING_NAME = trim($_POST["TRAINING_NAME"]);
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Check input errors before inserting in database
    if (empty($TRAINING_NAME_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO trainingtype (TRAINING_NAME, TRAINING_RATE) VALUES (?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_TRAINING_NAME, $param_TRAINING_RATE);
            // Set parameters
            $param_TRAINING_NAME = $TRAINING_NAME;
            $param_TRAINING_RATE = $_POST["TRAINING_RATE"];
            //
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                session_start();
                $_SESSION['createdTrainingTypeMsg'] = 'Training Type Created';
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

$companyInfoDesc_err = "";

// Processing form data when form is submitted
if (!empty($_POST['create_companyInfo_submit'])) {
// Validate gymName
    if (empty($_POST["companyInfoDesc"])) {
        $companyInfoDesc_err = "Please enter a Company Information.";
    } else {
        $sql = "INSERT INTO companyinfo (companyInfoDesc) VALUES (?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_companyInfoDesc);
            // Set parameters
            $param_companyInfoDesc = $_POST["companyInfoDesc"];


            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: adminpanel.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    }
}

$trainingTipsType_err = $trainingTipsDesc_err = "";
if (!empty($_POST['create_trainingTips_submit'])) {
// Validate 
    if (empty($_POST["trainingTipsType"])) {
        $trainingTipsType_err = "Please enter a Training Tip Types.";
    }
    if (empty($_POST["trainingTipsDesc"])) {
        $trainingTipsDesc_err = "Please enter a Training Description.";
    } else {
        $sql = "INSERT INTO trainingTips (trainingTipsType, trainingTipsDesc) VALUES (?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_trainingTipsType, $param_trainingTipsDesc);
            // Set parameters
            $param_trainingTipsType = $_POST["trainingTipsType"];
            $param_trainingTipsDesc = $_POST["trainingTipsDesc"];


            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: adminpanel.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
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
                                    <a href="#" data-toggle="dropdown">User Management<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab2primary" data-toggle="tab">Verify New Users(<?php echo($row_cnt) ?>)</a></li>
                                        <li><a href="#tab1primary" data-toggle="tab">View All Users</a></li>
                                        <li><a href="#tab3primary" data-toggle="tab">Register New Trainers</a></li>

                                    </ul>
                                </li>
<!--                                <li><a href="#tab2primary" data-toggle="tab">Verify New Users (<?php echo($row_cnt) ?>)</a></li>-->
                                <!--<li><a href="#tab3primary" data-toggle="tab">Register New Trainer</a></li>-->
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Gym Facility Management<span class="caret"></span></a>
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
                                    <a href="#" data-toggle="dropdown">Group Trainings<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab11primary" data-toggle="tab">Pending Group Training Plans</a></li>
                                        <li><a href="#tab12primary" data-toggle="tab">Verified Group Training Plans</a></li>
                                        <li><a href="#tab13primary" data-toggle="tab">Rejected Group Training Plans</a></li>
                                        <li><a href="#tab30primary" data-toggle="tab">Manage Existing Group Trainings</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Training Category<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab14primary" data-toggle="tab">View Training Types</a></li>
                                        <li><a href="#tab15primary" data-toggle="tab">Add Training Types</a></li>
                                        <li><a href="#tab16primary" data-toggle="tab">Edit Training Types</a></li>
                                        <li><a href="#tab17primary" data-toggle="tab">Delete Training Types</a></li>
                                    </ul>
                                </li>

                                <!--Added to be changed accordingly-->
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Company Information<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <!--                                        <li><a href="#tab18primary" data-toggle="tab">Add company information</a></li>-->
                                        <li><a href="#tab23primary" data-toggle="tab">Update company information</a></li>

                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Training Tips<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab19primary" data-toggle="tab">Add Training Tips</a></li>
                                        <li><a href="#tab20primary" data-toggle="tab">Delete Training Tips</a></li>
                                        <li><a href="#tab21primary" data-toggle="tab">Update Training Tips</a></li>
                                        <li><a href="#tab22primary" data-toggle="tab">View Training Tips</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Manage Promotions<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab18primary" data-toggle="tab">Manage Promotion</a></li>

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

                                                                   data-height="600">
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
                                                                   data-height="600">
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
                                                                   data-height="600">
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
                                                                   data-height="600">
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
                                                    <b>List Of Gyms Facility</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllFacilityDelete"
                                                                   data-show-columns="true"
                                                                   data-height="600">
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
                                                    <b>List Of Gyms</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllGymsDelete"
                                                                   data-show-columns="true"
                                                                   data-height="600">
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
                                                                   data-detail-view="true"
                                                                   data-detail-formatter="detailFormatter"
                                                                   data-height="600">
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
                                                                   data-height="600">
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
                                                                   data-height="600">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>

                                <div class="tab-pane fade" id="tab14primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Training Types</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableAllTrainingTypes"
                                                                   data-show-columns="true"
                                                                   data-height="600">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>

                                <div class="tab-pane fade" id="tab15primary">  <div class="container" style="padding-left:50px; padding-right:200px;" >
                                        <h2>Create Training Type</h2>

                                        <?php
                                        if (isset($_SESSION['createdTrainingTypeMsg']) && $_SESSION['createdTrainingTypeMsg'] != '') {
                                            ?>
                                            <div class="alert alert-success">
                                                <strong>Success!</strong> <?php echo $_SESSION['createdTrainingTypeMsg']; ?>
                                            </div>
                                            <?php
                                            unset($_SESSION['createdTrainingTypeMsg']);
                                        }
                                        ?>

                                        <form  class="form-horizontal" role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-group <?php echo (!empty($TRAINING_NAME_err)) ? 'has-error' : ''; ?>">
                                                <label>Training name:<sup>*</sup></label>
                                                <input type="text" name="TRAINING_NAME"class="form-control" value="<?php echo $TRAINING_NAME; ?>">
                                                <span class="help-block"><?php echo $TRAINING_NAME_err; ?></span>
                                            </div>   
                                            <div class="form-group ">
                                                <label>Training Rate<sup></sup></label>
                                                <input type="text" name="TRAINING_RATE" class="form-control" value="<?php echo $TRAINING_RATE; ?>">
                                                <span class="help-block"></span>
                                            </div>

                                            <div class="form-group">

                                                <input type="submit"  name="create_trainingType_submit"  class="btn btn-primary" value="Create">
                                                <input type="reset" class="btn btn-default" value="Reset">
                                            </div>
                                        </form>

                                    </div> <!-- ./container --></div>

                                <div class="tab-pane fade" id="tab17primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Training Types</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableTrainingTypesDelete"
                                                                   data-show-columns="true"
                                                                   data-height="600">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>


                                <div class="tab-pane fade" id="tab16primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>List Of Training Types</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="Training_table"> 
                                                                <table class ="table table-bordered"
                                                                       data-show-columns="true"
                                                                       data-height="600">

                                                                    <tr>  
                                                                        <th width="40%">Training Name</th>  
                                                                        <th width="40%">Training Rate</th>  
                                                                        <th width="20%">View</th>  
                                                                    </tr>

                                                                    <?php
                                                                    $sql = "SELECT * FROM trainingtype ";
                                                                    $res = mysqli_query($link, $sql);
//mysqli_close($link);
                                                                    ?>
                                                                    <?php
                                                                    while ($row = $res->fetch_assoc()) {
                                                                        //while($row = mysqli_fetch_array($res))
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $row["TRAINING_NAME"]; ?></td>
                                                                            <td><?php echo $row["TRAINING_RATE"]; ?></td>
                                                                            <td><input type="button" name="edit" value="Edit" id="<?php echo $row["ID"]; ?>" class="btn btn-info btn-xs edit_data" /></td> 
                                                                        </tr>

                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>

                                    </div></div>

                                <div id="add_data_Modal" class="modal fade">  
                                    <div class="modal-dialog">  
                                        <div class="modal-content">  
                                            <div class="modal-header">  
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>  
                                                <h4 class="modal-title">Update</h4>  
                                            </div>  
                                            <div class="modal-body">  
                                                <form method="post" id="insert_form">  
                                                    <label>Training Name</label>  
                                                    <input type="text" name="TRAINING_NAME" id="TRAINING_NAME" class="form-control" />  
                                                    <br />  
                                                    <label>Training Rate</label>  
                                                    <input type="text" name="TRAINING_RATE" id="TRAINING_RATE" class="form-control" />   
                                                    <br />  
                                                    <input type="hidden" name="ID" id="ID" />  
                                                    <input type="submit" name="insert" id="insert" value="Insert" class="btn btn-success" />  
                                                </form>  
                                            </div>  
                                            <div class="modal-footer">  
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
                                            </div>  
                                        </div>  
                                    </div>  
                                </div>


                                <div class="tab-pane fade" id="tab18primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="container" style="width:900px;">  
                                            <center><h1> Manage Promotions </h1></center>

                                            <br />
                                            <div align="right">
                                                <button type="button" name="add" id="add" class="btn btn-success">Add</button>
                                            </div>
                                            <br />
                                            <div id="image_data">

                                            </div>
                                        </div>  
                                        </body>  
                                        </html>

                                        <div id="imageModal" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">Add Promotions</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="image_form" method="post" enctype="multipart/form-data">
                                                            <p><label>Select Image</label>
                                                                <input type="file" name="image" id="image" /></p><br />
                                                            <input type="hidden" name="action" id="action" value="insert" />
                                                            <input type="hidden" name="image_id" id="image_id" />
                                                            <div id="image_existing" name="image_existing"></div>
                                                            <div class="form-group">
                                                                <label>Promotion Title:<sup>*</sup></label>
                                                                <input type="text" name="addpromotiontitle"class="form-control">

                                                            </div>   
                                                            <div class="form-group ">
                                                                <label>Promotion Description<sup></sup></label>
                                                                <input type="text" name="addpromotiondescription" class="form-control"  >
                                                                <span class="help-block"></span>
                                                            </div>

                                                            <input type="submit" name="insert" id="insert" value="Add Promotion" class="btn btn-info" />

                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div></div>

                                <div class="tab-pane fade" id="tab23primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Update Company Information</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="CompanyInfo_table"> 
                                                                <table class ="table table-bordered"
                                                                       data-show-columns="true"
                                                                       data-height="460">

                                                                    <tr>  
                                                                        <th width="80%">Company Information</th>  
                                                                        <th width="20%">View</th>  
                                                                    </tr>

                                                                    <?php
                                                                    $sql = "SELECT * FROM companyinfo ";
                                                                    $res = mysqli_query($link, $sql);
//mysqli_close($link);
                                                                    ?>
                                                                    <?php
                                                                    while ($row = $res->fetch_assoc()) {
                                                                        //while($row = mysqli_fetch_array($res))
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $row["companyInfoDesc"]; ?></td>
                                                                            <td><input type="button" name="edit" value="Edit" id="<?php echo $row["companyInfoId"]; ?>" class="btn btn-info btn-xs edit_companyInfo" /></td> 
                                                                        </tr>

                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>

                                    </div></div>

                                <div id="edit_CompanyInfo_Modal" class="modal fade">  
                                    <div class="modal-dialog">  
                                        <div class="modal-content">  
                                            <div class="modal-header">  
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>  
                                                <h4 class="modal-title">Update</h4> 
                                            </div>  
                                            <div class="modal-body">  
                                                <form method="post" id="companyInfo_form">  
                                                    <label>Company Information</label>  
                                                    <textarea name="companyInfoDesc" id="companyInfoDesc" class="form-control" maxlength="255"></textarea>  
                                                    <br />  
                                                    <input type="hidden" name="id" id="id" />  
                                                    <input type="submit" name="editCompanyInfo" id="editCompanyInfo" value="Update" class="btn btn-success" />  
                                                </form>  
                                            </div> 
                                            <div class="modal-footer">  
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
                                            </div>  
                                        </div>  
                                    </div>  
                                </div>



                                <div class="tab-pane fade" id="tab19primary">  <div class="container" style="padding-left:50px; padding-right:200px;" >
                                        <h2>Create Training Tips</h2>

                                        <form  class="form-horizontal" role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-group <?php echo (!empty($trainingTipsType_err)) ? 'has-error' : ''; ?>">
                                                <label>Training Tips Type:<sup>*</sup></label>
                                                <input type="text" name="trainingTipsType"class="form-control">
                                                <span class="help-block"><?php echo $trainingTipsType_err; ?></span>
                                            </div>   
                                            <div class="form-group <?php echo (!empty($trainingTipsDesc_err)) ? 'has-error' : ''; ?>">
                                                <label>Training Tips Description:<sup></sup></label>
                                                <input type="text" name="trainingTipsDesc" class="form-control">
                                                <span class="help-block"><?php echo $trainingTipsDesc_err; ?></span>
                                            </div>

                                            <div class="form-group">

                                                <input type="submit"  name="create_trainingTips_submit"  class="btn btn-primary" value="Create">
                                                <input type="reset" class="btn btn-default" value="Reset">
                                            </div>
                                        </form>

                                    </div> <!-- ./container --></div>
                                <div class="tab-pane fade" id="tab20primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Delete Training Types</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableTrainingTipsDelete"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>


                                <div class="tab-pane fade" id="tab21primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Update Training Tips</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="TrainingTips_table"> 
                                                                <table class ="table table-bordered"
                                                                       data-show-columns="true"
                                                                       data-height="460">

                                                                    <tr>  
                                                                        <th width="40%">Training Tips Type</th>  
                                                                        <th width="40%">Training Tips Description</th>  
                                                                        <th width="20%">View</th>  
                                                                    </tr>

                                                                    <?php
                                                                    $sql = "SELECT * FROM trainingtips ";
                                                                    $res = mysqli_query($link, $sql);
//mysqli_close($link);
                                                                    ?>
                                                                    <?php
                                                                    while ($row = $res->fetch_assoc()) {
                                                                        //while($row = mysqli_fetch_array($res))
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $row["trainingTipsType"]; ?></td>
                                                                            <td><?php echo $row["trainingTipsDesc"]; ?></td>
                                                                            <td><input type="button" name="edit" value="Edit" id="<?php echo $row["trainingTipsId"]; ?>" class="btn btn-info btn-xs edit_trainingTips" /></td> 
                                                                        </tr>

                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>

                                    </div></div>

                                <div id="edit_TrainingTips_Modal" class="modal fade">  
                                    <div class="modal-dialog">  
                                        <div class="modal-content">  
                                            <div class="modal-header">  
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>  
                                                <h4 class="modal-title">Update</h4>  
                                            </div>  
                                            <div class="modal-body">  
                                                <form method="post" id="trainingTips_form">  
                                                    <label>Training Tips Type</label>  
                                                    <input type="text" name="trainingTipsType" id="trainingTipsType" class="form-control" />  
                                                    <br />  
                                                    <label>Training Rate</label>  
                                                    <input type="text" name="trainingTipsDesc" id="trainingTipsDesc" class="form-control" />   
                                                    <br />  
                                                    <input type="hidden" name="trainingTipsId" id="trainingTipsId" />  
                                                    <input type="submit" name="editTrainingTips" id="editTrainingTips" value="Update" class="btn btn-success" />  
                                                </form>  
                                            </div>  
                                            <div class="modal-footer">  
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
                                            </div>  
                                        </div>  
                                    </div>  
                                </div>

                                <div class="tab-pane fade" id="tab22primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>View Training Tips</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <table id="tableTrainingTipsView"
                                                                   data-show-columns="true"
                                                                   data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>

                                <div id="view_Recurring_GT" class="modal fade">  
                                    <div class="modal-dialog">  
                                        <div class="modal-content">  
                                            <div class="modal-header">  
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>  
                                                <h4 class="modal-title">Update</h4>  
                                            </div>  
                                            <div class="modal-body">  
                                                <form method="post" id="trainingTips_form">  
                                                    <label>Training Tips Type</label>  
                                                    <input type="text" name="trainingTipsType" id="trainingTipsType" class="form-control" />  
                                                    <br />  
                                                    <label>Training Rate</label>  
                                                    <input type="text" name="trainingTipsDesc" id="trainingTipsDesc" class="form-control" />   
                                                    <br />  
                                                    <input type="hidden" name="id" id="id" />  
                                                    <input type="submit" name="editTrainingTips" id="editTrainingTips" value="Update" class="btn btn-success" />  
                                                </form>  
                                            </div>  
                                            <div class="modal-footer">  
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
                                            </div>  
                                        </div>  
                                    </div>  
                                </div>



                                <!--Code for Managing Group Training Sessions-->
                                <div class="tab-pane fade" id="tab30primary">
                                    <div class="container" style="padding-top:20px;padding-right:80px;">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>Manage Existing Group Trainings</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="Training_table"> 
                                                                <table class ="table table-bordered"
                                                                       data-show-columns="true"
                                                                       data-height="600">

                                                                    <tr>  
                                                                        <th width="1%">#</th>  
                                                                        <th width="15%">Training Title</th>  
                                                                        <th width="15%">Trainer Name</th>  
                                                                        <th width="15%">Gym Location</th>  
                                                                        <th width="15%">Facility</th>  
                                                                        <th width="15%">Training Date</th>  
                                                                        <th width="15%">Training Time</th>  
                                                                        <th width="10%">Current Capacity</th>  
                                                                        <th width="10%">Max Capacity </th>  

                                                                        <th width="20%">View</th>  
                                                                    </tr>

                                                                    <?php
                                                                    $sql = "SELECT * FROM `grouptrainingschedule` WHERE trainingDate > NOW() + interval 2 day AND trainingApprovalStatus='Verified' ORDER BY trainingDate ASC";
                                                                    $res = mysqli_query($link, $sql);
//mysqli_close($link);
                                                                    ?>
                                                                    <?php
                                                                    $i = 1;
                                                                    while ($row = $res->fetch_assoc()) {
                                                                        //while($row = mysqli_fetch_array($res))
                                                                        ?>
                                                                        <tr>
                                                                            <td><?php echo $i; ?></td>
                                                                            <td><?php echo $row["trainingTitle"]; ?></td>
                                                                            <td><?php echo $row["trainerName"]; ?></td>
                                                                            <td><?php echo $row["trainingGym"]; ?></td>
                                                                            <td><?php echo $row["trainingFacility"]; ?></td>

                                                                            <td><?php echo $row["trainingDate"]; ?></td>
                                                                            <td><?php echo $row["trainingTime"]; ?></td>
                                                                            <td><?php echo $row["currentCap"]; ?></td>
                                                                            <td><?php echo $row["trainingMaxCapacity"]; ?></td>

                                                                            <td><input type="button" name="edit" value="Postpone" id="<?php echo $row["id"]; ?>" class="btn btn-info btn-xs postpone_training" />
                                                                                <input type="button" name="edit" value="Cancel" id="<?php echo $row["id"]; ?>" class="btn btn-danger btn-xs cancel_training" /></td> 
                                                                        </tr>

                                                                        <?php
                                                                        $i++;
                                                                    }
                                                                    ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>

                                    </div></div>


                                <div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-header">
                                        <h1>Processing...</h1>
                                    </div>
                                    <div class="modal-body">
                                        <div class="progress progress-striped active">
                                            <div class="bar" style="width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>


                                <!--Cancel group training modal-->
                                <div id="cancel_grouptraining_modal" class="modal fade">  
                                    <div class="modal-dialog">  
                                        <div class="modal-content">  
                                            <div class="modal-header">  
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>  
                                                <h4 class="modal-title">CANCEL GROUP TRAINING</h4>  
                                            </div>  
                                            <div class="modal-body">  
                                                <b> <h3>Confirm Cancel Group Training ?</h3></b><i> (All Trainees/Trainers will be notified through E-Mail) </i>
                                                <div id="wrapper">

                                                </div>

                                            </div>  
                                            <div class="modal-footer">  

                                                <input type="button" name="cfmCancelGroupTraining" id="cfmCancelGroupTraining" value="Confirm Cancel" class="btn btn-danger" />  
                                                <div class="loading_msg" style="display:none"><b>Processing,please wait.......</b></div>
                                                <button type="button" class="btn btn-info" data-dismiss="modal">Back</button>  

                                            </div>  
                                        </div>  
                                    </div>  
                                </div>


                                <!--Modal for postpone of training-->
                                <div id="postpone_training_modal" class="modal fade">  
                                    <div class="modal-dialog">  
                                        <div class="modal-content">  
                                            <div class="modal-header">  
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>  
                                                <h4 class="modal-title">Postpone Training</h4>  
                                            </div>  
                                            <div class="modal-body">  
                                                <form method="post" id="postpone_GroupTraining">  
                                                    <label>Training Title</label>  
                                                    <input type="text" name="TRAINING_TITLE" id="TRAINING_TITLE" class="form-control" disabled/>  
                                                    <br />  

                                                    <label>Training Initial Date</label>  
                                                    <input type="text" name="TRAINING_DATE" id="TRAINING_DATE" class="form-control" disabled/>   
                                                    <br />  
                                                    <label>Training Postponed Date:</label>
                                                    <div class='input-group input-append date' id='datePicker'>
                                                        <input type='datepicker' class="form-control" name="trainingUpdatedDate" id="trainingUpdatedDate"  required/>
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <br/>
                                                    <div class = "form-group">
                                                        <label>Training Time:</label>
                                                        <select name="startTime" class="form-control" id="startTime" required>
                                                            <option value="" selected disabled hidden>Choose Time</option>
                                                            <option value="10:00">10:00</option>
                                                            <option value="11:00">11:00</option>
                                                            <option value="12:00">12:00</option>						  
                                                            <option value="13:00">13:00</option>
                                                            <option value="14:00">14:00</option>
                                                            <option value="15:00">15:00</option>
                                                            <option value="16:00">16:00</option>
                                                            <option value="17:00">17:00</option>
                                                            <option value="18:00">18:00</option>
                                                            <option value="19:00">19:00</option>
                                                            <option value="20:00">20:00</option>
                                                            <option value="20:00">21:00</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Gym Location:</label>
                                                        <?php
                                                        $sql = "SELECT * FROM gym ";
                                                        $res = mysqli_query($link, $sql);
                                                        mysqli_close($link);
                                                        ?>
                                                        <select class="form-control" name="gymLocationDropDown1" id="gymLocationDropDown1">
                                                            <!--                                                                <option value="showTraining" selected="selected">Show All Training Type</option>
                                                            -->                                                                                                                                <option value="">Please Select Gym Location:</option>
                                                            <?php
                                                            while ($row = $res->fetch_assoc()) {
                                                                echo '<option value=" ' . $row['id'] . ' "> ' . $row['gymName'] . ' </option>';
                                                            }
                                                            ?>
                                                        </select>
                                                            <!--<input type = "text" class = "form-control" required = "required" id = "trainingCategory" name = "trainingCategory" > -->
                                                    </div>
                                                    <div class = "form-group">
                                                        <label>Facility:</label>
                                                        <select class="form-control" id="Facility" name="Facility">
                                                            <option value="">Please Select Gym Location</option>
                                                        </select>
                                                        <!--<input type = "text" class = "form-control" required = "required" id = "trainingVenue" name = "trainingVenue" >-->
                                                    </div>
                                                    <input type="hidden" name="id" id="id" />  
                                                    <input type="button" name="postponeTraining_Btn" id="postponeTraining_Btn" value="Confirm Postpone" class="btn btn-success" disabled/>  
                                                    <div class="loading_msg1" style="display:none"><b>Processing,please wait.......</b></div>
                                                </form>  
                                            </div>  
                                            <div class="modal-footer">  
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
                                            </div>  
                                        </div>
                                    </div>  
                                </div>  




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
        $(document).ready(function () {
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 4);
            $('#trainingUpdatedDate')
                    .datepicker({
                        autoclose: true,
                        format: 'mm/dd/yyyy',
                        startDate: today

                    })



            $('#view_Recurring_GT').on('hidden.bs.modal', function () {
                window.location.href = 'adminpanel.php';
            })

            $(document).on('click', '.edit_data', function () {
                var ID = $(this).attr("ID");
                $.ajax({
                    url: "PHPCodes/fetch.php",
                    method: "POST",
                    data: {ID: ID},
                    dataType: "json",
                    success: function (data) {
                        $('#TRAINING_NAME').val(data.TRAINING_NAME);
                        $('#TRAINING_RATE').val(data.TRAINING_RATE);
                        $('#ID').val(data.ID);
                        $('#insert').val("Update");
                        $('#add_data_Modal').modal('show');
                    }
                });
            });
            $('#insert_form').on("submit", function (event) {
                event.preventDefault();
                if ($('#TRAINING_NAME').val() == "")
                {
                    alert("Training name is required");
                } else if ($('#TRAINING_RATE').val() == '')
                {
                    alert("Training rate is required");
                } else
                {
                    $.ajax({
                        url: "PHPCodes/insert.php",
                        method: "POST",
                        data: $('#insert_form').serialize(),
                        beforeSend: function () {
                            $('#insert').val("Inserting");
                        },
                        success: function (data) {
                            $('#insert_form')[0].reset();
                            $('#add_data_Modal').modal('hide');
                            $('#Training_table').html(data);
                        }
                    });
                }
            });
        });



        $(document).ready(function () {
            //Code to cancel training - Group Training
            var groupSessionId = "";
            $(document).on('click', '.cancel_training', function () {
                groupSessionId = $(this).attr("id");
                //                alert(id);
                $('#cancel_grouptraining_modal').modal('show');
            });
            $('#cfmCancelGroupTraining').on("click", function (event) {
                event.preventDefault();
                alert("Please hold while we inform the affected users");

                $.ajax({
                    url: "cancelGroupTrainingEmail.php",
                    method: "POST",
                    beforeSend: function () {
                        $(".loading_msg").show();
                    },
                    data: {id: groupSessionId},
                    success: function (data) {
                        alert(data);
                        location.reload();
                    }
                });
                //                
            });


            //Code to postpone training - Group
            $(document).on('click', '.postpone_training', function () {
                var id = $(this).attr("id");
                $.ajax({
                    url: "PHPCodes/fetchGroupTrainingDetails.php",
                    method: "POST",
                    data: {ID: id},
                    dataType: "json",
                    success: function (data) {
                        $('#TRAINING_TITLE').val(data.trainingTitle);
                        $('#TRAINING_DATE').val(data.trainingDate);
                        $('#startTime').val(data.trainingTime);
                        $('#postpone_training_modal').modal('show');
//                        $('#gymLocationDropDown1').val(data.trainingGym);
//                        $('#Facility').val(data.trainingFacility);
//                        $('#TRAINING_NAME').val(data.TRAINING_NAME);
//                        $('#TRAINING_RATE').val(data.TRAINING_RATE);
//                        $('#ID').val(data.ID);
//                        $('#insert').val("Update");
//                        $('#add_data_Modal').modal('show');

                    }
                });
                $("#gymLocationDropDown1").change(function ()
                {
                    //Upon gym location selected update 
                    var gymId = $(this).find(":selected").val();
                    var StartPostDate = $('#trainingUpdatedDate').val();
                    var e = document.getElementById("startTime");
                    var startPostTime = e.options[e.selectedIndex].value;
                    $.ajax
                            ({
                                type: "POST",
                                url: 'PHPCodes/fetchPostPoneAvailableLocation.php',
                                data: {gymId: gymId, GrpTrainingID: id, startTime: startPostTime, startDate: StartPostDate
                                },
                                cache: false,
                                success: function (r)
                                {
                                    alert(r);
                                    //Fetch the locations in the gym and display out 
                                    id_numbers = JSON.parse(r);
                                    var venue = [];
                                    for (var x in id_numbers) {
                                        venue.push(id_numbers[x]);
                                    }
                                    var venueDropDown = document.getElementById("Facility");

                                    venueDropDown.innerHTML = "";
                                    for (var i = 0; i < venue.length; i++) {
                                        var opt = venue[i];
                                        var el = document.createElement("option");
                                        el.textContent = opt;
                                        el.value = opt;
                                        venueDropDown.appendChild(el);
                                    }
                                }


                            })
                            ;
                });

                $("#Facility").change(function ()
                {
                    document.getElementById("postponeTraining_Btn").disabled = false;

                });
                $('#postponeTraining_Btn').on("click", function (event) {
                    event.preventDefault();
                    var StartPostDate = $('#trainingUpdatedDate').val();
                    //Fetch start time value 
                    var e = document.getElementById("startTime");
                    var startPostTime = e.options[e.selectedIndex].value;
                    //Fetch Gym location this is in ID form
                    var f = document.getElementById("gymLocationDropDown1");
                    var gymId = f.options[f.selectedIndex].value;
                    //Fetch selected facility 
                    var g = document.getElementById("Facility");
                    var postponeFac = g.options[g.selectedIndex].value;

//                    alert(id);
//                    alert(StartPostDate);
//                    alert(startPostTime);
                    alert(gymId);
//                    alert(postponeFac);
                    $.ajax
                            ({
                                type: "POST",
                                url: 'postponeGTEmailScript.php',
                                data: {id: id,
                                    StartPostDate: StartPostDate,
                                    startPostTime: startPostTime,
                                    gymId: gymId,
                                    gymFacility: postponeFac
                                },
                                beforeSend: function () {
                                    $(".loading_msg1").show();
                                },
                                cache: false,
                                success: function (r)
                                {
                                    alert(r);
                                    location.reload();

                                }


                            });



//                    if ($('#TRAINING_NAME').val() == "")
//                    {
//                        alert("Training name is required");
//                    } else if ($('#TRAINING_RATE').val() == '')
//                    {
//                        alert("Training rate is required");
//                    } else
//                    {
//                        $.ajax({
//                            url: "PHPCodes/insert.php",
//                            method: "POST",
//                            data: $('#insert_form').serialize(),
//                            beforeSend: function () {
//                                $('#insert').val("Inserting");
//                            },
//                            success: function (data) {
//                                $('#insert_form')[0].reset();
//                                $('#add_data_Modal').modal('hide');
//                                $('#Training_table').html(data);
//                            }
//                        });
//                    }
                });
            });

        });

        $(document).ready(function () {
            $(document).on('click', '.edit_companyInfo', function () {
                var ID = $(this).attr("id");
                //           alert("TESTING 3"+" "+ ID);
                $.ajax({url: "PHPCodes/listCompanyInfo.php",
                    method: "POST",
                    data: {id: ID},
                    dataType: "json",
                    success: function (result) {
                        ////                     alert('ok');
                        //                     alert(result.trainingTipsType);
                        $('#companyInfoDesc').val(result.companyInfoDesc);
                        $('#id').val(result.companyInfoId);
                        //                     alert("TESTING2" + " " +result.trainingTipsId);
                        $('#editCompanyInfo').val("Update");
                        $('#edit_CompanyInfo_Modal').modal('show');
                    }
                });
            });
            $('#companyInfo_form').on("submit", function (event) {
                //                alert("TESTING");
                event.preventDefault();
                if ($('#companyInfoDesc').val() == "")
                {
                    alert("Company Information is required");
                } else
                {
                    $.ajax({
                        url: "PHPCodes/insertCompanyInfo.php",
                        method: "POST",
                        data: $('#companyInfo_form').serialize(),
                        beforeSend: function () {
                            $('#editCompanyInfo').val("Inserting");
                        },
                        success: function (data) {
                            $('#companyInfo_form')[0].reset();
                            $('#edit_CompanyInfo_Modal').modal('hide');
                            $('#CompanyInfo_table').html(data);
                        }
                    });
                }
            });
        });
        $(document).ready(function () {
            $(document).on('click', '.edit_trainingTips', function () {
                var ID = $(this).attr("id");
                //           alert("TESTING 3"+" "+ ID);
                $.ajax({url: "PHPCodes/fetchTrainingTips.php",
                    method: "POST",
                    data: {id: ID},
                    dataType: "json",
                    success: function (result) {
                        ////                     alert('ok');
                        //                     alert(result.trainingTipsType);
                        $('#trainingTipsType').val(result.trainingTipsType);
                        $('#trainingTipsDesc').val(result.trainingTipsDesc);
                        $('#trainingTipsId').val(result.trainingTipsId);
                        //                     alert("TESTING2" + " " +result.trainingTipsId);
                        $('#editTrainingTips').val("Update");
                        $('#edit_TrainingTips_Modal').modal('show');
                    }
                });
            });
            $('#trainingTips_form').on("submit", function (event) {
                // alert("TESTING");
                event.preventDefault();
                if ($('#trainingTipsType').val() == "")
                {
                    alert("Training Tips Type is required");
                } else if ($('#trainingTipsDesc').val() == '')
                {
                    alert("Training Tips Description is required");
                } else
                {
                    $.ajax({
                        url: "PHPCodes/insertTrainingTips.php",
                        method: "POST",
                        data: $('#trainingTips_form').serialize(),
                        beforeSend: function () {
                            $('#editTrainingTips').val("Inserting");
                        },
                        success: function (data) {
//                            alert(data);
                            $('#trainingTips_form')[0].reset();
                            $('#edit_TrainingTips_Modal').modal('hide');
                            $('#TrainingTips_table').html(data);


                            location.reload();

                        }
                    });
                }
            });
        });
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

        function sendAjax(id, msg, urlToSend) {

            $.ajax({type: "POST",
                url: urlToSend,
                data: {id: id,
                    msg: msg},
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
        //To display recurring sessions 
        function retrieveAvailableLocations(trainingid) {
            var trainingUID = trainingid;
            $.ajax
                    ({
                        type: "POST",
                        url: 'PHPCodes/fetchSuggestedLocations.php',
                        data: {trainingUID: trainingUID},
                        cache: false,
                        success: function (r)
                        {
                            id_numbers = JSON.parse(r);
                            var venue = [];
                            for (var x in id_numbers) {
                                venue.push(id_numbers[x]);
                            }
                            var venueDropDown = document.getElementById(trainingid);
                            venueDropDown.innerHTML = "";
                            var el = document.createElement("option");
                            el.textContent = "Please Select:";
                            el.value = "";
                            venueDropDown.appendChild(el);
                            for (var i = 0; i < venue.length; i++) {
                                var opt = venue[i];
                                var el = document.createElement("option");
                                el.textContent = opt;
                                el.value = opt;
                                venueDropDown.appendChild(el);
                            }
                        }

                    });
        }

        function updateLocation(id) {

            alert(id);
            var e = document.getElementById(id);
            var updatedFacility = e.options[e.selectedIndex].value;
            alert("You have selected new location:" + updatedFacility);
            //            $('#view_Recurring_GT').modal('hide');
            //            location.reload();
            //            $('#view_Recurring_GT').modal('show');

            $.ajax({type: "POST",
                url: 'PHPCodes/updateGTSessionLocation.php',
                data: {id: id,
                    updatedFac: updatedFacility},
                success: function (result) {
                    alert(result);
                    var display = "display" + id;
                    document.getElementById(display).style.display = 'inline';
                    //                    location.reload();
                    //                    $('#view_Recurring_GT').modal('show');
                    //alert('ok');
                    //alert(value);
                    //                    alert(result);
                    //                    var $table = $('#tableNotVerifiedUsers');
                    //                    $table.bootstrapTable('refresh');
                    //                    location.reload();
                },
                error: function (result)
                {
                    // alert('error');
                }
            });
            //Call a
        }
        function detailFormatter(index, row) {
            var groupTrainingID = '';
            var x = 'groupId';
            for (var key in row) {
                if (row.hasOwnProperty(key)) {
                    if (key.indexOf('groupId') == 0) // or any other index.
                        groupTrainingID = row[key];
                }
            }
            $.ajax({
                url: "PHPCodes/fetchRecurringSession.php",
                method: "POST",
                data: {groupTrainingID: groupTrainingID},
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    var container = document.getElementById("wrapper");
                    while (container.hasChildNodes()) {
                        container.removeChild(container.lastChild);
                    }
                    for (var i = 0; i < data.length; i++) {
                        var id = data[i][0];
                        var date = data[i][6];
                        var location = data[i][9];
                        var gym = data[i][8];
                        var availability = data[i][17];
                        container.appendChild(document.createTextNode("Date of Training: " + date));
                        var input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "trainingID-" + id;
                        container.appendChild(input);
                        container.appendChild(document.createElement("br"));
                        container.appendChild(document.createTextNode("Gym Location: " + gym));
                        container.appendChild(document.createElement("br"));
                        container.appendChild(document.createTextNode("Requested Facility :" + location));
                        container.appendChild(document.createElement("br"));
                        container.appendChild(document.createTextNode("Require Change Of Location : " + availability));
                        container.appendChild(document.createElement("br"));
                        container.appendChild(document.createTextNode("Suggested Locations :  "));
                        var selectList = document.createElement("select");
                        selectList.id = id;
                        //                        selectList.setAttribute("onchange", function(){updateLocation(selectList.id);});
                        //                        selectList.setAttribute("onchange", function () {
                        //                            updateLocation(selectList.id);
                        //                        });
                        selectList.addEventListener(
                                'change',
                                function () {
                                    //On change update record 
                                    updateLocation(this.id);
                                },
                                false
                                );
                        container.appendChild(selectList);
                        link = document.createElement('span');
                        text = document.createTextNode('Updated,Please refresh');
                        link.style.color = "green";
                        link.appendChild(text);
                        link.title = 'Hide information';
                        link.href = '#';
                        link.id = 'display' + id;
                        link.style.display = 'none';
                        //                        link.addEventListener('click', toggle);

                        container.appendChild(link);
                        container.appendChild(document.createElement("br"));
                        retrieveAvailableLocations(selectList.id);
                        container.appendChild(document.createElement("br"));
                    }
                    $('#view_Recurring_GT').modal('show');
                    $(document).ready(function () {
                        //                        alert("hi");
                    });
                }
            });
        }
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
                '<a class="like" href="javascript:void(0)" title="Approve">',
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
                var msg = window.prompt("Reason for rejecting:", "");
                var x = 'groupId';
                if (msg + '.' == 'null.') {
                    alert("You have click cancel");
                    location.reload();
                } else {
                    for (var key in row) {
                        if (row.hasOwnProperty(key)) {
                            if (key.indexOf('groupId') == 0) // or any other index.
                                groupId = row[key];
                        }
                    }
                    var linkToUpdate = 'PHPCodes/rejectGroupTraining.php';
                    sendAjax(groupId, msg, linkToUpdate);
                    //alert('You click remove action, row: ' + JSON.stringify(row));
                }
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
            columns: [
                {
                    field: 'dateUnavailable',
                    title: 'Location Available',
                    sortable: true,
                },
                {
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
                    field: 'trainingSDate',
                    title: 'Start Date',
                    sortable: true,
                },
                {
                    field: 'trainingEDate',
                    title: 'End Date',
                    sortable: true,
                },
                {
                    field: 'venue',
                    title: 'Venue',
                    sortable: true,
                }, {
                    field: 'starttime',
                    title: 'Time',
                    sortable: true,
                    visible: false,
                },
                {
                    field: 'recurring',
                    title: 'Repeated On',
                    sortable: true,
                },
                {
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
                    title: 'Approve/Reject Training',
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
        var $table = $('#tableAllTrainingTypes');
        $table.bootstrapTable({
            url: 'PHPCodes/listTrainingTypes.php',
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
                    field: 'TRAINING_NAME',
                    title: 'Training Name',
                    sortable: true,
                }, {
                    field: 'TRAINING_RATE',
                    title: 'Training Rate',
                    sortable: true,
                },
            ],
        });
        window.operateEventDeactivateTrainingTypes = {
            'click .remove': function (e, value, row, index) {
                var x = 'ID';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('ID') == 0) // or any other index.
                            ID = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/deleteTrainingTypes.php';
                sendAjaxRequest(ID, linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };
        var $table = $('#tableTrainingTypesDelete');
        $table.bootstrapTable({
            url: 'PHPCodes/listTrainingTypes.php',
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
                    field: 'ID',
                    title: 'Training Type ID',
                    sortable: true,
                    visible: false,
                },
                {
                    field: 'TRAINING_NAME',
                    title: 'Training Name',
                    sortable: true,
                }, {
                    field: 'TRAINING_RATE',
                    title: 'Training Rate',
                    sortable: true,
                },
                {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Delete Training Type',
                    align: 'center',
                    events: operateEventDeactivateTrainingTypes,
                    formatter: operateFormatterDeactivate
                }
            ],
        });
        //        ADDED BY CP

        window.operateEventDeactivateTrainingTips = {
            'click .remove': function (e, value, row, index) {
                var x = 'trainingTipsId';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if (key.indexOf('trainingTipsId') == 0) // or any other index.
                            groupId = row[key];
                    }
                }
                var linkToUpdate = 'PHPCodes/deleteTrainingTips.php';
                sendAjaxRequest(groupId, linkToUpdate);
                //alert('You click remove action, row: ' + JSON.stringify(row));
            }
        };
        var $table = $('#tableTrainingTipsDelete');
        $table.bootstrapTable({
            url: 'PHPCodes/listTrainingTips.php',
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
                    field: 'trainingTipsId',
                    title: 'Training Tips ID',
                    sortable: true,
                    visible: false,
                },
                {
                    field: 'trainingTipsType',
                    title: 'Training Tips Type',
                    sortable: true,
                }, {
                    field: 'trainingTipsDesc',
                    title: 'Training Tips Description',
                    sortable: true,
                },
                {
                    //This is to add the icons into the table
                    field: 'operate',
                    title: 'Delete Training Tips',
                    align: 'center',
                    events: operateEventDeactivateTrainingTips,
                    formatter: operateFormatterDeactivate
                }
            ],
        });
        var $table = $('#tableTrainingTipsView');
        $table.bootstrapTable({
            url: 'PHPCodes/listTrainingTips.php',
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
                    field: 'trainingTipsId',
                    title: 'Training Tips ID',
                    sortable: true,
                    visible: false,
                },
                {
                    field: 'trainingTipsType',
                    title: 'Training Tips Type',
                    sortable: true,
                }, {
                    field: 'trainingTipsDesc',
                    title: 'Training Tips Description',
                    sortable: true,
                }
            ],
        });


        $(document).ready(function () {

            fetch_data();

            function fetch_data()
            {
                var action = "fetch";
                $.ajax({
                    url: "action.php",
                    method: "POST",
                    data: {action: action},
                    success: function (data)
                    {
                        $('#image_data').html(data);
                    }
                })
            }
            $('#add').click(function () {
                $('#imageModal').modal('show');
                $('#image_form')[0].reset();
                $('.modal-title').text("Add Image");
                $('#image_id').val('');
                $('#addpromotiontitle').val('insert');
                $('#addpromotiondescription').val('insert');
                $('#action').val('insert');
                $('#insert').val("Insert");
            });
            $('#image_form').submit(function (event) {
                event.preventDefault();
                var image_name = $('#image').val();
                if (image_name == '')
                {
                    alert("Please Select Image");
                    return false;
                } else
                {
                    var extension = $('#image').val().split('.').pop().toLowerCase();
                    if (jQuery.inArray(extension, ['gif', 'png', 'jpg', 'jpeg']) == -1)
                    {
                        alert("Invalid Image File");
                        $('#image').val('');
                        return false;
                    } else
                    {
                        $.ajax({
                            url: "action.php",
                            method: "POST",
                            data: new FormData(this),
                            contentType: false,
                            processData: false,
                            success: function (data)
                            {
                                alert(data);
                                fetch_data();
                                $('#image_form')[0].reset();
                                $('#imageModal').modal('hide');
                            }
                        });
                    }
                }
            });
            $(document).on('click', '.update', function () {
                $('#image_id').val($(this).attr("id"));

                var id = $(this).attr("id");
//                alert(id);
                $.ajax({
                    url: "PHPCodes/fetchPromotions.php",
                    method: "POST",
                    data: {ID: id},
                    dataType: "json",
                    success: function (data) {
                        var preview = document.getElementById("image_existing");
                        preview.innerHTML = "";
                        var id = data[0];
                        var title = data[1];
                        var description = data[2];
                        var picBlob = data[3];
                        $('input[name="addpromotiontitle"]').val(title);
                        $('input[name="addpromotiondescription"]').val(description);
                        var img = document.createElement("img");
                        img.src = "data:image/jpeg;base64," + picBlob;
                        img.width = "280";
                        img.height = "190";

                        preview.appendChild(img);
                    }
                });
                $('#action').val("update");
                $('.modal-title').text("Update Promotion");
                $('#insert').val("Update");
                $('#imageModal').modal("show");
            });
            $(document).on('click', '.delete', function () {
                var image_id = $(this).attr("id");
                var action = "delete";
                if (confirm("Are you sure you want to remove this image from database?"))
                {
                    $.ajax({
                        url: "action.php",
                        method: "POST",
                        data: {image_id: image_id, action: action},
                        success: function (data)
                        {
                            alert(data);
                            fetch_data();
                        }
                    })
                } else
                {
                    return false;
                }
            });
        });

    </script>

</html>
