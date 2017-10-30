<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['trainerCalName'] = $_POST['trainerName'];
//setcookie("name", $cookieTrainerName);

if (isset($_SESSION['trainerCalName'])) {
    echo " Set";
    echo $_SESSION['trainerCalName'];
}
else{
    echo "error";
}
?>
