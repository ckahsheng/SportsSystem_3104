<?php  
 require_once '../DBConfig.php';
 
 if(!empty($_POST))  
 {  
      $output = '';  
      $message = '';  
      $TRAINING_NAME = $_POST["TRAINING_NAME"];  
      $TRAINING_RATE = $_POST["TRAINING_RATE"];  
 
      if($_POST["ID"] != '')  
      {  
           $query = "  
           UPDATE trainingtype   
           SET TRAINING_NAME='$TRAINING_NAME',   
           TRAINING_RATE='$TRAINING_RATE'   
           WHERE ID='".$_POST["ID"]."'";  
           $message = 'Data Updated';  
      }  
      
      if(mysqli_query($link, $query))  
      {  
           $output .= '<label class="text-success">' . $message . '</label>';  
           $select_query = "SELECT * FROM trainingtype";  
           $result = mysqli_query($link, $select_query);  
           $output .= '  
                <table class="table table-bordered">  
                     <tr>  
                          <th width="40%">Training Name</th>  
                          <th width="40%">Training Rate</th>  
                          <th width="20%">View</th>
                     </tr>  
           ';  
           while($row = mysqli_fetch_array($result))  
           {  
                $output .= '  
                     <tr>  
                          <td>' . $row["TRAINING_NAME"] . '</td>
                          <td>' . $row["TRAINING_RATE"] . '</td>
                          <td><input type="button" name="edit" value="Edit" id="'.$row["ID"] .'" class="btn btn-info btn-xs edit_data" /></td> 
                     </tr>  
                ';  
           }  
           $output .= '</table>';  
      }  
      echo $output;  
 }
 
 mysqli_close($link);
 ?>