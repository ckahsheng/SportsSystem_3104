<?php
session_start();
// Include config file
ob_start();
require_once 'DBConfig.php';
$name = $_GET['trainerName'];
$sql = "SELECT * FROM users WHERE userid = '$name'";
$result = mysqli_query($link, $sql);

if (isset($_SESSION['username'])) {
    $selectQuery = mysqli_query($link, "SELECT * FROM users WHERE userid = '" . $_SESSION['username'] . "'");
    $selectResult = mysqli_fetch_array($selectQuery);
    
}

if (isset($_POST['bond'])) {
    $updateQuery = mysqli_query($link, "UPDATE users SET bondWithTrainerId ='" . $_POST['trainerId'] . "' WHERE id='" . $selectResult['id'] . "'");
    $row = mysqli_affected_rows($link);
    if ($row == 1) {
        echo '<script language="javascript">';
        echo 'alert("Bonded Sucessfully!");';
        echo '</script>';
        header("Refresh: 0");
    } else {
        echo '<script language="javascript">';
        echo 'alert("Something went wrong. Please try again later.");';
        echo '</script>';
    }
}
if (isset($_POST['endBond'])) {
    $updateQuery = mysqli_query($link, "UPDATE users SET bondWithTrainerId ='' WHERE id='" . $selectResult['id'] . "'");
    $row = mysqli_affected_rows($link);
    if ($row == 1) {
        echo '<script language="javascript">';
        echo 'alert("Bond Ended Sucessfully!");';
        echo '</script>';
        header("Refresh: 0");
    } else {
        echo '<script language="javascript">';
        echo 'alert("Something went wrong. Please try again later.");';
        echo '</script>';
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
        <link href='css/fullcalendar.css' rel='stylesheet' />
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
                                        <button type="button" class="close" data-dismiss="modal" onclick="removeCalendar()">&times;</button>
                                        <h3 class="modal-title" id="trainerNameModal"><?php echo $row['userid']; ?></h3>
                                    </center>
                                </div>
                                <p>
                                <h3><u>Description</u></h3>
                                <p>
                                    <?php echo $row['description']; ?>
                                </p>
                                <h3><u>Charge Rate</u></h3>
                                <p>
                                    <?php echo $row['chargeRate']; ?>
                                </p>
                                <form method='post'>
                                    <input type="hidden" name="trainerId" value="<?php echo $row['id']; ?>">
                                    <?php
                                    if (isset($_SESSION['role'])) {
                                        if ($_SESSION['role'] == 'Trainee' && $selectResult['bondWithTrainerId'] == "") {
                                            ?>
                                            <h3><u>Bond</u><img src="img/questionmark.png" alt="" data-toggle="tooltip" title="Once you are bonded, you are unable to join other trainer's training" style="max-height: 15px; max-width:15px; margin-top: -20px"/></h3> 
                                            <input type="submit" class="btn btn-primary" name="bond" value="Click here to bond" onclick="return confirm('Confirm to bond with <?php echo $row['userid']; ?>?')">

                                        <?php } else if ($_SESSION['role'] == 'Trainee' && $selectResult['bondWithTrainerId'] == $row['id']) { ?>
                                            <h3><u>Bond</u><img src="img/questionmark.png" alt="" data-toggle="tooltip" title="Once you are bonded, you are unable to join other trainer's training" style="max-height: 15px; max-width:15px; margin-top: -20px"/></h3> 
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
                                $sql1 = "SELECT * FROM `trainerschedule` WHERE name='$name' AND eventtype='pt'";
                                $req = $bdd->prepare($sql1);
                                $req->execute();

                                $events = $req->fetchAll();
                                ?>
                        </div>

                                                                                    </div>                                                     <!--<button type="button"  class="btn generate"onclick="" value="<?//php $row['userid'] ?>"><div id="calendar" class="monthly"></div>View Schedule</button>-->
                    <div class="row">                    
                        <div class="col-md-6" >
                            <center><p><strong>Personal Training Schedule</strong></p><br></center>
                            <div id="calendar" class="monthly"></div>
                            <p><strong>Group Training Classes</strong></p><br>
                            Insert Calendar Here 
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        ?>

        <!-- when can add PT to trainee calendar, this modal will pop out -->
        <div class="modal fade" id="ModalView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                            <input type="text" size="5" style="border-style:none;" name="starttime" id="starttime" readonly> to 
                            <input type="text" size="8.5" style="border-style:none;" name="endtime" id="endtime" readonly><br/><br/>
                            <label>Where</label><br/>
                            <input type="text"  style="border-style:none;" name="venue" id="venue" readonly><br/><br/>
                            <label>Cost</label><br/>
                            <input type="text"  style="border-style:none;" name="rate" id="rate" readonly><br/><br/>
                            
                        <!-- <form action="CalendarReqCodes/traineeJoinPT.php" method="POST"> -->
                            <input type="text" name="id" id="id" hidden>
                            <input type="text" name="traineeId" value="<?php echo $_SESSION['username'];?>" hidden>
                            <input type="text" name="trainerId" value="<?php echo $name?>" hidden>
                            <input type="submit" id="jnBtn" class="btn btn-primary" name="joinBtn" value="Join this session">         
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

        $(document).ready(function () {
            $('#calendar,#calendar1').fullCalendar({

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
                    if (event.start.isBefore(moment())) {
                        element.bind('click', function () {
                            $('#calendar').fullCalendar('unselect');
                            $('#ModalView').modal('hide');
                            alert("You are unable to view past event");
                        });
                    } else { // Show the pop up if is after today's date
                        
                        element.bind('click', function () {

                            var eventTitle = (event.title).split(" ");
                            var startTime = eventTitle[0];

                            console.log(event.venue);
                            console.log(event.start.format('YYYY-MM-DD'));
                            console.log(event.startT);
                            console.log(event.traineeId);

                            // ajax to check if gym has enough capacity for the particular PT session
                            $.ajax({
                                url: "CalendarReqCodes/sufCapacity.php",
                                type:"POST",
                                data:{// whatever data you want to "post" to the processing page, using json format
                                    'venue': event.venue,
                                    'startdate': event.start.format('YYYY-MM-DD'),
                                    'starttime': event.startT
                                },
                                async: false,
                                success: function(data){ // data = what you echo'd back, can just like do if else

                                    // alert(data);

                                    if (data.trim() == "have") { // have space

                                        console.log('have');

                                        if (event.traineeId == '<?php echo $_SESSION['username']; ?>') {

                                            console.log('delete');
                                            // TODO: to add codes for trainee to delete the session 
                                        } else if (event.traineeId == "") {

                                            console.log('no traineeId');

                                            $('#ModalView #id').val(event.id);
                                            $('#ModalView #startdate').val((event.start).format('DD MMM YYYY'));
                                            $('#ModalView #enddate').val((event.end).format('DD MMM YYYY'));
                                            $('#ModalView #title').val(eventTitle[1]);
                                            $('#ModalView #venue').val(event.venue);
                                            $('#ModalView #starttime').val(event.startT);
                                            $('#ModalView #endtime').val(event.realEndTime);
                                            $('#ModalView #rate').val(event.rate);

                                            $('#ModalView').modal('show');
                                        } else if (event.traineeId != "" && event.traineeId != '<?php echo $_SESSION['username']; ?>') {
                                            
                                            console.log('other training sesh');

                                            $('#noAccessModal').modal('show');
                                        }
                                        
                                    } else if (data.trim() == "nope") { // no space
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
                    }

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

                <?php
                foreach ($events as $event):

                    $recur = $event['recur'];
                    $name = $event['name'];
                    $end = explode(" ", $event['enddate']);
                    $titleWithTime = $event['starttime'] . ' ' . $event['title'];
                    $traineeId = $event['bookedTraineeId'];

                    $title = $event['title'];
                    $color = $event['color'];

                    // change color of the event if selected based on type of user viewing
                    if ($traineeId == NULL) { // no trainee sign up
                        $color = $color;
                    } else if ($_SESSION['username'] == $_GET['trainerName']) { // trainer view own page
                        $title = $traineeId . " " . $title;
                        $color = '#ADD8E6';
                    } else if ($_SESSION['username'] == $traineeId)  { // signed up trainee go back trainer page see
                        $color = '#ADD8E6';
                    } else if ($_SESSION['username'] != $traineeId) { // not signed up trainee go trainer page see
                        $color = '#d3d3d3';
                    }
                    
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
                            title: '<?php echo $title; ?>',
                            color: '<?php echo $color; ?>',
                            startT: '<?php echo $event['starttime']; ?>',
                            traineeId: '<?php echo $traineeId; ?>',
                            start: '<?php echo $event['startdate']; ?>',
                            end: '<?php echo $end; ?>T23:59:00', // add T23:59:00, is to end the date on $end. Otherwise, it will end the date before $end
                            venue: '<?php echo $event['venue']; ?>',
                            realEndTime: '<?php echo $event['endtime']; ?>',
                            rate: '<?php echo $event['rate']; ?>',
                        },
                    <?php 
                    } else { // if got recur
                        ?>
                        {
                            id: '<?php echo $event['trainingid']; ?>',
                            title: '<?php echo $titleWithTime; ?>',
                            start: '10:00',
                            end: '12:00',
                            color: '<?php echo $event['color']; ?>',
                            dow: '<?php echo $recur; ?>',
                            ranges: [{
                                start: '<?php echo $event['startdate']; ?>',
                                end: '<?php echo $end; ?>T23:59:00',
                            }],
                            venue: '<?php echo $event['venue']; ?>',
                            realEndTime: '<?php echo $event['endtime']; ?>',
                            rate: '<?php echo $event['rate']; ?>',
                        },
                        <?php
                    }
                    ?>

                <?php endforeach; ?>
                ] //END OF EVENT OBJECT
            });

            document.getElementById("jnBtn").onclick = function() {addTraineePT()};

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
                        $('#addedModal').modal('show');
                    },
                    error: function(data) {
                        alert("adding error");
                    }
                });
            }
        });

    </script>
</html>
