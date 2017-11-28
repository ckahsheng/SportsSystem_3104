<?php

//fetch.php  
require_once '../DBConfig.php';
if (isset($_POST["ID"])) {
    $result = mysqli_query($link, "select * from tbl_images WHERE id= '".$_POST["ID"]."'")or die(mysqli_error($link));
    $rows = [];
    while ($row = mysqli_fetch_array($result)) {
        $rows[] = $row['id'];
        $rows[]= $row['title'];
        $rows[]=$row['description'];
        $rows[]= base64_encode($row['name']);
    }
//    $row = mysqli_fetch_array($result);  
//    $row = mysqli_fetch_array($result);
//    echo json_encode($rows);
//      $row = mysqli_fetch_array($result);  
      echo json_encode($rows);  
//      print_r($rows);
//foreach($rows as $item) { //foreach element in $arr
//    echo '<img src="data:image/jpeg;base64,'.base64_encode( $item['name'] ).'"/>';
}

mysqli_close($link);
?>