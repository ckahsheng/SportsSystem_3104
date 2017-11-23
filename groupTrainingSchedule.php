
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
    <?php
    session_start();
    include("navigation.php");
    //edit to show only the specific calendar
    $sql = "SELECT * FROM grouptrainingschedule WHERE trainingApprovalStatus='Verified'";
    $req = $bdd->prepare($sql);
    $req->execute();

    $events = $req->fetchAll();
    ?>
    <body>
        <div class="container" style="padding-top: 100px">
            <center><h1>Group Training Calendar</h1></center>

            <!-- calendar legend -->
            <center>
                <table>                    
                    <tr>
                        <!-- TODO: color diff for each type of account + not logged in users -->
               
                            <?php if ($_SESSION['role'] == 'Trainer') { ?>
                                <td><input class="circle" style="background: #6299f7; border: none;" readonly></td>
                                <td style="padding-left: 5px; margin-bottom: 50px;">Your GT</td>
                                <td style="padding-left: 20px;"><input class="circle" style="background: #adc9fb; border: none;" readonly></td>
                                <td style="padding-left: 5px;">Your GT (Full)</td>
                            <?php } ?>
                   
                        <td style="padding-left: 20px;"><input class="circle" style="background: #396376; border: none;" readonly></td>
                        <td style="padding-left: 5px;">Available GT</td>
                        <td style="padding-left: 20px;"><input class="circle" style="background: #a1c3d2; border: none;" readonly></td>
                        <td style="padding-left: 5px;">Full GT</td>
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
                                            <label class="control-label col-md-4" for="eventtype">Type: </label>
                                            <div class="col-md-7">
                      
                                                    <?php
                                                    if ($_SESSION['role'] == 'Trainer') {
                                                        ?>

                                                        <label class="radio-inline">

                                                            <input type="radio" name="eventtype" value="pt" checked>Personal Training
                                                        </label>
                                                    <?php }
                                                    ?>
                                   
                                                <label class="radio-inline">
                                                    <input type="radio" name="eventtype"  value="ot" checked>Own Training
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="trainerName">Trainer Name:</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" id="trainerName" name ="trainerName" readonly value="<?php echo $_SESSION['username'] ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="trainingTitle"><span style="color: red">*</span>Personal Training Title:</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" id="trainingTitle" name="trainingTitle" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="color" class="col-md-4 control-label">Color:</label>
                                            <div class="col-md-7">
                                                <select name="color" class="form-control" id="color">
                                                    <option value="">Choose Color</option>
                                                    <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
                                                    <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
                                                    <option style="color:#008000;" value="#008000">&#9724; Green</option>						  
                                                    <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
                                                    <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
                                                    <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
                                                    <option style="color:#000;" value="#000">&#9724; Black</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="startDate">Start Date:</label>
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
                                        <div class="form-group" id="recurr">
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
                                            <label class="control-label col-md-4" for="endDate"><span style="color: red">*</span>End Date:</label>
                                            <div class="col-md-7">          
                                                <input type="text" class="form-control" id="endDate" name="endDate" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="rate">Rate:</label>
                                            <div class="col-md-7">          
                                                <input type="text" class="form-control" id="rate" name="rate" readonly value="$50 per hour">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="venue">Venue:</label>
                                            <div class="col-md-7">          
                                                <input type="text" class="form-control" id="venue" name="venue" readonly value="Gym">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="add">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- EDIT Modal -->
                    <!--added to prevent non-login from editting--!>
                    <?php if (isset($_SESSION['username'])) { ?>
                                <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form class="form-horizontal" method="POST" action="CalendarReqCodes/editEventTitle.php">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Group Training Details</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="name" class="form-control" id="name" placeholder="Name" value="<?php echo $_SESSION['username'] ?>">
                                                    <div class="form-group">
                                                        <label for="date" class="col-sm-2 control-label">Date</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="date" class="form-control" id="date" placeholder="Date">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="time" class="col-sm-2 control-label">Time</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="time" class="form-control" id="time" placeholder="Time">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="title" class="col-sm-2 control-label"> Title</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="category" class="col-sm-2 control-label"> Category:</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="category" class="form-control" id="category" placeholder="Category">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="rate" class="col-sm-2 control-label"> Rate</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="rate" class="form-control" id="rate" placeholder="Training Rate">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="gym" class="col-sm-2 control-label"> Gym</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="gym" class="form-control" id="gym" placeholder="Gym Location">
                                                        </div>
                                                    </div>
                    
                                                        <div class="form-group">
                                                        <label for="maxCapacity" class="col-sm-2 control-label"> Max Size</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="maxCapacity" class="form-control" id="maxCapacity" placeholder="Max Capacity">
                                                        </div>
                                                    </div>
                                    
                                        
                        <!---Added time to edit modal !--->
                        <!--                                                                                        <div class="form-group">
                                                                                                                    <label for="startTime" class="col-sm-2 control-label">Start Time</label>
                                                                                                                    <div class="col-sm-10">
                                                                                                                        <select name="startTime" class="form-control" id="startTime">
                                                                                                                            <option value="">Choose</option>
                                                                                                                            <option value="13:00:00">13:00</option>
                                                                                                                            <option value="15:00:00">15:00</option>
                                                                                                                            <option value="17:00:00">17:00</option>						  
                                                                                                                            <option value="19:00:00">19:00</option>
                                                                                                                            <option value="21:00:00">21:00</option>
                                                                                                                            <option value="23:00:00">23:00</option>
                                                                    
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </div>-->

                        <input type="hidden" name="id" class="form-control" id="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <!--<button type="submit" class="btn btn-primary" name="savechanges">Save changes</button>-->

                    </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- MODALS FOR LOGGED IN USERS -->
    <?php if (isset($_SESSION['role'])) { ?>
        <!-- trainee join group training modal -->
        <?php if ($_SESSION['role'] == 'Trainee') { ?>
            <!-- if user is a trainee, show modal that can let them join the session if got space -->
            <div id="traineeJoin" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <input type="text" style="border-style:none; font-size:200%; font-weight: bold;" name="title" id="title" readonly>
                        </div>
                        <div class="modal-body">
                            <label>When</label><br/>
                            <input type="text" style="border-style:none;" size="8.5" name="date" id="date" readonly><br/><br/>
                            <label>Time</label><br/>
                            <input type="text" style="border-style:none;" size="2" name="time" id="time" readonly><br/><br/>
                            <label>Where</label><br/>
                            <input type="text" style="border-style:none;" name="gym" id="gym" readonly><br/>
                            <input type="text" style="border-style:none;" name="venue" id="venue" readonly><br/><br/>
                            <label>Cost</label><br/>
                            <input type="text" size="1" style="border-style:none;" name="rate" id="rate" readonly>Per Hour<br/><br/>

                            <input type="text" name="id" id="id" hidden>
                            <input type="text" name="grpRecurId" id="grpRecurId" hidden>
                            <input type="text" name="traineeId" value="<?php echo $_SESSION['username']; ?>" hidden>
                            <input type="text" name="trainerId" id="trainerId" hidden>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" id="jnGrpBtn" class="btn btn-primary" name="joinGTBtn" value="Join this session">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php } else if ($_SESSION['role'] == 'Trainer' || $_SESSION['role'] == 'Admin') { ?>
            <!-- else if user is a trainer/ admin, see the info of each session that they are in charge of -->

        <?php } ?>
    <?php } ?>

</div>
</div> <!-- /.container -->

<!---Displaying of error message !-->
<?php
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    echo "<script type='text/javascript'>alert('$message');</script>";
}
?>
</body>
<?php include("calendarscripts.html"); ?>

<script>
    $(document).ready(function () {
        $("input[name='recurring[]']").click(function () {
            if (jQuery('#recurr input[type=checkbox]:checked').length) {
                $("#endDateRecur").show();
            } else {
                $("#endDateRecur").hide();
            }

        });
    });
    // full calendar
    $(document).ready(function () {
    $('#calendar').fullCalendar({
    header: {
    left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
    },
            eventLimit: true, // allow "more" link when too many events

<?php

    if ($_SESSION['role'] == 'Admin') {
        ?>
            editable: true,
                    selectable: true,
    <?php } else { ?>
            editable: false,
                    selectable: false,
        <?php
    }

?>

    selectHelper: true,
            displayEventTime: false, // hide the time. Eg 2a, 12p
            // when you click the cells in the calendar
            select: function (start, end) { //START OF SELECT FUNC.
            // Hide the pop up if past date is before today's date
            if (start.isBefore(moment())) {
            $('#calendar').fullCalendar('unselect');
                    $('#ModalAdd').modal('hide');
            }
            // Show the pop up if is after today's date
            else {
            $('#ModalAdd #startDate').val(moment(start).format('YYYY-MM-DD'));
                    $('#ModalAdd').modal('show');
            }
            }, // END OF SELECT FUNC.

            eventRender: function (event, element, view) { //START OF EVENT RENDER FUNC.
            // Hide the pop up if past date is before today's date
            if (event.start.isBefore(moment())) {
            element.bind('dblclick', function () {
            $('#calendar').fullCalendar('unselect');
                    $('#traineeJoin').modal('hide');
                    alert("You are unable to make changes to past event dates!");
            });
            }
            // Show the pop up if is after today's date
            else {
            element.bind('dblclick', function () {

           <?php 
           if ( $_SESSION['role']  == 'Trainee') {?>
            // ajax to check if class full or if got conflicting events
            $.ajax({
            url: "CalendarReqCodes/gtCheck.php",
                    type:"POST",
                    data:{// whatever data you want to "post" to the processing page, using json format
                    'trainerId': event.trainerId,
                            'traineeId': '<?php echo $_SESSION['username'] ?>',
                            'id': event.id
                    },
                    async: false,
                    success: function(data){ // data = what you echo'd back, can just like do if else
                    console.log('hui lai liao');
                            console.log(data.trim());
                            gtCapStatus = data.trim();
                            if (gtCapStatus == 'full') {
                    alert('Sorry, this group training is fully booked!');
                    } else if (gtCapStatus == 'free') {
                    $('#traineeJoin #id').val(event.id);
                            $('#traineeJoin #date').val(event.date);
                            $('#traineeJoin #title').val(event.title);
                            $('#traineeJoin #rate').val(event.rate);
                            $('#traineeJoin #gym').val(event.gym);
                            $('#traineeJoin #venue').val(event.venue);
                            $('#traineeJoin #maxCapacity').val(event.maxCapacity);
                            $('#traineeJoin #time').val(event.time);
                            $('#traineeJoin #trainerId').val(event.trainerId);
                            $('#traineeJoin #grpRecurId').val(event.recurId);
                            $('#traineeJoin').modal('show');
                    } else if (gtCapStatus == 'exists') {
                    alert('You have already joined this group training session!');
                    }
                    }
            });
            <?php  } ?>

            }

            });
            }

            // for recurring
            if (event.ranges) {
            return (event.ranges.filter(function (range) {
            return (event.start.isBefore(range.end) &&
                    event.end.isAfter(range.start));
            }).length) > 0;
            } else { // if no recurring
            return true;
            }
            },
            events: [// START OF EVENT OBJECT

<?php
foreach ($events as $event):

    $color = '#396376';

    if (isset($_SESSION['role']) && $_SESSION['role'] == 'Trainer' && $_SESSION['username'] == $event['trainerName']) {
        // your training sessions
        $color = '#6299f7';

        if ($event['currentCap'] >= $event['trainingMaxCapacity']) {
            $color = '#adc9fb';
        }
    }

    // full
    else if ($event['currentCap'] >= $event['trainingMaxCapacity']) {
        $color = '#a1c3d2';
    }

    // TODO: find a way to let trainees see their signed up classes
    ?>
                {
                id: '<?php echo $event['id']; ?>',
                        title: '<?php echo $event['trainingTitle']; ?>',
                        category: '<?php echo $event['trainingCategory']; ?>',
                        rate: '<?php echo $event['trainingRate']; ?>',
                        gym: '<?php echo $event['trainingGym']; ?>',
                        maxCapacity: '<?php echo $event['trainingMaxCapacity']; ?>',
                        date: '<?php echo $event['trainingDate']; ?>',
                        time: '<?php echo $event['trainingTime']; ?>',
                        desc: '<?php echo $event['trainingDescription'] ?>',
                        venue: '<?php echo $event['trainingFacility'] ?>'.split(/[(]/g)[0], // removing the '(room size:xx)' ending
                        recurId: '<?php echo $event['GrpRecurrID']; ?>',
                        trainerId: '<?php echo $event['trainerid']; ?>',
                        color: '<?php echo $color; ?>',
                },
<?php endforeach; ?>
            ] //END OF EVENT OBJECT
    });
            // when click on the particular join button in the current/ selected modal
<?php if ($_SESSION['role'] == 'Trainee') { ?>
        document.getElementById("jnGrpBtn").onclick = function() {addTraineeGT()};
<?php } ?>

    // ajax to add selected PT 
    function addTraineeGT() {
    $('#traineeJoin').modal('hide');
            $.ajax ({
            url: "CalendarReqCodes/traineeJoinGT.php",
                    data: {
                    'traineeId': $('input[name=traineeId]').val(),
                            'trainerId': $('input[name=trainerId]').val(),
                            'id': $('input[name=id]').val(),
                            'grpRecurId': $('input[name=grpRecurId]').val()
                    },
                    type: "POST",
                    async: false,
                    success: function(data) {
                    if (data.trim() == 'inserted') {
                    alert("You have signed up for the session successfully!");
                    }
                    },
                    error: function(data) {
                    alert("Error joining this session");
                    }
            });
            location.reload();
    }
    });


</script>
</html>
