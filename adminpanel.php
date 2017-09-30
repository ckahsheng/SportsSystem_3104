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
        $sql = "INSERT INTO users (userid, password,role,phoneNumber,emailAddress) VALUES (?, ?,?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_role, $param_telephone, $param_email);
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_role = "Trainer";
            $param_telephone = $_POST["telephone"];
            $param_email = $_POST["email"];
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
    mysqli_close($link);
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
                    <div class="panel with-nav-tabs panel-primary">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs" >
                                <li class="active"><a href="#tab1primary" data-toggle="tab">View All Users</a></li>
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
                                mysqli_close($link);
                                ?>

                                <li><a href="#tab2primary" data-toggle="tab">Verify New Users (<?php echo($row_cnt) ?>)</a></li>
                                <li><a href="#tab3primary" data-toggle="tab">Register New Trainer</a></li>
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Approve Group Training Plans ( 0 )<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab4primary" data-toggle="tab">Primary 4</a></li>
                                        <li><a href="#tab5primary" data-toggle="tab">Primary 5</a></li>
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

                                                            <table 	 id="tableAllVerifiedUsers"
                                                                     data-show-columns="true"
                                                                     data-height="460">
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>				
                                            </div>
                                        </div>
                                    </div></div>
                                <div class="tab-pane fade" id="tab2primary"><div class="container" style="padding-top:20px;padding-right:80px; ">
                                        <div class="col-md-12">
                                            <div class="panel panel-success">
                                                <div class="panel-heading "> 
                                                    <b>New Registered Users</b>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table 	 id="tableNotVerifiedUsers"
                                                                     data-show-columns="true"
                                                                     data-height="460"
                                                                     >

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
                                <div class="tab-pane fade" id="tab4primary">Primary 4</div>
                                <div class="tab-pane fade" id="tab5primary">Primary 5</div>
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
            ],
        });
        function sendAjaxRequest(value, urlToSend) {

            $.ajax({type: "POST",
                url: urlToSend,
                data: {id: value},
                success: function (result) {
                    alert('ok');
                    alert(value);
                },
                error: function (result)
                {
                    alert('error');
                }
            });
        }
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
                var linkToUpdate = 'PHPCodes/updateVerificationAccount.php';
                sendAjaxRequest(userid, linkToUpdate);
                alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .remove': function (e, value, row, index) {
                $table.bootstrapTable('remove', {
                    field: 'num',
                    values: [row.id]
                });
            }
        };
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
                }
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

    </script>

</html>
