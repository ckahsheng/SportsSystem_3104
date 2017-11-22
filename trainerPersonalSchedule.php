<?php
// Include config file
//STOPPED HERE ( INSERT VALUE INTO DATABASE ) 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    include_once('DBConfig.php');

    $errorMessage = "";
    $datesToStoreRecurring = array();
    if (!empty($_POST['requestTraining'])) {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE userid = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = $_SESSION['username'];
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $ID);
                    while ($stmt->fetch()) {
                        $trainerId = $ID;
                    }
                } else {
                    $errorMessage = "Invalid Trainer";
                    $_SESSION['errorMessage'] = "Invalid Trainer";
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
                $errorMessage = "Please try again later";
                $_SESSION['errorMessage'] = "Please try again later";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);

        //Fetch Training Category 
        $sql = "SELECT TRAINING_NAME FROM trainingtype WHERE ID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_trainingID);
            // Set parameters
            $param_trainingID = $_POST['trainingTypeDropDown'];
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $ID);
                    while ($stmt->fetch()) {
                        $trainingType = $ID;
                    }
                } else {
                    $errorMessage = "Invalid Trainer";
                    $_SESSION['errorMessage'] = "Invalid Training Category";
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
                $errorMessage = "Please try again later";
                $_SESSION['errorMessage'] = "Please try again later";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);

        //Fetch Gym 
        $sql = "SELECT gymName FROM gym WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_gymID);
            // Set parameters
            $param_gymID = $_POST['gymLocationDropDown'];
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
                    $_SESSION['errorMessage'] = "Invalid Gym";
                }
            } else {
                //  echo "Oops! Something went wrong. Please try again later.";
                $errorMessage = "Please try again later";
                $_SESSION['errorMessage'] = "Please try again later";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
        $invalidGymLocationFlag = "";
        $duplicateDateFlag = "";
        //Code to check for recurring 
        if (isset($_POST['recurring'])) {
            $repeatDays = implode(",", $_POST['recurring']);
            $startDate = $_POST['trainingDate'];
            $endDate = $_POST['trainingendDate'];
            echo 'ohmy';
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);
            $mydays = $_POST['recurring'];
            foreach ($period as $date) {
                if (in_array($date->format('N'), $mydays)) {
                    $result = $date->format('Y-m-d');
                    //Means this are the dates we have to store into the database as well 
                    //Store into arrraylist
                    array_push($datesToStoreRecurring, $result);
                    //do something for Monday, Wednesday and Friday
                }
            }
        }
        $conflictDates = "";
        $lastInsertedId = "";
        $conflictArray = array();
        //print_r($datesToStore);
        // Check input errors before inserting in database
        if (empty($errorMessage)) {
            if (sizeof($datesToStoreRecurring) > 0) {
                //Create a flag to check if the duplicated dates are conflicting schedules 
                //Check for each date if they conflict with any of the records in the Personal , OT , or group training sessions

                foreach ($datesToStoreRecurring as $value) {
                    echo $value;
                    $sqlDuplicate = "select * from ( "
                            . "select trainerschedule.trainingid,trainerschedule.startdate as 'StartDate', trainerschedule.starttime as 'StartTime',trainerschedule.name as 'TrainerName' from trainerschedule "
                            . "union all "
                            . "select grouptrainingschedule.trainerid,grouptrainingschedule.trainingDate AS 'StartDate', grouptrainingschedule.trainingTime as 'StartTime',grouptrainingschedule.trainerName as 'TrainerName' from grouptrainingschedule ) "
                            . "a "
                            . "WHERE StartDate=? AND StartTime=? AND TrainerName=?";

                    $q = $bdd->prepare($sqlDuplicate);
                    $q->execute(array(date('Y-m-d', strtotime($value)), $_POST['startTime'], $_SESSION['username']));
                    $result = $q->fetchAll(PDO::FETCH_ASSOC);
                    if ($result == TRUE) {
                        $_SESSION['errorMessage'] = "Duplicated slot on ";
                        $duplicateDateFlag = "true";
                        //If conflicted dates, then stop the transaxction from taking place 
//                        echo "Duplicated la";
//                        header("Location:../testFullCalendar.php?msg=$msg");
                    }

                    $sqlGymAvail = "SELECT * from grouptrainingschedule WHERE trainingDate=? AND trainingTime=? AND trainingGym=? AND trainingFacility=? AND trainingApprovalStatus='Verified'";
                    $q = $bdd->prepare($sqlGymAvail);
                    $q->execute(array(date('Y-m-d', strtotime($value)), $_POST['startTime'], $gym, $_POST['Facility']));
                    $result = $q->fetchAll(PDO::FETCH_ASSOC);

                    if ($result == TRUE) {
//                        print_r($result);
//                        $_SESSION['errorMessage'] = "Gym Location Not Available for one of the dates";
//                        $invalidDates="Not Available location on : ";
//                        $invalidGymLocationFlag = "true";
                        foreach ($result as $values) {
                            $conflictDates = $conflictDates . "," . $values['trainingDate'];
                            $conflictArray[] = $values['trainingDate'];
                        }
                    }
                }
                print_r($conflictArray);
                print($conflictDates);
                echo $duplicateDateFlag;
                echo $invalidGymLocationFlag;

//                if (($duplicateDateFlag== "") && ($invalidGymLocationFlag=="")) {
                if (($duplicateDateFlag == "")) {
                    //INSERT INTO MAIN TABLE TO STORE GROUP TRAINING SESSIONS 
                    $sql = "INSERT INTO grouptrainings (title,trainername,trainingcategory,trainingrate,trainingstartdate,trainingenddate,trainingtime,trainingcapacity,trainingApprovalStatus,trainingGym,trainingFacility,recurring,dateUnavailable) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sssssssssssss", $param_trainingTitle, $param_trainerName, $param_trainingCategory, $param_rate, $param_SDate, $param_EDate, $param_Time, $param_MaxCap, $param_Status, $param_Gym, $param_Facility, $param_recurr_days, $param_dateUnavailable);
                        // Set parameters
                        $param_trainerid = $trainerId;
                        $param_trainingTitle = $_POST['trainingTitle'];
                        $param_trainingCategory = $trainingType;
                        $param_rate = $_POST['trainingRate'];
                        $param_SDate = date('Y-m-d', strtotime($_POST['trainingDate']));
                        $param_EDate = date('Y-m-d', strtotime($_POST['trainingendDate']));
                        $param_Time = $_POST['startTime'];
                        $param_Gym = $gym;
                        $param_Facility = $_POST['Facility'];
                        $param_MaxCap = $_POST['trainingCapacityDropDown'];
                        $param_Status = "Pending";
                        $param_trainerName = $_SESSION['username'];
                        $param_recurr_days = implode(",", $_POST['recurring']);
                        $param_dateUnavailable = $conflictDates;
                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Redirect to login page
                            $lastInsertedId = mysqli_stmt_insert_id($stmt);
                            echo $lastInsertedId;
                            $_SESSION['groupSessionCreated'] = 'Group Training Request Submitted';
                            echo "Stored into main table";
                        } else {
                            $errorMessage = "Something went wrong. Please try again later.";
                            $_SESSION['errorMessage'] = "Please try again later";
                        }
                    }
                    //If no duplicated dates then proceed to store into database 
                    //1. Check if user has any training session in this period of time before allowing of creation 

                    foreach ($datesToStoreRecurring as $value) {
                        if (in_array($value, $conflictArray)) {
                            $sql = "INSERT INTO grouptrainingschedule (trainerid,trainingTitle,trainingCategory,trainingRate,trainingDescription,trainingDate,trainingTime,trainingGym,trainingFacility,trainingMaxCapacity,trainingApprovalStatus,trainerName,GrpRecurrID,invalidLocation) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            if ($stmt = mysqli_prepare($link, $sql)) {
                                // Bind variables to the prepared statement as parameters
                                mysqli_stmt_bind_param($stmt, "ssssssssssssss", $param_trainerid, $param_trainingTitle, $param_trainingCategory, $param_rate, $param_Desc, $param_Date, $param_Time, $param_Gym, $param_Facility, $param_MaxCap, $param_Status, $param_trainerName, $param_recurrID, $param_invalidLocation);
                                // Set parameters
                                $param_trainerid = $trainerId;
                                $param_trainingTitle = $_POST['trainingTitle'];
                                $param_trainingCategory = $trainingType;
                                $param_rate = $_POST['trainingRate'];
                                $param_Desc = $_POST['trainingDesc'];
                                $param_Date = date('Y-m-d', strtotime($value));
                                $param_Time = $_POST['startTime'];
                                $param_Gym = $gym;
                                $param_Facility = $_POST['Facility'];
                                $param_MaxCap = $_POST['trainingCapacityDropDown'];
                                $param_Status = "Pending";
                                $param_trainerName = $_SESSION['username'];
                                $param_recurrID = $lastInsertedId;
                                $param_invalidLocation = 'YES';
                                // Attempt to execute the prepared statement
                                if (mysqli_stmt_execute($stmt)) {
                                    // Redirect to login page

                                    $_SESSION['groupSessionCreated'] = 'Group Training Request Submitted';
                                } else {
                                    $errorMessage = "Something went wrong. Please try again later.";
                                    $_SESSION['errorMessage'] = "Please try again later";
                                }
                            }
                        } else {
                            $sql = "INSERT INTO grouptrainingschedule (trainerid,trainingTitle,trainingCategory,trainingRate,trainingDescription,trainingDate,trainingTime,trainingGym,trainingFacility,trainingMaxCapacity,trainingApprovalStatus,trainerName,GrpRecurrID,invalidLocation) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            if ($stmt = mysqli_prepare($link, $sql)) {
                                // Bind variables to the prepared statement as parameters
                                mysqli_stmt_bind_param($stmt, "ssssssssssssss", $param_trainerid, $param_trainingTitle, $param_trainingCategory, $param_rate, $param_Desc, $param_Date, $param_Time, $param_Gym, $param_Facility, $param_MaxCap, $param_Status, $param_trainerName, $param_recurrID, $param_invalidLocation);
                                // Set parameters
                                $param_trainerid = $trainerId;
                                $param_trainingTitle = $_POST['trainingTitle'];
                                $param_trainingCategory = $trainingType;
                                $param_rate = $_POST['trainingRate'];
                                $param_Desc = $_POST['trainingDesc'];
                                $param_Date = date('Y-m-d', strtotime($value));
                                $param_Time = $_POST['startTime'];
                                $param_Gym = $gym;
                                $param_Facility = $_POST['Facility'];
                                $param_MaxCap = $_POST['trainingCapacityDropDown'];
                                $param_Status = "Pending";
                                $param_trainerName = $_SESSION['username'];
                                $param_recurrID = $lastInsertedId;
                                $param_invalidLocation = 'NO';
                                // Attempt to execute the prepared statement
                                if (mysqli_stmt_execute($stmt)) {
                                    // Redirect to login page

                                    $_SESSION['groupSessionCreated'] = 'Group Training Request Submitted';
                                } else {
                                    $errorMessage = "Something went wrong. Please try again later.";
                                    $_SESSION['errorMessage'] = "Please try again later";
                                }
                            }
                        }
                    }
                    // Close statement
                    mysqli_stmt_close($stmt);
                } else {
                    echo "weird";
                }
            }
            //Recurring is not selected 
            else {
                //1. Check if trainer has any events during this period of time- be it own training or own training or group training 
                $sqlGymAvail = "SELECT * from grouptrainingschedule WHERE trainingDate=? AND trainingTime=? AND trainingGym=? AND trainingFacility=?";
                $q = $bdd->prepare($sqlGymAvail);
                $q->execute(array(date('Y-m-d', strtotime($_POST['trainingDate'])), $_POST['startTime'], $gym, $_POST['Facility']));
                $result = $q->fetchAll(PDO::FETCH_ASSOC);
                if ($result == TRUE) {
                    $_SESSION['errorMessage'] = "Gym Location Not Available for one of the dates";
                    $invalidGymLocationFlag = "true";
                    //If conflicted dates, then stop the transaxction from taking place 
                    //  echo "Invalid Location";
//                        header("Location:../testFullCalendar.php?msg=$msg");
                    foreach ($result as $values) {
                        $conflictDates = $conflictDates . "," . $values['trainingDate'];
                    }
                } else {
                    // echo  "nope";
                }

                $sqlDuplicate = "select * from ( "
                        . "select trainerschedule.trainingid,trainerschedule.startdate as 'StartDate', trainerschedule.starttime as 'StartTime',trainerschedule.name as 'TrainerName' from trainerschedule "
                        . "union all "
                        . "select grouptrainingschedule.trainerid,grouptrainingschedule.trainingDate AS 'StartDate', grouptrainingschedule.trainingTime as 'StartTime',grouptrainingschedule.trainerName as 'TrainerName' from grouptrainingschedule ) "
                        . "a "
                        . "WHERE StartDate=? AND StartTime=? AND TrainerName=?";

                $q = $bdd->prepare($sqlDuplicate);
                $q->execute(array(date('Y-m-d', strtotime($_POST['trainingDate'])), $_POST['startTime'], $_SESSION['username']));
                $result = $q->fetchAll(PDO::FETCH_ASSOC);
                if ($result == TRUE) {
                    $_SESSION['errorMessage'] = "Duplicated slot";
                    $duplicateDateFlag = "true";
                    //echo "Duplicated Slot";
//                        header("Location:../testFullCalendar.php?msg=$msg");
                }

                echo $_POST['trainingDate'];
                echo $_POST['startTime'];
                echo $gym;
                echo $_POST['Facility'];
                echo $duplicateDateFlag;
                echo $invalidGymLocationFlag;
                if (($duplicateDateFlag == "true") || ($invalidGymLocationFlag == "true")) {
                    
                } else {
                    $sql = "INSERT INTO grouptrainings (title,trainername,trainingcategory,trainingrate,trainingstartdate,trainingenddate,trainingtime,trainingcapacity,trainingApprovalStatus,trainingGym,trainingFacility,recurring,dateUnavailable) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sssssssssssss", $param_trainingTitle, $param_trainerName, $param_trainingCategory, $param_rate, $param_SDate, $param_EDate, $param_Time, $param_MaxCap, $param_Status, $param_Gym, $param_Facility, $param_recurr_days, $param_dateUnavailable);
                        // Set parameters
                        $param_trainerid = $trainerId;
                        $param_trainingTitle = $_POST['trainingTitle'];
                        $param_trainingCategory = $trainingType;
                        $param_rate = $_POST['trainingRate'];
                        $param_SDate = date('Y-m-d', strtotime($_POST['trainingDate']));
                        $param_EDate = date('Y-m-d', strtotime($_POST['trainingDate']));
                        $param_Time = $_POST['startTime'];
                        $param_Gym = $gym;
                        $param_Facility = $_POST['Facility'];
                        $param_MaxCap = $_POST['trainingCapacityDropDown'];
                        $param_Status = "Pending";
                        $param_trainerName = $_SESSION['username'];
                        $param_recurr_days = "";
                        $param_dateUnavailable = $conflictDates;
                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Redirect to login page
                            $lastInsertedId = mysqli_stmt_insert_id($stmt);
//                            echo $lastInsertedId;
                            $_SESSION['groupSessionCreated'] = 'Group Training Request Submitted';
//                            echo "Stored into main table";
                        } else {
                            $errorMessage = "Something went wrong. Please try again later.";
                            $_SESSION['errorMessage'] = "Please try again later";
                        }
                    }

                    //  echo "No Recurr- Inserted";
                    $sql = "INSERT INTO grouptrainingschedule (trainerid,trainingTitle,trainingCategory,trainingRate,trainingDescription,trainingDate,trainingTime,trainingGym,trainingFacility,trainingMaxCapacity,trainingApprovalStatus,trainerName,GrpRecurrID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sssssssssssss", $param_trainerid, $param_trainingTitle, $param_trainingCategory, $param_rate, $param_Desc, $param_Date, $param_Time, $param_Gym, $param_Facility, $param_MaxCap, $param_Status, $param_trainerName, $param_recurrID);
                        // Set parameters
                        $param_trainerid = $trainerId;
                        $param_trainingTitle = $_POST['trainingTitle'];
                        $param_trainingCategory = $trainingType;
                        $param_rate = $_POST['trainingRate'];
                        $param_Desc = $_POST['trainingDesc'];
                        $param_Date = date('Y-m-d', strtotime($_POST['trainingDate']));
                        $param_Time = $_POST['startTime'];
                        $param_Gym = $gym;
                        $param_Facility = $_POST['Facility'];
                        $param_MaxCap = $_POST['trainingCapacityDropDown'];
                        $param_Status = "Pending";
                        $param_recurrID = $lastInsertedId;
                        $param_trainerName = $_SESSION['username'];
                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Redirect to login page
                            $_SESSION['groupSessionCreated'] = 'Group Training Request Submitted';
                        } else {
                            $errorMessage = "Something went wrong. Please try again later.";
                            $_SESSION['errorMessage'] = "Please try again later";
                        }
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
        }
        // Close connection
//        mysqli_close($link);
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
            <img class="fixed-ratio-resize" src="img/pc.jpg" alt="img/thumbnail_COVER.JPG"/>
        </div>
        <div class="container" style="padding-top:20px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-primary">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs" >
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown">Manage Group Training<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">

                                        <li><a href="#tab1primary" data-toggle="tab">View Existing Group Training Request</a></li>
                                        <li><a href="#tab2primary" data-toggle="tab">Request for Group Training Session</a></li>
                                        <li><a href="#tab3primary" data-toggle="tab">Past Group Trainings</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <center>
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="tab2primary">
                                        <div class="container" style="padding-top:20px;padding-right:80px;padding-left:50px;">
                                            <center><h2> Request New Group Training Session </h2></center>
                                            <?php
                                            if (isset($_SESSION['groupSessionCreated']) && $_SESSION['groupSessionCreated'] != '') {
                                                ?>
                                                <div class="alert alert-success">
                                                    <strong>Success!</strong> <?php echo $_SESSION['groupSessionCreated']; ?>
                                                </div>
                                                <?php
                                                unset($_SESSION['groupSessionCreated']);
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
                                                <div class="panel-body form-horizontal payment-form">
                                                    <div class="form-group">
                                                        <label for="concept" class="col-sm-3 control-label" required>Trainer Name:</label>
                                                        <div class="col-sm-5">
                                                            <input type="text" class="form-control" required="required" id="trainerName" name="trainerName" value="<?php echo $_SESSION['username'] ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="trainingTitle" class="col-sm-3 control-label">Group Training Title:</label>
                                                        <div class="col-sm-5">
                                                            <input type="text" class="form-control"  required="required" id="trainingTitle" name="trainingTitle" required>
                                                        </div>
                                                        <div id="msg"></div>

                                                    </div> 
                                                    <div class="form-group">
                                                        <label for="trainingCategory" class="col-sm-3 control-label">Training Category:</label>
                                                        <div class="col-sm-5">
                                                            <?php
                                                            $sql = "SELECT * FROM trainingtype ";
                                                            $res = mysqli_query($link, $sql);
                                                            ?>
                                                            <select class="form-control" name="trainingTypeDropDown" id="trainingTypeDropDown">
                                                                <!--                                                                <option value="showTraining" selected="selected">Show All Training Type</option>
                                                                -->                                                                                                                                <option value="">Please Select:</option>

                                                                <?php
                                                                while ($row = $res->fetch_assoc()) {
                                                                    echo '<option value=" ' . $row['ID'] . ' "> ' . $row['TRAINING_NAME'] . ' </option>';
                                                                }
                                                                ?>
                                                            </select>
                                                                <!--<input type = "text" class = "form-control" required = "required" id = "trainingCategory" name = "trainingCategory" > -->

                                                        </div>
                                                    </div>

                                                    <div class = "form-group">
                                                        <label for = "trainingRate" class = "col-sm-3 control-label">Training Rate/Hr:</label>
                                                        <div class = "col-sm-5">
                                                            <input type = "text" class = "form-control"  id = "trainingRate" name = "trainingRate" readonly="readonly" >
                                                        </div>
                                                        <div id = "amountBal"></div>
                                                    </div>


                                                    <div class = "form-group">
                                                        <label for = "trainingDesc" class = "col-sm-3 control-label">Training Description:</label>
                                                        <div class = "col-sm-5">

                                                            <input type = "text" class = "form-control" required = "required" id = "trainingDesc" name = "trainingDesc" >
                                                        </div>
                                                    </div>


                                                    <!--                                                    <div class = "form-group">
                                                                                                            <label for = "trainingDate" class = "col-sm-3 control-label">Training Date:</label>
                                                                                                            <div class = "col-sm-5">
                                                    
                                                                                                                <input type = "datepicker" class = "form-control" required = "required" id = "trainingDate" name = "trainingDate" >
                                                                                                                 <input  type="datepicker" placeholder="click to show datepicker"  id=""/>
                                                                                                            </div>
                                                                                                        </div>-->


                                                    <div class="form-group">
                                                        <label for = "trainingDesc" class = "col-sm-3 control-label">Training Date:</label>
                                                        <div class='col-sm-5'>
                                                            <div class='input-group input-append date' id='datePicker'>
                                                                <input type='datepicker' class="form-control" name="trainingDate" id="trainingDate" />
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group" id="recurr">
                                                        <label class="col-sm-3 control-label" for="recurring">Recurring Training:</label>
                                                        <div class="col-sm-5">          
                                                            <input type="checkbox" value="1" name="recurring[]" id="recurring">Mon
                                                            <input type="checkbox" value="2" name="recurring[]" id="recurring">Tues
                                                            <input type="checkbox" value="3" name="recurring[]" id="recurring">Wed
                                                            <input type="checkbox" value="4" name="recurring[]" id="recurring">Thur
                                                            <input type="checkbox" value="5" name="recurring[]" id="recurring">Fri
                                                            <input type="checkbox" value="6" name="recurring[]" id="recurring">Sat
                                                            <input type="checkbox" value="7" name="recurring[]" id="recurring">Sun
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="display:none" id="endDateRecur">
                                                        <label class="col-sm-3 control-label" for="endDate"><span style="color: red">*</span>Training End Date:</label>
                                                        <div class="col-sm-5">          
<!--                                                            <input type="text" class="form-control" id="endDate" name="endDate" placeholder="YYYY-MM-DD">-->
                                                            <div class='input-group input-append date' id='endDate'>
                                                                <input type='datepicker' class="form-control" name="trainingendDate" id="trainingendDate" />
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class = "form-group">
                                                        <label for = "trainingTime" class = "col-sm-3 control-label">Training Time:</label>
                                                        <div class = "col-sm-5">

    <!--                                                            <input type = "text" class = "form-control" required = "required" id = "trainingTime" name = "trainingTime" >-->
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
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="trainingCategory" class="col-sm-3 control-label">Gym Location:</label>
                                                        <div class="col-sm-5">
                                                            <?php
                                                            $sql = "SELECT * FROM gym ";
                                                            $res = mysqli_query($link, $sql);
                                                            mysqli_close($link);
                                                            ?>
                                                            <select class="form-control" name="gymLocationDropDown" id="gymLocationDropDown">
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
                                                    </div>
                                                    <div class = "form-group">
                                                        <label for = "trainingVenue" class = "col-sm-3 control-label">Facility:</label>
                                                        <div class = "col-sm-5">

                                                            <select class="form-control" id="Facility" name="Facility">
                                                                <option value="">Please Select Gym Location</option>
                                                            </select>
                                                            <!--<input type = "text" class = "form-control" required = "required" id = "trainingVenue" name = "trainingVenue" >-->
                                                        </div>
                                                    </div>


                                                    <div class = "form-group">
                                                        <label for = "trainingCapacity" class = "col-sm-3 control-label">Maximum Capacity:</label>
                                                        <div class = "col-sm-5">

    <!--                                                            <input type = "number" class = "form-control" min='0' required = "required" id = "trainingCapacity" name = "trainingCapacity" >-->
                                                            <select class="form-control" name="trainingCapacityDropDown" id="trainingCapacityDropDown">

                                                                <option value="">Please Select Facility:</option>


                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type = "submit" id = "requestTraining" name = "requestTraining" class = "btn btn-primary" value = "Request Training Session" >


                                            </form>



                                        </div></div>
                                    <div class = "tab-pane fade in active" id = "tab1primary">  <div class="container" style="padding-top:20px;padding-right:80px; ">
                                            <div class="col-md-12">
                                                <div class="panel panel-danger">
                                                    <div class="panel-heading "> 
                                                        <b>Group Training Sessions Pending Approval</b>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-12">

                                                                <table id="tableNotVerifiedRequests"
                                                                       data-show-columns="true"
                                                                       data-height="460">
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>				
                                                </div>
                                            </div>
                                        </div></div>

                                    <div class = "tab-pane fade" id = "tab3primary"> <div class = "container" style = "padding-top:20px;padding-right:80px;" >
                                            <div class="col-md-12">
                                                <div class="panel panel-success">
                                                    <div class="panel-heading "> 
                                                        <b>Past Group Requests ( Approved / Rejected ) </b>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-12">

                                                                <table id="tablePastRequests"
                                                                       data-show-columns="true"
                                                                       data-height="460">
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>				
                                                </div>
                                            </div>



                                        </div> <!-- ./container --></div>
                                    <div class="tab-pane fade" id="tab4primary">Primary 4</div>
                                    <div class="tab-pane fade" id="tab5primary">Primary 5</div>
                                </div>
                            </div></center>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <?php include("footer.html"); ?>
    <!--This is the Javascript for the table for view all user details--> 
    <script type="text/javascript">
        $(document).ready(function ()
        {
            $("input[name='recurring[]']").click(function () {
                if (jQuery('#recurr input[type=checkbox]:checked').length) {
                    $("#endDateRecur").show();
                } else {
                    $("#endDateRecur").hide();
                }

            });
            //Upon training type being selected, the price of the category of training will be updated as well 
            $("#trainingTypeDropDown").change(function ()
            {
                var id = $(this).find(":selected").val();
                //   alert(id);
                var trainingId = id;
                $.ajax
                        ({
                            type: "POST",
                            url: 'PHPCodes/getTrainingRate.php',
                            data: {trainingId: trainingId},
                            cache: false,
                            success: function (r)
                            {
//                        $("#trainingRate").html(r);
                                document.getElementById("trainingRate").value = r;
                                //alert(r);
                            }
                        })
                        ;
            });

            $("#gymLocationDropDown").change(function ()
            {
                //Upon gym location selected update 
                var id = $(this).find(":selected").val();
//                alert(id);

                //   alert(id);
                var gymId = id;
                $.ajax
                        ({
                            type: "POST",
                            url: 'PHPCodes/getGymFacilities.php',
                            data: {gymId: gymId},
                            cache: false,
                            success: function (r)
                            {
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
                //Upon gym location selected update 
                //Get the maximum capacity and set a limit on the limit of maximum capacity 
                var venueSelected = $(this).find(":selected").val();
                var maxPax = venueSelected.split(':')[1];
                maxPax = maxPax.replace(")", "");
                alert(maxPax);
                var capacityDropDown = document.getElementById("trainingCapacityDropDown");

                capacityDropDown.innerHTML = "";
                for (var i = 0; i <= maxPax; i++) {
                    var opt = i;
                    var el = document.createElement("option");
                    el.textContent = i;
                    el.value = i;
                    capacityDropDown.appendChild(el);
                }

            });

            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 14);

            $('#trainingendDate')
                    .datepicker({
                        autoclose: true,
                        format: 'mm/dd/yyyy',
                        startDate: today

                    })

//                    .on('changeDate', function (e) {
//                        // Revalidate the date field
//                        $('#eventForm').formValidation('revalidateField', 'date');
//                    });

            $('#datePicker')
                    .datepicker({
                        autoclose: true,

                        format: 'mm/dd/yyyy',
                        startDate: today

                    })

//                    .on('changeDate', function (e) {
//                        // Revalidate the date field
//                        $('#eventForm').formValidation('revalidateField', 'date');
//                    });
        });




        //Table to display current status of Gym Request to user 
        var $table = $('#tableNotVerifiedRequests');
        $table.bootstrapTable({
            url: 'PHPCodes/TrainerListGroupSessionsPending.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [

                {
                    field: 'num',
                    title: '#',
                    sortable: true,
                },
                {
                    field: 'dateUnavailable',
                    title: 'Location Available',
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
                },
            ],
        });

        //Display past records
        var $table = $('#tablePastRequests');
        $table.bootstrapTable({
            url: 'PHPCodes/TrainerListPastGroupSessionRequest.php',
            search: true,
            pagination: true,
            buttonsClass: 'primary',
            showFooter: true,
            minimumCountColumns: 2,
            columns: [
                 {
                    field: 'num',
                    title: '#',
                    sortable: true,
                },
                {
                    field: 'dateUnavailable',
                    title: 'Location Available',
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
                },
            ],
        });

    </script>

</html>
