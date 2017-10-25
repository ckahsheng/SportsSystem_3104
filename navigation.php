<?php
//Login Codes
// Include config file
require_once 'DBConfig.php';
//Script to perform login
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
// Processing form data when form is submitted
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (!empty($_POST['login_submit'])) {
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = 'Please enter username.';
    } else {
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT userid, password,role,verified FROM users WHERE userid = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = $username;
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password, $role, $verified);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            /* Password is correct, so start a new session and
                              save the username to the session */
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }
                            //Added this to identify what role is this user 
                            $_SESSION['username'] = $username;
                            $_SESSION['role'] = $role;
                            $_SESSION['hashed_pw'] = $hashed_password;
                            $_SESSION['verified_user'] = $verified;
                            header("location: index.php");
                        } else {
                            // Display an error message if password is not valid
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = 'No account found with that username.';
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
//}
?>
<!-- Navigation -->
<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container" >
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand page-scroll" href="index.php">
                <i class="glyphicon glyphicon-grain"></i> <span class="light">Sports Training </span> <font color="#8B0000">Schedule</font> 
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
            <ul class="nav navbar-nav">
                <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                <li class="hidden">
                    <a href="#page-top"></a>
                </li>
                <!--Over here add the access control-->
                <?php
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                if (isset($_SESSION['username'])) {
                    //If user is logged in, they will be able to access their own calendar 
                    if ($_SESSION['role'] == 'Trainee') {
                        ?>
                        <li>
                            <a class="page-scroll" href="testFullCalendar.php">MY SCHEDULE</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="">GROUP PT</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="trainerList.php">PERSONAL COACH</a>
                        </li>

                        <?php
                    } else if ($_SESSION['role'] == 'Trainer') {
                        //If not they will only be able to see the schedule of all our group trainings
                        ?>
                        <li>
                            <a class="page-scroll" href="testFullCalendar.php">MY SCHEDULE</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="">GROUP PT</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="trainerList.php">PERSONAL COACH</a>
                        </li>

                        <?php
                    } else if ($_SESSION['role'] == 'admin') {
                        //If not they will only be able to see the schedule of all our group trainings
                        ?>
                        <li>
                            <a class="page-scroll" href="testFullCalendar.php">MY SCHEDULE</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="">GROUP PT</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="trainerList.php">PERSONAL COACH</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="adminpanel.php">ADMIN PANEL</a>
                        </li>
                        <?php
                    }
                } else {
                    //Means not logged in user
                    ?>
                    <li>
                        <a class = "page-scroll" href = "">SCHEDULE</a>
                    </li>
                    <li>
                        <a class = "page-scroll" href = "">GROUP PT</a>
                    </li>
                    <li>
                        <a class = "page-scroll" href = "trainerList.php">PERSONAL COACH</a>
                    </li>
                    <?php
                }
                ?>
                <li class="dropdown">
                    <?php
// Initialize the session
                    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                        ?>
                        <!--If no session has been created,means no user has logged in- Display the login bar-->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>Login</b> <span class="caret"></span></a>
                        <ul id="login-dp" class="dropdown-menu">
                            <li>
                                <div class="row">
                                    <div class="col-md-11" style="width:300px;">
                                        <center> Login</center> <br>

                                        <form class="form" role="form" method="post"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" accept-charset="UTF-8" id="login-nav">
                                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                                <label class="sr-only" for="username">User Name</label>
                                                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>"  placeholder="User name" required>
                                                <span class="help-block"><?php echo $username_err; ?></span>
                                            </div>
                                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                                <label class="sr-only" for="password">Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                                <span class="help-block"><?php echo $password_err; ?></span>
                                                <div class="help-block text-right"><a href=""><i>Forget password ?</i></a></div>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" name="login_submit" class="btn btn-primary btn-block" value="Sign in">
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox"> keep me logged-in
                                                </label>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="bottom text-center">
                                        New here ? <a href="registration.php"><b>Join Us</b></a>
                                    </div>
                                </div>
                        </ul>
                        <?php
                    } else {
                        ?>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>PROFILE</b> <span class="caret"></span></a>
                        <ul id="login-dp" class="dropdown-menu">
                            <li>
                                <div class="row">
                                    <div class="col-md-11" style="width:300px;">
                                        <center> <a href="editTraineeProfile.php">VIEW PROFILE DETAILS</a></center> <br>
                                        <center> <a href="changePassword.php">CHANGE PASSWORD</a></center> <br>
                                    </div>
                                    <div class="bottom text-center">
                                        <a href="PHPCodes/logout.php"><b> LOGOUT</b></a>
                                    </div>
                                </div>
                        </ul>
                        <?php
                    }
                    ?>


                    </div>
                    <!-- /.navbar-collapse -->
                    </div>
                    <!-- /.container -->
                    </nav>
