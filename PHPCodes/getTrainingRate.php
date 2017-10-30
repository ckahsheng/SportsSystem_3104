<?php

include_once('../DBConfig.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $trainingId= $_POST['trainingId'];

    $sql = "SELECT TRAINING_RATE FROM trainingtype WHERE ID=?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_trainingId);
    $param_trainingId = $trainingId;
    
    if(mysqli_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) ==1 ){
             mysqli_stmt_bind_result($stmt,$rate);
              while($stmt->fetch()){
                  
                  echo "$".$rate;
                  
              }
            //Means the account balance is sufficient
            
            //This is added to save user input for verification before processing 
         
        } 
        else if (mysqli_stmt_affected_rows($stmt)==0){
            //Account balance is insufficnet
            echo "No such Training";
        }
            
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);



?>




