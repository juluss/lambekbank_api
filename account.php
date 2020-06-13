<?php
include("functions.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$headers = getallheaders();

print_r($headers);
$token = explode(" ",$headers['Authorization'])[1];


//first of all we check if the token is valid;
$decoded = JWT::decode($token, "prout", array('HS256'));
$decoded = (array) $decoded;

if( time() > $decoded["exp"] ) {
	print "token expired";
	exit();
}

print_r($decoded);


//////

if( $requestMethod == "GET" ) {
	getAccountForUser( $decoded["userId"] );

}

else {
	header("HTTP/1.0 405 Method Not Allowed");
}




function getAccountForUser( $userId ) {
	global $bdd;

	print "accounts for " . $userId;

	// first we search for all accounts
	$req = $bdd->prepare('SELECT id, name FROM accounts WHERE user_id = ?');
	$req->bind_param( "i", $userId );

	$req->execute();

	$result = $req->get_result();
	$userAccounts = $result->fetch_all(MYSQLI_ASSOC);

	print_r($userAccounts);

	//then for each account we are going to search for the last transaction
	// $req = bdd->prepare('SELECT * FROM transactions WHERE account_id=? ORDER BY id DESC LIMIT 1');
	// $req->bind_param("i", $accountId);
	// $req->execute();
	//
	// $result = $req->get_result();
	// $lastTransaction = $result->fetch_all(MYSQLI_ASSOC);


}
?>
