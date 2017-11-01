<?php

include_once('../DBConfig.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $gymId= $_POST['gymId'];
    $data=array();
    $sql = "SELECT facilityName,facilityCapacity FROM trainingtype WHERE gymid=?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_gymId);
    $param_gymId = $gymId;
    
    if(mysqli_execute($stmt)){
        mysqli_stmt_store_result($stmt);
             mysqli_stmt_bind_result($stmt,$facilityName,$facilityCapacity);
              while($stmt->fetch()){
                  
                  $facilityDetail= $facilityName."- ".$facilityCapacity;
                  $data[]=$facilityDetail;
                  
              }
              echo json_encode($data);
    }
       
        else if (mysqli_stmt_affected_rows($stmt)==0){
            //Account balance is insufficnet
            echo "No Facilities Available yet ";
        }
            
    }
    mysqli_stmt_close($stmt);

mysqli_close($link);



?>




