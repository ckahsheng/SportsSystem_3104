<?php
//action.php
if(isset($_POST["action"]))
{
 $connect = mysqli_connect("localhost", "root", "", "ict3104_sports");
 if($_POST["action"] == "fetch")
 {
  $query = "SELECT * FROM tbl_images ORDER BY id DESC";
  $result = mysqli_query($connect, $query);
  $output = '
   <table class="table table-bordered table-striped">  
    <tr>
     <th width="5%">Promotion ID</th>
     <th width="5%">title</th>
      <th width="20%">description</th>
     <th width="50%">Image</th>
     <th width="10%">Change</th>
     <th width="10%">Remove</th>
    </tr>
  ';
  while($row = mysqli_fetch_array($result))
  {
   $output .= '

    <tr>
     <td>'.$row["id"].'</td>
           <td>'.$row["title"].'</td>
                 <td>'.$row["description"].'</td>
     <td>
      <img src="data:image/jpeg;base64,'.base64_encode($row['name'] ).'" height="60" width="75" class="img-thumbnail" />
     </td>
      <input type="hidden" name="title" value="'.$row["title"].'">
          <input type="hidden" name="description" value="'.$row["description"].'">
     <td><button type="button" name="update" class="btn btn-warning bt-xs update" id="'.$row["id"].'">Change</button></td>
     <td><button type="button" name="delete" class="btn btn-danger bt-xs delete" id="'.$row["id"].'">Remove</button></td>
    </tr>
   ';
  }
  $output .= '</table>';
  echo $output;
 }

 if($_POST["action"] == "insert")
 {
      $description = $_POST["addpromotiondescription"];  
        $title = $_POST["addpromotiontitle"];  
  $file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
  $query = "INSERT INTO tbl_images ( name, title,description ) VALUES
('$file', '$title' ,'$description')";
  
  if(mysqli_query($connect, $query))
  {
   echo 'Image Inserted into Database';
  }
 }
 if($_POST["action"] == "update")
 {
     $description = $_POST["addpromotiondescription"];  
        $title = $_POST["addpromotiontitle"];  
  $file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
  
  $query = "UPDATE tbl_images SET name = '$file',title = '$title',description = '$description' WHERE id = '".$_POST["image_id"]."'";
  if(mysqli_query($connect, $query))
  {
   echo 'Image Updated into Database';
  }
 }
 if($_POST["action"] == "delete")
 {
  $query = "DELETE FROM tbl_images WHERE id = '".$_POST["image_id"]."'";
  if(mysqli_query($connect, $query))
  {
   echo 'Image Deleted from Database';
  }
 }
}
?>