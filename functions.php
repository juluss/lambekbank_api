<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("php_jwt.php");

$tokenPrivateKey = "lkjbjkhgjhg#asdrwer6535tzsdt";



$bdd = new mysqli('localhost:8889', 'lambel', 'lambel123', 'lambelbank');
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

function isTokenValid( $token ) {
	global $bdd;
	global $tokenPrivateKey;

	$req = $bdd->prepare('SELECT token FROM tokens WHERE token = ?');
	$req->bind_param( "s", $token );

	$req->execute();

	$result = $req->get_result();
	$tokens = $result->fetch_all(MYSQLI_ASSOC);

	if( !empty($tokens) ) {

		$decoded = JWT::decode($token, $tokenPrivateKey, array('HS256'));
		$decoded = (array) $decoded;
		if( time() > $decoded["exp"] ) {
			return 0;
		}
		else {
			return 1;
		}
	}
	else {
		return 0;
	}
}
?>
