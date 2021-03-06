<!---Displaying of error message !-->
<?php
include_once('DBConfig.php');
session_start();
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    echo "<script type='text/javascript'>alert('$message');</script>";
}

//Show the specific calendar to specific user.
if (!isset($_SESSION['username'])) {
    $sql = "SELECT * FROM trainerschedule";
} else {
    $name = $_SESSION['username'];

    // updated when role == trainer, if trainer deletes PT, removed from the trainer's own calendar too
    if ($_SESSION['role'] == 'Trainer') {
        $sql = "SELECT * FROM trainerschedule WHERE name = '$name' AND ((eventType = 'pt' AND trainingstatus != 'Cancelled') OR (eventType = 'ot' AND trainingstatus!='Cancelled'))";
    } else if ($_SESSION['role'] == 'Trainee') {
        $sql = "SELECT * FROM trainerschedule WHERE traineeid = '$name' OR name = '$name'";
    } else {
        $sql = "SELECT * FROM trainerschedule";
    }
}

$username = $_SESSION['username'];

$events = array();
$group = array();
$req = $bdd->prepare($sql);
$req->execute();
$events = $req->fetchAll();
//array_merge($events, $personal);

if ($_SESSION['role'] == 'Trainee') {
    $sql1 = "SELECT * FROM grouptrainingschedule LEFT JOIN gttrainees ON grouptrainingschedule.GrpRecurrID = gttrainees.recurringId WHERE gttrainees.username = '$username'";
} else if ($_SESSION['role'] == 'Trainer') {
    $sql1 = "SELECT * FROM grouptrainingschedule WHERE trainerName='$username'";
}

$req = $bdd->prepare($sql1);
$req->execute();
$group = $req->fetchAll();
$events = array_merge($events, $group);
//print_r($events);
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
        <!-- FullCalendar -->
        <link href='css/fullcalendar.css' rel='stylesheet' />
        <link href='css/circle.css' rel='stylesheet'/>
    </head>

    <body>
        <?php
        include("navigation.php");
        ?>
        <div class="container" style="padding-top:100px;">
            <center><h1>Personal Calendar</h1></center>
            <center>
                <table>                    
                    <tr>
                        <?php if ($_SESSION['role'] == 'Trainer') { ?>
                            <td><input class="circle" style="background: #005800; border: none;" readonly></td>
                            <td style="padding-left: 5px; margin-bottom: 50px;">Available PT</td>
                            <td style="padding-left: 20px;"><input class="circle" style="background: #67d967; border: none;" readonly></td>
                            <td style="padding-left: 5px;">Occupied PT</td>
                            <td style="padding-left: 20px;"><input class="circle" style="background: #6299f7; border: none;" readonly></td>
                            <td style="padding-left: 5px;">Group Training</td>
                            <td style="padding-left: 20px;"><input class="circle" style="background: #adc9fb; border: none;" readonly></td>
                            <td style="padding-left: 5px;">Group Training (Full)</td>
                        <?php } ?>
                        <?php if ($_SESSION['role'] == 'Trainee') { ?>
                            <td style="padding-left: 20px;"><input class="circle" style="background: #67d967; border: none;" readonly></td>
                            <td style="padding-left: 5px;">Personal Training</td>
                            <td style="padding-left: 20px;"><input class="circle" style="background: #6299f7; border: none;" readonly></td>
                            <td style="padding-left: 5px;">Group Training</td>
                        <?php } ?>
                        <td style="padding-left: 20px;"><input class="circle" style="background: #b6abfb; border: none;" readonly></td>
                        <td style="padding-left: 5px;">Own Training Schedule</td>
                    </tr>
                </table>
            </center>
            <div id="calendar" class="monthly" style="margin: 50px 0;">
                <div class="row">
                    <!-- ADD Modal -->
                    <div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form class="form-horizontal" method="POST" action="CalendarReqCodes/addEvent.php">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">New Training Session</h4>
                                    </div>
                                    <div class="modal-body">
                                        <span style="color: red;font-size:14px;">* Mandatory fields</span>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="eventtype"><span style="color: red">*</span>Type Of Training: </label>
                                            <div class="col-md-7">
                                                <?php
                                                if ($_SESSION['role'] == 'Trainer') {
                                                    ?>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="eventtype" id="pt" value="pt" checked>Personal Training
                                                    </label>
                                                <?php }
                                                ?>
                                                <label class="radio-inline">
                                                    <input type="radio" name="eventtype" id="ot" value="ot" checked>Own Training
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="trainerName"><span style="color: red">*</span>
                                                <?php if ($_SESSION['role'] == 'Trainer') { ?>
                                                    Trainer Name:
                                                <?php } else { ?>
                                                    Trainee Name:
                                                <?php } ?>
                                            </label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" id="trainerName" name ="trainerName" readonly value="<?php echo $_SESSION['username'] ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="trainingTitle"><span style="color: red">*</span>Training Title:</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" id="trainingTitle" name="trainingTitle" placeholder="Training Title" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="startDate"><span style="color: red">*</span>Start Date:</label>
                                            <div class="col-md-7">          
                                                <input type="text" class="form-control" id="startDate" name="startDate" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="startTime"><span style="color: red">*</span>Start Time:</label>
                                            <div class="col-md-7">          
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
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="recur">
                                            <label class="control-label col-md-4" for="recurring">Recurring:</label>
                                            <div class="col-md-7">          
                                                <input type="checkbox" value="1" name="recurring[]" id="recurring">Mon
                                                <input type="checkbox" value="2" name="recurring[]" id="recurring">Tues
                                                <input type="checkbox" value="3" name="recurring[]" id="recurring">Wed
                                                <input type="checkbox" value="4" name="recurring[]" id="recurring">Thur
                                                <input type="checkbox" value="5" name="recurring[]" id="recurring">Fri
                                                <input type="checkbox" value="6" name="recurring[]" id="recurring">Sat
                                                <input type="checkbox" value="0" name="recurring[]" id="recurring">Sun
                                            </div>
                                        </div>
                                        <div class="form-group" style="display:none" id="endDateRecur">
                                            <label class="control-label col-md-4" for="endDate">End Date:</label>
                                            <div class="col-md-7">          
                                                <div class='input-group input-append date' id='datePicker'>
                                                    <input type='text' class="form-control" name="endDate" id="endDate" placeholder="DD-MM-YYYY" readonly style="background:white;"/>
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>                                                
                                            </div>
                                        </div>

                                        <div class="form-group" style="display:none !important;" id="trainingTypeDDL">
                                            <label for="trainingType" class="col-md-4 control-label"><span style="color: red">*</span>Training Category:</label>
                                            <div class="col-md-7">
                                                <?php
                                                $sql = "SELECT * FROM trainingtype ";
                                                $res = mysqli_query($link, $sql);
                                                ?>
                                                <select class="form-control" name="trainingType" id="trainingType" required disabled>
                                                    <option value="" selected disabled hidden>Choose Training Category</option>
                                                    <?php
                                                    while ($row = $res->fetch_assoc()) {
                                                        echo '<option value=" ' . $row['ID'] . ' "> ' . $row['TRAINING_NAME'] . ' </option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class = "form-group" style="display:none" id="rateText">
                                            <label for="rate" class="col-md-4 control-label"><span style="color: red">*</span>Training Rate/Hr:</label>
                                            <div class="col-md-7">
                                                <input type = "text" class="form-control" id="rate" name="rate" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display:none" id="gymLocationDDL">
                                            <label for="gymLocation" class="control-label col-md-4"><span style="color: red">*</span>Gym Location:</label>
                                            <div class="col-md-7">
                                                <?php
                                                $sql = "SELECT * FROM gym ";
                                                $res = mysqli_query($link, $sql);
                                                ?>
                                                <select class="form-control" name="gymLocation" id="gymLocation" disabled required>
                                                    <option value="" selected disabled hidden>Choose Gym Location</option>

                                                    <?php
                                                    while ($row = $res->fetch_assoc()) {
                                                        echo '<option value="' . $row['id'] . '">' . $row['gymName'] . ' </option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class = "form-group" style="display:none" id="facilityText">
                                            <label for="facility" class="col-md-4 control-label"><span style="color: red">*</span>Facility:</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" id="facility" name="facility" value="Open Gym" readonly>                                             
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">                                      
                                        <button type="submit" class="btn btn-primary" name="add">Add</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!--added to prevent non-login from editting--!>
                    <?php if (isset($_SESSION['username'])) { ?>                                                                                       
                        <!-- EDIT Modal -->
                        <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form class="form-horizontal" method="POST" action="CalendarReqCodes/editEventTitle.php">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Event</h4>
                                        </div>
                                        <div class="modal-body">
                                            <span style="color: red;font-size:14px;">* Mandatory fields</span>
                                            <div class="form-group">
                                                <label for="editEventType" class="col-md-4 control-label"><span style="color: red">*</span>Type Of Training:</label>
                                                <div class="col-md-7">
                                                    <input type="text" name="editEventType" class="form-control" id="editEventType" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="editName" id="editNameLabel" class="col-md-4 control-label"><span style="color: red">*</span>
                                                    
                                                </label>
                                                <div class="col-md-7">
                                                    <input type="text" name="editName" class="form-control" id="editName" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="editTitle" class="col-md-4 control-label"><span style="color: red">*</span>Training Title:</label>
                                                <div class="col-md-7">
                                                    <input type="text" name="editTitle" class="form-control" id="editTitle" placeholder="Training Title" required>
                                                </div>
                                            </div>

                                            <div class="form-group" id="editDescriptionText" style="display:none">
                                                <label for="editDescription" class="col-md-4 control-label"><span style="color: red">*</span>Training Description:</label>
                                                <div class="col-md-7">
                                                    <input type="text" name="editDescription" class="form-control" id="editDescription" placeholder="Training Description" disabled required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="editStartDate" class="col-md-4 control-label"><span style="color: red">*</span>Start Date:</label>
                                                <div class="col-md-7">
                                                    <div class='input-group input-append date' id='editStartDatePicker'>
                                                        <input type='text' class="form-control" name="editStartDate" id="editStartDate" placeholder="DD-MM-YYYY" required />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>       
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="editStartTime" class="col-md-4 control-label"><span style="color: red">*</span>Start Time:</label>
                                                <div class="col-md-7">
                                                    <select name="editStartTime" class="form-control" id="editStartTime" required>
                                                        <option value="" disabled hidden>Choose Time</option>
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
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" id="editRecur" style="display:none!important;">
                                                <label class="control-label col-md-4" for="editRecurring">Recurring:</label>
                                                <div class="col-md-7">          
                                                    <input type="checkbox" value="1" name="editRecurring[]" id="editRecurring"  readonly>Mon
                                                    <input type="checkbox" value="2" name="editRecurring[]" id="editRecurring"  readonly>Tues
                                                    <input type="checkbox" value="3" name="editRecurring[]" id="editRecurring"  readonly>Wed
                                                    <input type="checkbox" value="4" name="editRecurring[]" id="editRecurring"  readonly>Thur
                                                    <input type="checkbox" value="5" name="editRecurring[]" id="editRecurring"  readonly>Fri
                                                    <input type="checkbox" value="6" name="editRecurring[]" id="editRecurring"  readonly>Sat
                                                    <input type="checkbox" value="0" name="editRecurring[]" id="editRecurring"  readonly>Sun
                                                </div>
                                            </div>

                                            <div class="form-group" style="display:none!important;" id="editEndDateRecur">
                                                <label class="control-label col-md-4" for="editEndDate">End Date:</label>
                                                <div class="col-md-7">          
                                                    <div class='input-group input-append date' id='editEndDatePicker'>
                                                        <input type='text' class="form-control" name="editEndDate" id="editEndDate" placeholder="DD-MM-YYYY" readonly disabled/>
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                            </div>   

                                            <div class="form-group" style="display:none !important;" id="editTrainingTypeDDL">
                                                <label for="editTrainingType" class="col-md-4 control-label"><span style="color: red">*</span>Training Category:</label>
                                                <div class="col-md-7">
                                                    <?php
                                                    $sql = "SELECT * FROM trainingtype ";
                                                    $res = mysqli_query($link, $sql);
                                                    ?>
                                                    <select class="form-control" name="editTrainingType" id="editTrainingType" disabled required>
                                                        <option value="" disabled hidden>Choose Training Category</option>
                                                        <?php
                                                        while ($row = $res->fetch_assoc()) {
                                                            echo '<option value="' . $row['ID'] . '">' . $row['TRAINING_NAME'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none !important;" id="editRateText">
                                                <label for="editRate" class="col-md-4 control-label"><span style="color: red">*</span>Training Rate/Hr:</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" id="editRate" name="editRate" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none" id="editGymLocationDDL">
                                                <label for="editGymLocation" class="control-label col-md-4"><span style="color: red">*</span>Gym Location:</label>
                                                <div class="col-md-7">
                                                    <?php
                                                    $sql = "SELECT * FROM gym ";
                                                    $res = mysqli_query($link, $sql);
                                                    ?>
                                                    <select class="form-control" name="editGymLocation" id="editGymLocation" disabled required>
                                                        <option value="" disabled hidden>Choose Gym Location</option>

                                                        <?php
                                                        while ($row = $res->fetch_assoc()) {
                                                            echo '<option value="' . $row['id'] . '">' . $row['gymName'] . ' </option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class = "form-group" style="display:none" id="editFacilityText">
                                                <label for="editFacility" class="col-md-4 control-label"><span style="color: red">*</span>Facility:</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" id="editFacility" name="editFacility" readonly>    
                                                </div>
                                            </div>

                                            <div class="form-group" style="display:none !important;" id="editCapacityText">
                                                <label for="editCapacity" class="col-md-4 control-label"><span style="color: red">*</span>Maximum Capacity:</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" id="editCapacity" name="editCapacity" readonly>
                                                </div>
                                            </div>

                                            <input type="hidden" name="id" class="form-control" id="id">
                                        </div>
                                        <div class="modal-footer">                                            
                                            <button type="submit" class="btn btn-primary" id="savechanges" name="savechanges">Save changes</button>

                                            <!--KEEGAN-->
                                            <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                            <h6 class="modal-title" id="myModalLabel">Confirm Cancellation</h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <p>You are about to cancel your training session!</p>                                                              

                                                        </div>

                                                        <div class="modal-footer">
                                                            </form>
                                                            <form action="cancelTrainingEmailScript.php" method="post"> 
                                                                <button type="submit" name="id" id="id" class="btn btn-danger">Confirm</button>
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                            </form>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="button" id="myBtn" class="btn btn-danger" value="Cancel training" formnovalidate data-toggle="modal" data-target="#confirm-delete"></a>
                                            <!-- Button for cancelling training Plus modal inside-->

                                            <!-- END OF KEEGAN -->  

                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <!-- /.container -->
            </div>

    </body>

    <?php include("calendarscripts.html"); ?>

    <script>

        $(document).ready(function(){

            // END-DATE DATEPICKER
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $("#datePicker, #editStartDatePicker, #editEndDatePicker").datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy',
                startDate: today
            });
            //prevent user to type anything in start date during editing
            $('#editStartDate').keypress(function(e) {
                e.preventDefault();
            });
            // TO DISPLAY END DATE IF RECUR IS CHECKED
            $("input[name='recurring[]']").click(function(){
                if (jQuery('#recur input[type=checkbox]:checked').length){
                    $("#endDateRecur").show();
                } else {
                    $("#endDateRecur").hide();
                }
            });
            // dont allow it to be checked for group recurring editting
             $("input[name='editRecurring[]']").click(function(){
                event.preventDefault();
            });
            
            // TO DISPLAY RATE, TRAINING TYPE, LOCATION, FACILITY IF PT IS CHECKED
            $("#ot, #pt").change(function(){
                if ($("#pt").is(":checked")){
                    $("#rateText").show();
                    $("#trainingTypeDDL").show();
                    $("#gymLocationDDL").show();
                    $("#facilityText").show();
                    jQuery("#trainingType").removeAttr("disabled");
                    jQuery("#gymLocation").removeAttr("disabled");
                } else {
                    $("#rateText").hide();
                    $("#trainingTypeDDL").hide();
                    $("#gymLocationDDL").hide();
                    $("#facilityText").hide();
                    jQuery("#trainingType").attr("disabled", 'disabled');
                    jQuery("#gymLocation").attr("disabled", 'disabled');
                }
            });
            //Upon training type being selected, the price of the category of training will be updated as well 
            $("#trainingType, #editTrainingType").change(function(){
                var id = $(this).find(":selected").val();
                var trainingId = id;

                $.ajax({
                    type: "POST",
                    url: 'PHPCodes/getTrainingRate.php',
                    data: {trainingId: trainingId},
                    cache: false,
                    success: function (r) {
                    document.getElementById("rate").value = r;
                    document.getElementById("editRate").value = r;
                    }
                });
            });
            // TO PREVENT HIDE/SHOW BUGS FOR EDIT MODAL
            $('#ModalEdit').on('hidden.bs.modal', function () {
                location.reload();
            });
        // FULL CALENDAR
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            eventLimit: true, // allow "more" link when too many events

            <?php if (!isset($_SESSION['username'])) { ?>
                editable: false,
                selectable: false,
            <?php } else { ?>
                editable: true,
                selectable: true,
            <?php } ?>

            displayEventTime: false, // hide the time. Eg 2a, 12p

            // When you click the cell in the calendar
            select: function (start, end) { //START OF SELECT FUNC.
                if (start.isBefore(moment())) {
                    $('#calendar').fullCalendar('unselect');
                    $('#ModalAdd').modal('hide');
                }
                else {
                    $('#ModalAdd #startDate').val(moment(start).format('DD-MM-YYYY'));
                    $('#ModalAdd').modal('show');
                }
            }, // END OF SELECT FUNC.

            // When you double click the event in the cell
            eventRender: function (event, element, view) { //START OF EVENT RENDER FUNC.                
                if (event.start.isBefore(moment().subtract(2, 'days'))) {
                    element.bind('dblclick', function () {
                        $('#calendar').fullCalendar('unselect');
                        $('#ModalEdit').modal('hide');
                        alert("You are unable to make changes to past event dates!");
                    });
                }
                else {
                    element.bind('dblclick', function () {
                        var eventTitle = (event.title).split(" ");
                        var realStartDate = (event.realStartDate).split(" ");
                        var concatTitle = "";
                        if (event.color == "#67d967"){
                            <?php if ($_SESSION['role'] == 'Trainer') { ?>
                                for (var i = 2; i < eventTitle.length; i++) {
                                    eventTitle[i]
                                    concatTitle += eventTitle[i] + " ";
                                }
                            <?php } else { ?>
                                for (var i = 1; i < eventTitle.length; i++) {
                                    concatTitle += eventTitle[i] + " ";
                                }
                            <?php } ?>
                        } else {
                            for (var i = 1; i < eventTitle.length; i++) {
                                concatTitle += eventTitle[i] + " ";
                            }
                        }
                        concatTitle = concatTitle.replace("/", "");
                        // based on color code, change the label name (trainer's name / trainee's name)
                        <?php if ($_SESSION['role'] == 'Trainer') { ?>
                            if (event.color == "#005800" || event.color == "#b6abfb" || event.color == "#67d967") {
                                document.getElementById('editNameLabel').innerHTML = "Trainer's Name";
                            } else {
                                document.getElementById('editNameLabel').innerHTML = "Trainer's Name";
                            }
                        <?php } else { ?>
                            if (event.color == "#005800" || event.color == "#b6abfb"){
                                document.getElementById('editNameLabel').innerHTML = "Trainee's Name";
                            } else if (event.color == "#67d967"){
                                document.getElementById("editNameLabel").innerHTML = "Trainer's Name";
                            } else{
                                document.getElementById('editNameLabel').innerHTML = "Trainer's Name";
                            }
                        <?php } ?>


                        if (event.eventType == "pt"){
                            
                            event.eventType = "Personal Training";
                            $('#editRateText').show();
                            $('#editTrainingTypeDDL').show();
                            $('#editGymLocationDDL').show();
                            $('#editFacilityText').show();
                            jQuery("#editTrainingType").removeAttr("disabled");
                            jQuery("#editGymLocation").removeAttr("disabled");
                            jQuery("#editRecurring").attr('disabled', 'disabled');

                        } else if (event.eventType == "ot") {

                            event.eventType = "Own Training";
                            jQuery("#editRecurring").attr('disabled', 'disabled');

                        } else if (event.eventType == "gt") {

                            event.eventType = "Group Training";

                            $('#editRateText').show();
                            $('#editTrainingTypeDDL').show();
                            $('#editGymLocationDDL').show();
                            $('#editFacilityText').show();
                            $('#editDescriptionText').show();
                            $('#editCapacityText').show();
                            $('#myBtn').hide();

                            jQuery("#editDescription").removeAttr("disabled");
                            jQuery("#editGymLocation").attr('readonly', true);
                            jQuery("#editTrainingType").attr('readonly', true);
                            jQuery("#editStartDate").attr('readonly', true);
                            jQuery("#editStartTime").attr('readonly', true);
                            jQuery("#editTrainingType").attr('disabled', 'disabled');
                            jQuery("#editStartTime").attr('disabled', 'disabled');
                            $("#editRecurring").prop("checked", false);
                            $('#editStartDatePicker').datepicker('remove');
                            
                            var arrayValues = (event.recur).split(",");
                            if (arrayValues == "") {
                                $('#editEndDateRecur').hide();
                            } else {
                                for (var i = 0; i < arrayValues.length; i++) {
                                    $('#ModalEdit #editRecurring').val(arrayValues);
                                }
                                $('#editEndDateRecur').show();
                                $('#editRecur').show();
                            }
                        }

                        if (event.traineeId == "<?php echo $_SESSION['username']; ?>"){
                            $('#editEndDateRecur').hide();
                            $('#savechanges').hide();
                            $('#editRecur').hide();
                            jQuery("#editTitle").attr('readonly', true);
                            jQuery("#editGymLocation").attr('readonly', true);
                            jQuery("#editStartDate").attr('readonly', true);
                            jQuery("#editRecurring").attr('disabled', 'disabled');
                            jQuery("#editGymLocation").attr('disabled', 'disabled');
                            jQuery("#editTrainingType").attr('disabled', 'disabled');
                            jQuery("#editStartTime").attr('disabled', 'disabled');
                            $('#editStartDatePicker').datepicker('remove');
                        }

                        $('#ModalEdit #id').val(event.id);
                        $('#ModalEdit #editEventType').val(event.eventType);
                        $('#ModalEdit #editName').val(event.name);
                        $('#ModalEdit #editTitle').val(concatTitle);
                        $('#ModalEdit #editStartDate').val(moment(realStartDate[0]).format('DD-MM-YYYY'));
                        $('#ModalEdit #editEndDate').val(moment(event.realEndDate).format('DD-MM-YYYY'));
                        $('#ModalEdit #editStartTime').val(event.startT);
                        $('#ModalEdit #editTrainingType').val(event.trainingCategory);
                        $('#ModalEdit #editGymLocation').val(event.gymLocation);
                        $('#ModalEdit #editFacility').val(event.facility);
                        $('#ModalEdit #editRate').val(event.rate);
                        $('#ModalEdit #editDescription').val(event.description);
                        $('#ModalEdit #editCapacity').val(event.capacity);

                        if ("<?php echo $_SESSION['role']; ?>" != "Trainee") {
                            $('#ModalEdit').modal('show');
                        } else {
                            if (event.eventType != "Group Training") {
                                $('#ModalEdit').modal('show');
                            }
                        }
                        
//                document.getElementById("savechanges").style.visibility = 'visible';
//                document.getElementById("savechanges").show();
                //kee for cancelling training - special requirement where todays date is > 2 button will be disable //
                if (event.start > (moment().add(2, 'days'))) {
                document.getElementById("myBtn").disabled = false;
                }
                else
                {
                document.getElementById("myBtn").disabled = true;
                }
                //kee for cancelling training - special requirement where todays date is > 2 button will be disable //
                });
                }
                }, // END OF RENDER FUNC.

                events: [ // START OF EVENT OBJECT
<?php
foreach ($events as $event):
    //Search for PT / OT 
    if ($event['eventType'] == 'gt') {
        $title = $event['trainingTime'] . ' ' . $event['trainingTitle'];

        // RETRIEVE GYM NAME 
        $gymLocationQuery = mysqli_prepare($link, "SELECT id FROM gym WHERE gymName = ?");
        mysqli_stmt_bind_param($gymLocationQuery, "s", $gymLocation);
        $gymLocation = $event['trainingGym'];
        mysqli_stmt_execute($gymLocationQuery);
        mysqli_stmt_bind_result($gymLocationQuery, $ID);
        while ($gymLocationQuery->fetch()) {
            $venue = $ID;
        }

        // Retrieve training category id
        if ($event['trainingCategory'] != "") {
            $categoryQuery = mysqli_prepare($link, "SELECT ID FROM trainingtype WHERE TRAINING_NAME = ?");
            mysqli_stmt_bind_param($categoryQuery, "s", $category);
            $category = $event['trainingCategory'];
            mysqli_stmt_execute($categoryQuery);
            mysqli_stmt_bind_result($categoryQuery, $ID);
            while ($categoryQuery->fetch()) {
                $trainingCategory = $ID;
            }
        } else {
            $trainingCategory = "";
        }

        // Retrieve training category id
        if ($event['GrpRecurrID'] != "") {
            $recurQuery = mysqli_prepare($link, "SELECT recurring, trainingenddate FROM grouptrainings GT INNER JOIN grouptrainingschedule GTS ON GT.ID = ?");
            mysqli_stmt_bind_param($recurQuery, "s", $recur);
            $recur = $event['GrpRecurrID'];
            mysqli_stmt_execute($recurQuery);
            $recurResult = $recurQuery->get_result();

            while ($recurring = mysqli_fetch_assoc($recurResult)) {
                $recur = $recurring['recurring'];
                $trainingenddate = $recurring['trainingenddate'];
            }
        } else {
            $recur = "";
            $trainingenddate = "";
        }

        if ($_SESSION['role'] == 'Trainee') {
            $color = '#6299f7';
        } else if ($_SESSION['role'] == 'Trainer') {
            $color = '#6299f7';

            if ($event['trainingMaxCapacity'] <= $event['currentCap']) {
                $color = '#adc9fb';
            }
        }
        ?>
                        {
                        id: '<?php echo $event['id']; ?>',
                                title: '<?php echo $title; ?>',
                                start: '<?php echo $event['trainingDate']; ?>T00:00:00',
                                name: '<?php echo $event['trainerName']; ?>',
                                eventType: '<?php echo $event['eventType']; ?>',
                                trainingCategory: '<?php echo $trainingCategory ?>',
                                gymLocation: '<?php echo $venue; ?>',
                                facility: '<?php echo $event['trainingFacility']; ?>',
                                rate: '<?php echo $event['trainingRate']; ?>',
                                startT: '<?php echo $event['trainingTime']; ?>',
                                realStartDate: '<?php echo $event['trainingDate']; ?>',
                                recur: '<?php echo $recur; ?>',
                                description: '<?php echo $event['trainingDescription']; ?>',
                                capacity: '<?php echo $event['trainingMaxCapacity']; ?>',
                                color: '<?php echo $color; ?>',
                        },
    <?php
    } else if (($event['eventType'] == 'ot') || ($event['eventType'] == 'pt')) {
//        $recur = $event['recur'];
        $end = explode(" ", $event['enddate']);
        $titleWithTime = $event['starttime'] . ' ' . $event['title'];

        // RETRIEVE GYM NAME 
        $gymLocationQuery = mysqli_prepare($link, "SELECT id FROM gym WHERE gymName = ?");
        mysqli_stmt_bind_param($gymLocationQuery, "s", $gymLocation);
        $gymLocation = $event['venue'];
        mysqli_stmt_execute($gymLocationQuery);
        mysqli_stmt_bind_result($gymLocationQuery, $ID);
        while ($gymLocationQuery->fetch()) {
            $venue = $ID;
        }
        // RETRIEVE Facility Name 
        $facilityQuery = mysqli_prepare($link, "SELECT facilityName FROM gymfacility WHERE id = ?");
        mysqli_stmt_bind_param($facilityQuery, "s", $facilityID);
        $facilityID = $event['facility'];
        mysqli_stmt_execute($facilityQuery);
        mysqli_stmt_bind_result($facilityQuery, $ID);
        while ($facilityQuery->fetch()) {
            $facility = $ID;
        }

        // Retrieve training category id
        if ($event['trainingCategory'] != "") {
            $categoryQuery = mysqli_prepare($link, "SELECT ID FROM trainingtype WHERE TRAINING_NAME = ?");
            mysqli_stmt_bind_param($categoryQuery, "s", $category);
            $category = $event['trainingCategory'];
            mysqli_stmt_execute($categoryQuery);
            mysqli_stmt_bind_result($categoryQuery, $ID);
            while ($categoryQuery->fetch()) {
                $trainingCategory = $ID;
            }
        } else {
            $trainingCategory = "";
        }

        // HERE BOSS - TO REMOVE THIS LINE OF COMMENT
        // added this if else to change color + title for both trainee & trainee
        if ($_SESSION['role'] == 'Trainer') {
            $traineeId = $event['traineeid'];
            $title = $event['title'];
            $color = '#005800';
            // echo 'alert("'. $event['eventType'] .'");';

            if ($traineeId == NULL && $event['eventType'] == 'pt') { // no trainee sign up
                $title = $event['starttime'] . " " . $title;
                $color = $color;
            } else if ($event['eventType'] == 'ot') { // trainer's own training session
                $title = $event['starttime'] . " " . $title;
                $color = '#b6abfb';
                $venue="";
                $facility="";
            } else if ($event['eventType'] == 'pt' && $traineeId != NULL) { // trainee signed up for training
                $title = $event['starttime'] . " (" . $traineeId . ") " . $title;
                $color = '#67d967';
            }
        } else if ($_SESSION['role'] == 'Trainee') {
            $traineeId = $event['traineeid'];
            $title = $event['title'];

            if ($event['eventType'] == 'ot') { // trainee own training session
                $title = $event['starttime'] . " " . $title;
                $color = '#b6abfb';
                $venue="";
                $facility="";
            } else if ($event['eventType'] == 'pt' && $traineeId == $_SESSION['username']) { // trainee signed up for personal training session
                $title = $event['starttime'] . " " . $title;
                $color = '#67d967';
            }

            // TODO: cancelled trainings by trainers need to put?
        }
        ?>
                        {
                        id: '<?php echo $event['trainingid']; ?>',
                                title: '<?php echo $title; ?>',
                                start: '<?php echo $event['startdate']; ?>',
                                end: '<?php echo $end[0]; ?>T23:59:00', // add T23:59:00, is to end the date on $end. Otherwise, it will end the date before $end
                                name: '<?php echo $event['name']; ?>',
                                eventType: '<?php echo $event['eventType']; ?>',
                                realStartDate: '<?php echo $event['startdate']; ?>',
                                realEndDate: '<?php echo $event['enddate']; ?>',
                                trainingCategory: '<?php echo $trainingCategory; ?>',
                                gymLocation: '<?php echo $venue; ?>',
                                facility: '<?php echo $facility; ?>',
                                rate: '<?php echo $event['rate']; ?>',
                                startT: '<?php echo $event['starttime']; ?>',
                                traineeId: '<?php echo $traineeId; ?>',
                                color: '<?php echo $color; ?>',
                        },
        <?php
    }
    ?>

<?php endforeach; ?>
                ] //END OF EVENT OBJECT       
        });
        });
    </script>
</html>
