<?php  
 //fetch.php  
 require_once '../DBConfig.php';

 if(isset($_POST["ID"]))  
 {    
      $result = mysqli_query($link, "SELECT * FROM trainingtype WHERE ID = '".$_POST["ID"]."'")or die(mysqli_error($con));
      $row = mysqli_fetch_array($result);  
      echo json_encode($row);  
 }
 mysqli_close($link);
 ?>