<?php
// Include config file
ob_start();
require_once 'DBConfig.php';

$sql = "SELECT * FROM users WHERE role = 'Trainer'";
$result = mysqli_query($link, $sql);
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
            <center><h1>Trainer List</h1></center>
            <hr>
            <div class="row">
                <!-- edit form column -->                     

                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-4">
                        <div class="thumbnail" id="one" style="align-content:center;">

                            <img src="<?php echo 'img/trainers/' . $row['image']; ?>" class="img-rounded img-responsive" style="max-height: 200px; max-width:400px;" alt=""/>
                            <br>  <h3><center><?php echo $row['userid']; ?></center></h3>

                            <!-- Trigger the modal with a button --><center>
                                <button type="button" id="myBtn"  class="btn btn-primary trainerBtn" name="test" value="<?php echo $row['userid']; ?>" data-toggle="modal" data-target="#<?php echo $row['id']; ?>">View Trainer Profile</button></center>

                            <!-- Modal -->

                            <div id="<?php echo $row['id']; ?>" class="modal fade" role="dialog" >
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content" id="p1" >
                                        <div class="modal-header">
                                            <center>
                                                <button type="button" class="close" data-dismiss="modal" onclick="removeCalendar()">&times;</button>
                                                <h3 class="modal-title" id="trainerNameModal"><?php echo $row['userid']; ?></h3>
                                            </center>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <h3><u>Description</u></h3>
                                                <p>
                                                    <?php echo $row['description']; ?>
                                                </p>
                                                <h3><u>Charge Rate</u></h3>
                                                <p>
                                                    <?php echo $row['chargeRate']; ?>
                                                </p>
                                                <p><strong>Personal Training Schedule</strong><br>
                                                    <?php
                                                    $name = $row['userid'];
                                                    echo $name;
                                                    $name = trim($name);
                                                    // $name1=$name.strip();
                                                    //$sql1 = "SELECT * FROM trainerschedule where name='$name' and eventtype='pt'";
                                                    $sql1 = "SELECT * FROM `trainerschedule` WHERE eventtype='pt'";
                                                    $req = $bdd->prepare($sql1);
                                                    $req->execute();

                                                    $events = $req->fetchAll();
                                                    ?>

                                                    <button type="button"  class="btn generate"onclick="" value="<?php $row['userid'] ?>"><div id="calendar" class="monthly"></div>View Schedule</button>
                                                <p><strong>Group Training Classes</strong><br>
                                                    Insert Calendar Here 
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn white" data-dismiss="modal" onclick="removeCalendar()">Close</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                ?>

            </div>
        </div>
        <hr>
    </body>
    <?php include("footer.html"); ?>

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
        $('.generate').click(function () {
        // var trainerNameModal = document.getElementById("trainerNameModal").innerText;
//        var trainerNameModal = document.getElementById("myBtn").value;
        // var trainerNameModal = $(this).val();
        alert(trainerClicked);
        document.cookie = "name=" + trainerClicked;
        var x = document.cookie;
        alert(x);

        $(document).ready(function () {
        $('#calendar,#calendar1').fullCalendar({

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
                $('#calendar').fullCalendar('unselect');
                $('#ModalEdit').modal('hide');
                }
                // Show the pop up if is after today's date
                else {
                element.bind('dblclick', function () {
                $('#ModalEdit #id').val(event.id);
                $('#ModalEdit #date').val((event.start).format('YYYY-MM-DD'));
                $('#ModalEdit #title').val(event.title);
                $('#ModalEdit #color').val(event.color);
                $('#ModalEdit').modal('show');
                });
                }
                // for recurring
                if (event.ranges) {
                return (event.ranges.filter(function (range) {
                // window.alert(range.start);
                return (event.start.isBefore(range.end) &&
                        event.end.isAfter(range.start));
                }).length) > 0;
                }
                else { // if no recurring
                return true;
                }
                }, //END OF EVENT RENDER FUNC.
                eventDrop: function (event, delta, revertFunc) { // si changement de position
                edit(event);
                },
                eventResize: function (event, dayDelta, minuteDelta, revertFunc) { // si changement de longueur
                edit(event);
                },
                events: [ // START OF EVENT OBJECT


<?php
foreach ($events as $event):

    $recur = $event['recur'];
    $name = $event['name'];
    $end = explode(" ", $event['enddate']);
    $cookieName = $_COOKIE['name'];

    if (strcasecmp($name, $cookieName) == 0) {

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
                                    title: '<?php echo $event['starttime'] . $event['title']; ?>',
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
            <?php
        }
    }
    ?>

<?php endforeach; ?>
                ] //END OF EVENT OBJECT

        });
        // Drag and drop event
        function edit(event) {
        start = event.start.format('YYYY-MM-DD HH:mm:ss');
        if (event.end) {
        end = event.end.format('YYYY-MM-DD HH:mm:ss');
        } else {
        end = start;
        }

        id = event.id;
        Event = [];
        Event[0] = id;
        Event[1] = start;
        Event[2] = end;
        $.ajax({
        url: 'CalendarReqCodes/editEventDate.php',
                type: "POST",
                data: {Event: Event},
                success: function (rep) {
                if (rep == 'OK') {
                alert('Updated training date');
                } else {
                alert('Could not be updated. try again.');
                }
                }
        });
        } // END OF FUNCTION EDIT

        });
        }
        )



                function removeCalendar() {
                $('#calendar,#calendar1').fullCalendar('removeEvents');
                $('#calendar,#calendar1').fullCalendar('rerenderEvents');
                $('#calendar,#calendar1').fullCalendar('destroy');
                        window.location.href="trainerList.php";
                }


//         $('#calendar3,#calendar4').fullCalendar('removeEvents');
//        $('#calendar3,#calendar4').fullCalendar('rerenderEvents');
//        $('#calendar3,#calendar4').fullCalendar('destroy');

    </script>
</html>
