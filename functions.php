<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("php_jwt.php");



$bdd = new mysqli('localhost:8889', 'lambel', 'lambel123', 'lambelbank');
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}


?>
