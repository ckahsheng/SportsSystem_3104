<?php
session_start();
// Include config file
ob_start();
require_once 'DBConfig.php';
$name = $_GET['trainerName'];
$sql = "SELECT * FROM users WHERE userid = '$name'";
$result = mysqli_query($link, $sql);


if (isset($_SESSION['username'])) {
    $selectQuery = mysqli_query($link, "SELECT bondWithTrainerId FROM users WHERE userid = '" . $_SESSION['username'] . "'");
    $selectResult = mysqli_fetch_array($selectQuery);
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
        <link href='css/fullcalendar.css' rel='stylesheet' />
                <link href='css/circle.css' rel='stylesheet'/>
        <?php include("header.html"); ?>
    </head>

    <?php include("navigation.php"); ?>

    <body>
        <div class="container" style="padding-top:70px;">
            <img class="fixed-ratio-resize" src="img/personaltrainer.jpg" alt="img/thumbnail_COVER.JPG"/>
        </div>

        <div class="container" style="padding-top:20px;">
            <center><h1>Trainer Profile</h1></center>
         
            <hr>
            <div class="row">
                <!-- edit form column -->                     
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-5" >
                        <div class="thumbnail" id="one" style="align-content:center;">

                            <img src="<?php echo 'img/trainers/' . $row['image']; ?>" class="img-rounded img-responsive" style="max-height: 200px; max-width:400px;" alt=""/>
                            <!-- Trigger the modal with a button --><center>
                                <!-- Modal -->
                                <div id="<?php echo $row['id']; ?>">

                                    <!-- Modal content-->

                                    <center>
                                     
                                        <h3 class="modal-title" id="trainerNameModal"><?php echo $row['userid']; ?></h3>
                                    </center>
                                </div>
                                <p>
                                <h3><u>Description</u></h3>
                                <p>
                                    <?php echo $row['description']; ?>
                                </p>
                              
                          
                                <form method='post' action="PHPCodes/updateBond.php">
                                    <input type="hidden" name="trainerId" value="<?php echo $row['id']; ?>">
                                    <?php
                                    if (isset($_SESSION['role'])) {
                                        if ($_SESSION['role'] == 'Trainee' && $selectResult['bondWithTrainerId'] == "") {
                                            ?>
                                            <h3><u>Bond</u><img src="img/questionmark.png" alt="" data-toggle="tooltip" title="Once you are bonded, you are unable to join other trainer's training" style="max-height: 15px; max-width:15px; margin-top: -20px"/></h3> 
                                            <input type="submit" class="btn btn-primary" name="bond" value="Click here to bond" onclick="return confirm('Note that your future training session that is not bonded with <?php echo $row['userid']; ?> will be removed. Confirm to bond with <?php echo $row['userid']; ?>?')">

                                        <?php } else if ($_SESSION['role'] == 'Trainee' && $selectResult['bondWithTrainerId'] == $row['id']) { ?>
                                            <h3><u>Bond</u><img src="img/questionmark.png" alt="" data-toggle="tooltip" title="Once you are bonded, you are unable to join other trainer's training." style="max-height: 15px; max-width:15px; margin-top: -20px"/></h3> 
                                            <input type="submit" class="btn btn-danger" name="endBond" value="Click here to end bond" onclick="return confirm('Are you sure you want to end bond with <?php echo $row['userid']; ?>?')">  
                                        <?php
                                        }
                                    }
                                    ?>
                                </form>
                                <?php
                                $name = $row['userid'];
                                //echo $name;
                                $name = trim($name);
                                // $name1=$name.strip();
                                //$sql1 = "SELECT * FROM trainerschedule where name='$name' and eventtype='pt'";
                                $sql1 = "SELECT * FROM `trainerschedule` WHERE name='$name' AND eventtype='pt' AND trainingStatus!='Cancelled'";
                               
                                $req = $bdd->prepare($sql1);
                                $req->execute();

                                $events = $req->fetchAll();                            
                                    


                                //Retrieve group training event from database 
                                $sql2 = "SELECT * FROM `grouptrainingschedule` WHERE trainerName='$name' and trainingApprovalStatus='Verified'";
                                $req1 = $bdd->prepare($sql2);
                                $req1->execute();
        
                                $events1 = $req1->fetchAll();
                                ?>
                        </div>

                    </div>
                    <!--<button type="button"  class="btn generate"onclick="" value="<?//php $row['userid'] ?>"><div id="calendar" class="monthly"></div>View Schedule</button>-->
                    
                    <!-- the title and buttons for displaying the calendar on trainer's page -->
                    <div class="row">                    
                        <div class="col-md-6" >
                            <center>
                                    <center>
                <table>                    
                    <tr>
                      
                            <td><input class="circle" style="background: #005800; border: none;" readonly></td>
                            <td style="padding-left: 5px; margin-bottom: 50px;">Available PT</td>
     
                        <td style="padding-left: 20px;"><input class="circle" style="background: #67d967; border: none;" readonly></td>
                        <td style="padding-left: 5px;">Your PT</td>
                         <td style="padding-left: 20px;"><input class="circle" style="background: #bfbfbf; border: none;" readonly></td>
                        <td style="padding-left: 5px;">Occupied PT</td>

                    </tr>
                </table>
            </center>
                                <p><strong>Personal Training Schedule</strong></p>
                                <button type="button" class="btn btn-primary" onclick="personalTraining()">View Personal Schedule</button>
                                <div id="calendar" class="monthly"></div>
                            </center>
                            
                            <center>
                                <p><strong>Group Training Classes</strong></p>
                                <button type="button" class="btn btn-primary" onclick="groupTraining()">View Group Schedule</button>
                                <div id="calendar2" class="monthly"></div>
                                </center>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        ?>

        <!-- when can add PT to trainee calendar, this modal will pop out -->
        <div class="modal fade" id="ModalView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" align="center">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">  
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <input type="text" style="border-style:none; font-size:200%; font-weight: bold;" name="title" id="title" readonly>
                    </div>
                    <div class="modal-body">
                            <label>When</label><br/>
                            <input type="text" size="8.5" style="border-style:none;" name="startdate" id="startdate" readonly> to 
                            <input type="text" size="8.5" style="border-style:none;" name="enddate" id="enddate" readonly> <br/><br/>
                            <label>Time</label><br/>
                            <input type="text" size="2" style="border-style:none;" name="starttime" id="starttime" readonly> to 
                            <input type="text" size="5" style="border-style:none;" name="endtime" id="endtime" readonly><br/><br/>
                            <label>Where</label><br/>
                            <input type="text" style="border-style:none;" name="venue" id="venue" readonly><br/><br/>
                            <label>Cost</label><br/>
                            <input type="text" size="2" style="border-style:none;" name="rate" id="rate" readonly>Per Hour<br/><br/>
                            
                        <!-- <form action="CalendarReqCodes/traineeJoinPT.php" method="POST"> -->
                            <input type="text" name="id" id="id" hidden>
                            <input type="text" name="traineeId" value="<?php echo $_SESSION['username'];?>" hidden>
                            <input type="text" name="trainerId" value="<?php echo $name?>" hidden>


                            <!-- HERE BOSS - TO REMOVE THIS LINE OF COMMENT -->
                            <!-- added code to check the trainee's bonding status -->
                            <!-- if the trainee is bonded, but the page is not the page of the trainer he is bonded to, join btn disabled -->
                            <!-- if the page is the page of the trainer trainee bonded to or there is no bonded trainer, join btn enabled -->
                            <?php 

                            if ($_SESSION['role'] == 'Trainee') {
                                $checkBondUser = $_SESSION['username'];
                                $sqlBond = "SELECT bondWithTrainerId FROM `users` WHERE userid = '$checkBondUser'";
                                $reqBond = $bdd->prepare($sqlBond);
                                $reqBond->execute();
        
                                $bondRes = $reqBond->fetchAll();

                                $bondedTrainerName = '';

                                foreach ($bondRes as $bond) {
                                    $bondedTrainerId = $bond[0];
                                }

                                $sqlCTI = "SELECT userid FROM `users` WHERE id = '$bondedTrainerId'";
                                $reqCTI = $bdd->prepare($sqlCTI);
                                $reqCTI->execute();
        
                                $CTIRes = $reqCTI->fetchAll();

                                foreach ($CTIRes as $cti) {
                                    $bondedTrainerName = $cti[0];
                                }

                                // if trainee is bonded with a trainer, and the bonded trainer's page is not this page, dont show join btn
                                if ($bondedTrainerName != "" && $bondedTrainerName != $_GET['trainerName']) { ?>
                                    <input type="submit" id="jnBtn" class="btn btn-primary" disabled name="joinBtn" value="Join this session">
                                <?php } else if ($bondedTrainerName == $_GET['trainerName'] || $bondedTrainerName == "") { ?>
                                    <input type="submit" id="jnBtn" class="btn btn-primary" name="joinBtn" value="Join this session">
                                <?php }
                            }

                            ?>

                                     
                        <!-- </form> -->
                    </div>                   
                </div>
            </div>
        </div>

        <!-- when there is no space for the trainee anymore modal -->
        <div class="modal fade" id="noSpaceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">  
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h1>No more space</h1>
                    </div>
                    <div class="modal-body">
                        <p>Unable to add training session due to lack of space. <br/> Sorry for the inconvenience caused.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>          
                </div>
            </div>
        </div>

        <!-- when successfully added trainee pt modal -->
        <div class="modal fade" id="addedModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">  
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h1>Added</h1>
                    </div>
                    <div class="modal-body">
                        <p>Successfully added!</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>          
                </div>
            </div>
        </div>

        <!-- no access to other people modal -->
        <div class="modal fade" id="noAccessModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">  
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h1>Booked by other trainees</h1>
                    </div>
                    <div class="modal-body">
                        <p>This session has been booked by other people.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>          
                </div>
            </div>
        </div>

        <!-- EDIT Modal - for the delete -->
        <!-- HERE BOSS - TO REMOVE THIS LINE OF COMMENT -->
        <!-- remove this line: removed the color label + dropdown, removed checkbox for deleting event, -->
        <!-- remove this line: edited to check if is trainer, able to edit fields, if trainee, read only -->
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
                            
                            <?php if ($_SESSION['role'] == 'Trainer') { ?>
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
                            <?php } else if ($_SESSION['role'] == 'Trainee') { ?>
                                <div class="form-group">
                                    <label for="date" class="col-sm-2 control-label">Date</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="date" class="form-control" id="date" placeholder="Date" readonly>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="title" class="col-sm-2 control-label">Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="title" class="form-control" id="title" placeholder="Title" readonly>
                                    </div>
                                </div>
                            <?php } ?>

                            <input type="hidden" name="id" class="form-control" id="id">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <!--<button type="submit" class="btn btn-primary" name="savechanges">Save changes</button>-->
                            
                            <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h6 class="modal-title" id="myModalLabel">Confirm Cancellation</h4>
                                        </div>

                                        <div class="modal-body">
                                            <p>You are about to cancel your trainning session!</p>
                                        </div>

                                        <div class="modal-footer">
                                            </form>
                                            <form action="cancelTrainingEmailScript.php" method="post"> 
                                                <button type="submit" name="id"  id="id" class="btn btn-danger" >Confirm</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            </form>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--<input type="button" id="myBtn" class="btn btn-danger" value="Cancel training"  data-toggle="modal" data-target="#confirm-delete"></a><br>-->
                            <!-- Button for cancelling training Plus modal inside-->
                            <span></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
    </body>


<?php include("calendarscripts.html"); ?>

    <script>
        trainerClicked = "";
        
        $('.trainerBtn').click(function () {
            //alert('called');
            // we want to copy the 'id' from the button to the modal
            trainerClicked = $(this).val();
            alert(trainerClicked);
        });

        //        function generateCalendar(){
        //        $('.generate').click(function () {
        //        // var trainerNameModal = document.getElementById("trainerNameModal").innerText;
        ////        var trainerNameModal = document.getElementById("myBtn").value;
        //        // var trainerNameModal = $(this).val();
        //        alert(trainerClicked);
        //        document.cookie = "name=" + trainerClicked;
        //        var x = document.cookie;
        //        alert(x);

        function personalTraining(){

            // alert("test123");
            var x = document.getElementById("calendar");
            if (x.style.display === "none") {
                x.style.display = "block";

                $(document).ready(function () {
                // alert("test");
                    $('#calendar,#calendar1').fullCalendar({

                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,basicWeek,basicDay'
                        },
                        eventLimit: true, // allow "more" link when too many events
                        
                        <?php if (!isset($_SESSION['username'])) { ?>
                        selectable: false,
                        editable: false,
                        <?php } else { ?>
                        selectable: true,
                        editable: true,
                        <?php } ?>

                        selectHelper: true,
                        displayEventTime: false, // hide the time. Eg 2a, 12p               
                        eventRender: function (event, element, view) { //START OF EVENT RENDER FUNC.
                             if (event.start.isBefore(moment().subtract(1, 'days'))) {
                                 element.bind('click', function () {
                                     $('#calendar').fullCalendar('unselect');
                                     $('#ModalView').modal('hide');
                                     alert("You are unable to view past event");
                                 });
                             } else { // Show the pop up if is after today's date
                                
                                element.bind('click', function () {

                                    var eventTitle = (event.title).split(" ");
                                    var startTime = eventTitle[0];

                                    var realStartDate = (event.realStartDate).split(" ");
                                    var realEndDate = (event.realEndDate).split(" ");

                                    console.log(event.facility);
                                    console.log(event.start.format('YYYY-MM-DD'));
                                    console.log(event.startT);
                                    console.log(event.traineeId);

                                    // ajax to check if gym has enough capacity for the particular PT session
                                    $.ajax({
                                        url: "CalendarReqCodes/sufCapacity.php",
                                        type:"POST",
                                        data:{// whatever data you want to "post" to the processing page, using json format
                                            'facility': event.facility,
                                            'startdate': event.start.format('YYYY-MM-DD'),
                                            'starttime': event.startT,
                                            'userid': '<?php echo $_SESSION['username'];?>'
                                        },
                                        async: false,
                                        success: function(data){ // data = what you echo'd back, can just like do if else

                                            // alert(data);
                                            
                                            var hold = data.trim().toString();
                                            var status = hold.split("-");

                                            if (status[0] == "have") { // have space

                                                console.log('have');
                                                
                                                

                                                if ('<?php echo $_SESSION['role'];?>' == 'Trainer'){ // if the trainer is viewing, just double click to delete
                                                    console.log('trainer delete');
                                                    // TODO: to add codes for trainee to delete the session 

                                                    $('#ModalEdit #id').val(event.id);
                                                    $('#ModalEdit #date').val((event.start).format('YYYY-MM-DD'));
                                                    $('#ModalEdit #title').val(event.title);
                                                    $('#ModalEdit #color').val(event.color);
                                                    // $('#ModalEdit #startTime').val(event.time);
                                                    $('#ModalEdit').modal('show');
                                                    //kee for cancelling training - special requirement where todays date is > 2 button will be disable //
                                                    if (event.start > (moment().add(2, 'days'))) {
                                                        document.getElementById("myBtn").disabled = false;
                                                    } else {
                                                        document.getElementById("myBtn").disabled = true;
                                                    }
                                                } else if ('<?php echo $_SESSION['role'];?>' == 'Trainee') {
                                                    if (event.traineeId == '<?php echo $_SESSION['username']; ?>') {

                                                        console.log('trainee delete');
                                                        // TODO: to add codes for trainee to delete the session 

                                                        $('#ModalEdit #id').val(event.id);
                                                        $('#ModalEdit #date').val((event.start).format('YYYY-MM-DD'));
                                                        $('#ModalEdit #title').val(event.title);
                                                        $('#ModalEdit #color').val(event.color);
                                                        // $('#ModalEdit #startTime').val(event.time);
                                                        $('#ModalEdit').modal('show');
                                                        //kee for cancelling training - special requirement where todays date is > 2 button will be disable //
                                                        if (event.start > (moment().add(2, 'days'))) {
                                                            document.getElementById("myBtn").disabled = false;
                                                        } else {
                                                            document.getElementById("myBtn").disabled = true;
                                                        }

                                                    } else if (event.traineeId == "") {

                                                        console.log('no traineeId');

                                                        if (status[1] == 'exists') {
                                                            // cannot add

                                                            alert("Please check your schedule. You have conflicting schedules");
                                                        } else if (status[1] == 'free') {
                                                            $('#ModalView #id').val(event.id);
                                                            $('#ModalView #startdate').val(moment(realStartDate[0]).format('DD MMM YYYY'));
                                                            $('#ModalView #enddate').val(moment(realEndDate[0]).format('DD MMM YYYY'));
                                                            $('#ModalView #title').val(eventTitle[1]);
                                                            $('#ModalView #venue').val(event.venue);
                                                            $('#ModalView #facility').val(event.facility);
                                                            $('#ModalView #starttime').val(event.startT);
                                                            $('#ModalView #endtime').val(event.realEndTime);
                                                            $('#ModalView #rate').val(event.rate);

                                                            $('#ModalView').modal('show');
                                                        }

                                                    } else if (event.traineeId != "" && event.traineeId != '<?php echo $_SESSION['username']; ?>') {

                                                        console.log('other training sesh');

                                                        $('#noAccessModal').modal('show');
                                                    }
                                                }
                                            } else if (status[0] == "nope") { // no space
                                                // alert("no space");

                                                $('#noSpaceModal').modal('show');
                                            }
                                        },
                                        error: function (data) {
                                            alert("got error");
                                            console.log("GOT ERROR LAH");
                                        }
                                    });
                                });
                            // }
                            }
                        }, //END OF EVENT RENDER FUNC.

                        events: [ // START OF EVENT OBJECT

                        <?php
                        foreach ($events as $event):

                            $recur = $event['recur'];
                            $name = $event['name'];
                            $end = explode(" ", $event['enddate']);
                            $endTime = date ('H:i',strtotime($event['endtime']));
                            $titleWithTime = $event['starttime'] . ' ' . $event['title'];
                            $traineeId = $event['traineeid'];
                            
                            $title = $event['title'];
                            $color = '#008000';

                            // change color of the event if selected based on type of user viewing
                            // all eventType are pt here
                            if ($traineeId == NULL) { // no trainee sign up
                                $title = $event['starttime'] . " " . $title;
                                $color = $color;
                            } else if ($_SESSION['username'] == $_GET['trainerName']) { // trainer view own page
                                $title = $traineeId . "/" . $title;
                                $color = '#67d967'; // light green
                            } else if ($_SESSION['username'] == $traineeId)  { // signed up trainee go back trainer page see
                                $color = '#67d967'; // light green
                            } else if ($_SESSION['username'] != $traineeId) { // not signed up trainee go trainer page see
                                $color = '#bfbfbf'; // grey
                            }

                                ?>
                                {
                                    id: '<?php echo $event['trainingid']; ?>',
                                    title: '<?php echo $title; ?>',
                                    color: '<?php echo $color; ?>',
                                    startT: '<?php echo $event['starttime']; ?>',
                                    traineeId: '<?php echo $traineeId; ?>',
                                    start: '<?php echo $event['startdate']; ?>',
                                    end: '<?php echo $end[0]; ?>T23:59:00', // add T23:59:00, is to end the date on $end. Otherwise, it will end the date before $end
                                    venue: '<?php echo $event['venue']; ?>',
                                    facility: '<?php echo $event['facility']; ?>',
                                    rate: '<?php echo $event['rate']; ?>',
                                    realStartDate: '<?php echo $event['startdate']; ?>',
                                    realEndDate: '<?php echo $event['enddate']; ?>',
                                    realEndTime: '<?php echo $endTime; ?>',
                                },
                            
                        <?php endforeach; ?>
                        ] //END OF EVENT OBJECT
                    });

                    // when click on the particular join button in the current/ selected modal
                    <?php if ($_SESSION['role'] == 'Trainee') { ?>
                    document.getElementById("jnBtn").onclick = function() {addTraineePT()};
                    <?php } ?>
                    
                    // ajax to add selected PT 
                    function addTraineePT() {
                        $('#ModalView').modal('hide');

                        $.ajax ({
                            url: "CalendarReqCodes/traineeJoinPT.php",
                            data: {
                                'traineeId': $('input[name=traineeId]').val(),
                                'trainerId': $('input[name=trainerId]').val(),
                                'id': $('input[name=id]').val()
                            },
                            type: "POST",
                            async: false,
                            success: function(data) {
                                // alert("added!");
                                alert("You have signed up for the session");
                                $('#addedModal').modal('show');
                            },
                            error: function(data) {
                                alert("adding error");
                            }
                        });

                        location.reload();
                    }
                });
            }
            else{
                x.style.display="none";
            }
        }

        function groupTraining(){
            // alert("test123");
            var x = document.getElementById("calendar2");
            if (x.style.display === "none") {
                x.style.display = "block";

                $(document).ready(function () {
                // alert("test");
                    $('#calendar2').fullCalendar({

                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,basicWeek,basicDay'
                        },
                        eventLimit: true, // allow "more" link when too many events
                        
                        // TODO: identify username, so other usernames cannot click
                        // TODO: got to check if bonded - if bonded && not to this trainer, then cannot select (here or L250)
                        <?php if (!isset($_SESSION['username'])) { ?>
                        selectable: false,
                        editable: false,
                        <?php } else { ?>
                        selectable: true,
                        editable: true,
                        <?php } ?>

                        selectHelper: true,
                        displayEventTime: false, // hide the time. Eg 2a, 12p               
                        eventRender: function (event, element, view) { //START OF EVENT RENDER FUNC.
                            // if (event.start.isBefore(moment())) {
                            //     element.bind('click', function () {
                            //         $('#calendar').fullCalendar('unselect');
                            //         $('#ModalView').modal('hide');
                            //         alert("You are unable to view past event");
                            //     });
                            // } else { // Show the pop up if is after today's date
                                
                                element.bind('click', function () {

                                    var eventTitle = (event.title).split(" ");
                                    var startTime = eventTitle[0];

                                    var realStartDate = (event.realStartDate).split(" ");
                                    var realEndDate = (event.realEndDate).split(" ");

                                    console.log(event.facility);
                                    console.log(event.start.format('YYYY-MM-DD'));
                                    console.log(event.startT);
                                    console.log(event.traineeId);

                                    // ajax to check if gym has enough capacity for the particular PT session
                                    $.ajax({
                                        url: "CalendarReqCodes/sufCapacity.php",
                                        type:"POST",
                                        data:{// whatever data you want to "post" to the processing page, using json format
                                            'facility': event.facility,
                                            'startdate': event.start.format('YYYY-MM-DD'),
                                            'starttime': event.startT
                                        },
                                        async: false,
                                        success: function(data){ // data = what you echo'd back, can just like do if else

                                            // alert(data);

                                            if (data.trim() == "have") { // have space

                                                console.log('have');

                                                if ('<?php echo $_SESSION['role'];?>' == 'Trainer'){ // if the trainer is viewing, just double click to delete
                                                    console.log('trainer delete');
                                                    // TODO: to add codes for trainee to delete the session 

                                                    $('#ModalEdit #id').val(event.id);
                                                    $('#ModalEdit #date').val((event.start).format('YYYY-MM-DD'));
                                                    $('#ModalEdit #title').val(event.title);
                                                    $('#ModalEdit #color').val(event.color);
                                                    // $('#ModalEdit #startTime').val(event.time);
                                                    $('#ModalEdit').modal('show');
                                                    //kee for cancelling training - special requirement where todays date is > 2 button will be disable //
                                                    if (event.start > (moment().add(2, 'days'))) {
                                                        document.getElementById("myBtn").disabled = false;
                                                    } else {
                                                        document.getElementById("myBtn").disabled = true;
                                                    }
                                                } else if ('<?php echo $_SESSION['role'];?>' == 'Trainee') {
                                                    if (event.traineeId == '<?php echo $_SESSION['username']; ?>') {

                                                        console.log('trainee delete');
                                                        // TODO: to add codes for trainee to delete the session 

                                                        $('#ModalEdit #id').val(event.id);
                                                        $('#ModalEdit #date').val((event.start).format('YYYY-MM-DD'));
                                                        $('#ModalEdit #title').val(event.title);
                                                        $('#ModalEdit #color').val(event.color);
                                                        // $('#ModalEdit #startTime').val(event.time);
                                                        $('#ModalEdit').modal('show');
                                                        //kee for cancelling training - special requirement where todays date is > 2 button will be disable //
                                                        if (event.start > (moment().add(2, 'days'))) {
                                                            document.getElementById("myBtn").disabled = false;
                                                        } else {
                                                            document.getElementById("myBtn").disabled = true;
                                                        }

                                                    } else if (event.traineeId == "") {

                                                        console.log('no traineeId');

                                                        $('#ModalView #id').val(event.id);
                                                        $('#ModalView #startdate').val(moment(realStartDate[0]).format('DD MMM YYYY'));
                                                        $('#ModalView #enddate').val(moment(realEndDate[0]).format('DD MMM YYYY'));
                                                        $('#ModalView #title').val(eventTitle[1]);
                                                        $('#ModalView #venue').val(event.venue);
                                                        $('#ModalView #facility').val(event.facility);
                                                        $('#ModalView #starttime').val(event.startT);
                                                        $('#ModalView #endtime').val(event.realEndTime);
                                                        $('#ModalView #rate').val(event.rate);

                                                        $('#ModalView').modal('show');

                                                    } else if (event.traineeId != "" && event.traineeId != '<?php echo $_SESSION['username']; ?>') {

                                                        console.log('other training sesh');

                                                        $('#noAccessModal').modal('show');
                                                    }
                                                }
                                            } else if (data.trim() == "nope") { // no space
                                                // alert("no space");

                                                $('#noSpaceModal').modal('show');
                                            }
                                        },
                                        error: function (data) {
                                            alert("Error-Please try again later");
                                            console.log("GOT ERROR LAH");
                                        }
                                    });
                                });
                            // }

                            // for recurring
                            if (event.ranges) {
                                return (event.ranges.filter(function (range) {
                                    // window.alert(range.start);
                                    return (event.start.isBefore(range.end) && event.end.isAfter(range.start));
                                }).length) > 0;
                            } else { // if no recurring
                                return true;
                            }
                        }, //END OF EVENT RENDER FUNC.

                        events: [ // START OF EVENT OBJECT

                        <?php foreach ($events1 as $event): ?>
                        {
                                id: '<?php echo $event['id']; ?>',
                                title: '<?php echo $event['trainingTitle']; ?>',
                                start: '<?php echo $event['trainingDate']; ?>',
                                time: '<?php echo $event['trainingTime']; ?>',
                                color: '<?php echo $event['trainingRate']; ?>',
                            },
                        <?php endforeach; ?>
                        ] //END OF EVENT OBJECT
                    });
                });
            }
            else{
                x.style.display="none";
            }
        }
        

    </script>
</html>
