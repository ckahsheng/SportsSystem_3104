
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
    include("navigation.php");
    $trainingTitleErr = $startDateErr = $endDateErr = $venueErr = "";

    $sql = "SELECT * FROM trainerschedule ";

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
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="trainerName">Trainer Name:</label>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" id="trainerName" name ="trainerName" readonly value="<?php echo $_SESSION['username'] ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="trainingTitle">Personal Training Title:</label>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" id="trainingTitle" name="trainingTitle">
                                                <span class="text-danger"><?php echo $trainingTitleErr; ?></span> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="color" class="col-md-4 control-label">Color:</label>
                                            <div class="col-md-5">
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
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="startDate">Start Date:</label>
                                            <div class="col-md-5">          
                                                <input type="text" class="form-control" id="startDate" name="startDate" readonly>
                                              <!--<span class="text-danger"><?php echo $startDateErr; ?></span>-->
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="endDate">End Date:</label>
                                            <div class="col-md-5">          
                                                <input type="text" class="form-control" id="endDate" name="endDate">
                                                <span class="text-danger"><?php echo $endDateErr; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="venue">Venue:</label>
                                            <div class="col-md-5">          
                                                <input type="text" class="form-control" id="venue" name="venue">
                                                <span class="text-danger"><?php echo $venueErr; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



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

                                        <div class="form-group">
                                            <label for="trainingTitle" class="col-sm-2 control-label">Personal Training Title:</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="trainingTitle" class="form-control" id="trainingTitle" placeholder="Title">
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
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.container -->
        </div>
    </body>
    <?php include("footer.html"); ?>
    <?php include("calendarscripts.html"); ?>
    <!-- jQuery Version 1.11.1 -->
    <script>

        $(document).ready(function () {

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultDate: Date(),
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                selectable: true,
                selectHelper: true,
                select: function (start, end) {

                    $('#ModalAdd #startDate').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
                    $('#ModalAdd #endDate').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
                    $('#ModalAdd').modal('show');
                },
                eventRender: function (event, element) {
                    element.bind('dblclick', function () {
                        $('#ModalEdit #id').val(event.id);
                        $('#ModalEdit #trainingTitle').val(event.title);
                        $('#ModalEdit #color').val(event.color);
                        $('#ModalEdit').modal('show');
                    });
                },
                eventDrop: function (event, delta, revertFunc) { // si changement de position

                    edit(event);

                },
                eventResize: function (event, dayDelta, minuteDelta, revertFunc) { // si changement de longueur

                    edit(event);

                },
                events: [
<?php
foreach ($events as $event):

    $start = explode(" ", $event['startdate']);
    $end = explode(" ", $event['enddate']);
    if ($start[1] == '00:00:00') {
        $start = $start[0];
    } else {
        $start = $event['startdate'];
    }
    if ($end[1] == '00:00:00') {
        $end = $end[0];
    } else {
        $end = $event['enddate'];
    }
    ?>
                        {
                            id: '<?php echo $event['trainingid']; ?>',
                            title: '<?php echo $event['title']; ?>',
                            start: '<?php echo $start; ?>',
                            end: '<?php echo $end; ?>',
                            color: '<?php echo $event['color']; ?>',
                        },
<?php endforeach; ?>
                ]
            });

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
                    url: 'editEventDate.php',
                    type: "POST",
                    data: {Event: Event},
                    success: function (rep) {
                        if (rep == 'OK') {
                            alert('Saved');
                        } else {
                            alert('Could not be saved. try again.');
                        }
                    }
                });
            }

        });

    </script>

</html>
