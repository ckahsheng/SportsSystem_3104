
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
    </head>
    <?php
    session_start();
    include("navigation.php");
//edit to show only the specific calendar
    if (!isset($_SESSION['username'])) {
        $sql = "SELECT * FROM trainerschedule";
    } else {
        $name = $_SESSION['username'];
        $sql = "SELECT * FROM trainerschedule where name = '$name' ";
    }
    $req = $bdd->prepare($sql);
    $req->execute();

    $events = $req->fetchAll();
    ?>
    <body>
        <div class="container" style="padding-top:100px;">
            <center><h1>Calendar</h1></center>
            <div id="calendar" class="monthly">
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
                                            <input type="hidden" name="name" class="form-control" id="name" placeholder="Name" value="<?php echo $_SESSION['username'] ?>">
                                            <div class="form-group">
                                                <label for="date" class="col-sm-2 control-label">Date</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="date" class="form-control" id="date" placeholder="Date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="title" class="col-sm-2 control-label">Title</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="color" class="col-sm-2 control-label">Color</label>
                                                <div class="col-sm-10">
                                                    <select name="color" class="form-control" id="color">
                                                        <option value="">Choose</option>
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
                                            <!--Added time to edit modal !-->
                                            <!--
                                            <div class="form-group">
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
                                            <div class="form-group"> 
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <div class="checkbox">
                                                        <label class="text-danger"><input type="checkbox"  name="delete"> Delete event</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="id" class="form-control" id="id">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" name="savechanges">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <!-- /.container -->
            </div>
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
        $(document).ready(function(){
        $("input[name='recurring[]']").click(function(){
        if (jQuery('#recurr input[type=checkbox]:checked').length){
        $("#endDateRecur").show();
        } else{
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
<?php if (!isset($_SESSION['username'])) { ?>
            editable: false,
                    selectable: false,
<?php } else { ?>
            editable: true,
                    selectable: true,
<?php } ?>
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
                    $('#ModalEdit').modal('hide');
                    alert("You are unable to make changes to past event dates!");
                });
            }
            // Show the pop up if is after today's date
            else {
                element.bind('dblclick', function () {
                    $('#ModalEdit #id').val(event.id);
                    $('#ModalEdit #date').val((event.start).format('YYYY-MM-DD'));
                    $('#ModalEdit #title').val(event.title);
                    $('#ModalEdit #color').val(event.color);
                    // $('#ModalEdit #startTime').val(event.time);
                    $('#ModalEdit').modal('show');
                });
            }
            // for recurring
            if (event.ranges) {
                return (event.ranges.filter(function (range) {
                    return (event.start.isBefore(range.end) && event.end.isAfter(range.start));
                }).length) > 0;
            }
            else { // if no recurring
                return true;
            }
        },
        events: [ // START OF EVENT OBJECT
<?php
foreach ($events as $event):
    $recur = $event['recur'];
    $end = explode(" ", $event['enddate']);

    if ($end[1] == '00:00:00') {
        $end = $end[0];
    } else {
        $end = $event['enddate'];
    }
    // if no recur
    if ($recur == "") {
        ?>
                        {
                        id: '<?php echo $event['trainingid']; ?>',
                                title: '<?php echo $event['title']; ?>',
                                start: '<?php echo $event['startdate']; ?>',
                                end: '<?php echo $end; ?>T23:59:00', // add T23:59:00, is to end the date on $end. Otherwise, it will end the date before $end
                                color: '<?php echo $event['color']; ?>',
                        },
        <?php
    }
    // if got recur
    else {
        ?>
                        {
                        id: '<?php echo $event['trainingid']; ?>',
                                title: '<?php echo $event['title']; ?>',
                                start: '10:00',
                                end: '12:00',
                                color: '<?php echo $event['color']; ?>',
                                dow: '<?php echo $recur; ?>',
                                ranges: [{
                                start: '<?php echo $event['startdate']; ?>',
                                        end: '<?php echo $end; ?>T23:59:00',
                                }]
                        },
    <?php } ?>

<?php endforeach; ?>
                ] //END OF EVENT OBJECT
        });
        });
    </script>
</html>
