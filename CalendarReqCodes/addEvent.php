<?php

require_once('../DBConfig.php');
//echo $_POST['title'];

if (isset($_POST['trainerName']) && isset($_POST['trainingTitle']) && isset($_POST['startDate']) && isset($_POST['endDate']) && isset($_POST['color']) && isset($_POST['venue'])){
	
        $name = $_POST['trainerName'];
	$title = $_POST['trainingTitle'];
	$start = $_POST['startDate'];
	$end = $_POST['endDate'];
	$color = $_POST['color'];
        $venue = $_POST['venue'];

	$sql = "INSERT INTO trainerschedule(name, title, startdate, enddate, color, venue) values ('$name', '$title', '$start', '$end', '$color', '$venue')";
	//$req = $bdd->prepare($sql);
	//$req->execute();
	
	echo $sql;
	
	$query = $bdd->prepare( $sql );
	if ($query == false) {
	 print_r($bdd->errorInfo());
	 die ('error preparing');
	}
	$sth = $query->execute();
	if ($sth == false) {
	 print_r($query->errorInfo());
	 die ('error execute');
	}

}
header('Location: '.$_SERVER['HTTP_REFERER']);

	
?>
