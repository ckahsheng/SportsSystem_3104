<?php  
 require_once '../DBConfig.php';
 
 if(!empty($_POST))  
 {  
      $output = '';  
      $message = '';  
      $companyInfoDesc = $_POST["companyInfoDesc"]; 
 
      if($_POST["id"] != '')  
      {  
           $query = "  
           UPDATE companyinfo   
           SET companyInfoDesc='$companyInfoDesc'  
           WHERE companyInfoId='".$_POST["id"]."'";  
           $message = 'Data Updated';  
      } 
      
      
      if(mysqli_query($link, $query))  
      {  
           $output .= '<label class="text-success">' . $message . '</label>';  
           $select_query = "SELECT * FROM companyinfo";  
           $result = mysqli_query($link, $select_query);  
           $output .= '  
                <table class="table table-bordered">  
                     <tr>  
                          <th width="80%">Company Information</th>  
                          <th width="20%">View</th>
                     </tr>  
           ';  
           while($row = mysqli_fetch_array($result))  
           {  
                $output .= '  
                     <tr>  
                          <td>' . $row["companyInfoDesc"] . '</td>
                          <td><input type="button" name="edit" value="Edit" id="'.$row["companyInfoId"] .'" class="btn btn-info btn-xs edit_data" /></td> 
                     </tr>  
                ';  
           }  
           $output .= '</table>';  
      }  
      echo $output;  
 }
 
 mysqli_close($link);
 ?>