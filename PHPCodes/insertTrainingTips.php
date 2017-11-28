<?php  
 require_once '../DBConfig.php';
 
 if(!empty($_POST))  
 {  
      $output = '';  
      $message = '';  
      $trainingTipsType = $_POST["trainingTipsType"];  
      $trainingTipsDesc = $_POST["trainingTipsDesc"];  
 
      if($_POST["trainingTipsId"] != '')  
      {  
           $query = "  
           UPDATE trainingtips   
           SET trainingTipsType='$trainingTipsType',   
           trainingTipsDesc='$trainingTipsDesc'   
           WHERE trainingTipsId='".$_POST["trainingTipsId"]."'";  
           $message = 'Data Updated';  
      } 
      
      
      if(mysqli_query($link, $query))  
      {  
           $output .= '<label class="text-success">' . $message . '</label>';  
           $select_query = "SELECT * FROM trainingtips";  
           $result = mysqli_query($link, $select_query);  
           $output .= '  
                <table class="table table-bordered">  
                     <tr>  
                          <th width="40%">Training Tips Type</th>  
                          <th width="40%">Training Tips Description</th>  
                          <th width="20%">View</th>
                     </tr>  
           ';  
           while($row = mysqli_fetch_array($result))  
           {  
                $output .= '  
                     <tr>  
                          <td>' . $row["trainingTipsType"] . '</td>
                          <td>' . $row["trainingTipsDesc"] . '</td>
                          <td><input type="button" name="edit" value="Edit" id="'.$row["trainingTipsId"] .'" class="btn btn-info btn-xs edit_data" /></td> 
                     </tr>  
                ';  
           }  
           $output .= '</table>';  
      }  
      echo $output;  
 }
 
 mysqli_close($link);
 ?>