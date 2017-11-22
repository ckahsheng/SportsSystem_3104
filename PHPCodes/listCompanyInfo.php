<?php  
 //fetch.php  
 require_once '../DBConfig.php';

 if(isset($_POST["id"]))  
 {    
     $result = mysqli_query($link, "SELECT * FROM companyinfo WHERE companyInfoId = '".$_POST["id"]."'")or die(mysqli_error($con));
     $row = mysqli_fetch_array($result); 
     echo json_encode($row);
 }
 mysqli_close($link);
 ?>