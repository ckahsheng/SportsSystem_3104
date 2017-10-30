<?php
// Include config file
if (session_status() == PHP_SESSION_NONE) {
    session_start();


    include_once('DBConfig.php');
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
            <img class="fixed-ratio-resize" src="img/personalCoach.jpg" alt="img/thumbnail_COVER.JPG"/>
        </div>
        <div class="container" style="padding-top:20px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-primary">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs" >
                                <li class="dropdown active">
                                    <a href="#" data-toggle="dropdown">Group Training Session<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#tab1primary" data-toggle="tab">Request for Group Training Session</a></li>
                                        <li><a href="#tab2primary" data-toggle="tab">View Existing Group Training Request</a></li>
                                        <li><a href="#tab3primary" data-toggle="tab">Past Group Training Request</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <center>
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="tab1primary">
                                        <div class="container" style="padding-top:20px;padding-right:80px;padding-left:50px;">
                                            <center><h2> Request New Group Training Session </h2></center>
                                            <form  class="form-horizontal" role="form" action="" method="post">
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
                                                            mysqli_close($link);
                                                            ?>
                                                            <select class="form-control" id="trainingTypeDropDown">
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
                                                            <input type = "text" class = "form-control" onblur = "" required = "" id = "trainingRate" name = "trainingRate" disabled>
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
                                                                <input type='datepicker' class="form-control" id="trainingDate" />
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
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class = "form-group">
                                                        <label for = "trainingVenue" class = "col-sm-3 control-label">Venue:</label>
                                                        <div class = "col-sm-5">

                                                            <input type = "text" class = "form-control" required = "required" id = "trainingVenue" name = "trainingVenue" >
                                                        </div>
                                                    </div>


                                                    <div class = "form-group">
                                                        <label for = "trainingCapacity" class = "col-sm-3 control-label">Maximum Capacity:</label>
                                                        <div class = "col-sm-5">

                                                            <input type = "number" class = "form-control" required = "required" id = "trainingCapacity" name = "trainingCapacity" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type = "button" id = "requestTraining" name = "" class = "btn btn-primary" value = "Request Training Session" >


                                            </form>



                                        </div></div>
                                    <div class = "tab-pane fade" id = "tab2primary">Feature is down currently</div>
                                    <div class = "tab-pane fade" id = "tab3primary"> <div class = "container" style = "padding-left:50px; padding-right:200px;" >
                                            <h2>Add Personal Training Schedule</h2>
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
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
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



    </script>

</html>
