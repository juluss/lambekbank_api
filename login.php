<?php
include("functions.php");



$requestMethod = $_SERVER["REQUEST_METHOD"];



if( !empty($_SERVER['PHP_AUTH_USER']) AND !empty($_SERVER['PHP_AUTH_PW']) ) {
	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];
}
else {
	header("HTTP/1.0 400 Bad Request");
	exit();
}


if( $requestMethod == "POST" ) {

	print(json_encode(login($username, $password)));

}

else {
	header("HTTP/1.0 405 Method Not Allowed");
}



function login($username=null, $password=null) {

	global $tokenPrivateKey;
	global $bdd;

	//get infos in DATABASE
	$req = $bdd->prepare('SELECT id, username, password FROM users WHERE username = ?');
	$req->bind_param( "s", $username );
	$req->execute();

	$result = $req->get_result();
	$userFromDB = $result->fetch_all(MYSQLI_ASSOC);
	//need to add close for the request here

	$return = array();

	if( !empty($userFromDB[0]) ) {

		$userFromDB = $userFromDB[0];

		if( password_verify( $password, $userFromDB['password']) ) {

			// preparing TOKEN
			$payload = array(
				"userId" => $userFromDB['id'],
				"exp" => time() + 3600,
			);
			$jwt = JWT::encode($payload, $tokenPrivateKey);

			// pushing the token in DATABASE
			$req = $bdd->prepare('INSERT into tokens VALUES (NULL, ?)');
			$req->bind_param( "s", $jwt );
			$req->execute();
			if( $req->sqlstate != "00000" ) {
				exit();
			}

			$return["success"] = 1;
			$return["username"] = $username;
			$return['token'] = $jwt;

		}
		else {
			$return["success"] = 0;
		}
	}

	else {
		$return["success"] = 0;
	}

	return $return;
}

?>
