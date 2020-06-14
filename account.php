<?php
include("functions.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$headers = getallheaders();
$token = explode(" ",$headers['Authorization'])[1];



//First of all we check if the token exists in the DB (meaning it was issued by us)
if( !isTokenValid( $token ) ) {
	header("HTTP/1.0 403 Forbidden");
	exit();
}


$decoded = JWT::decode($token, $tokenPrivateKey, array('HS256'));
$decoded = (array) $decoded;



//////
if( $requestMethod == "GET" ) {
	print(json_encode(getAccountsForUser( $decoded["userId"] )));

}

else {
	header("HTTP/1.0 405 Method Not Allowed");
}




function getLastTransactions( $account, $number ) {
	global $bdd;

	$req = $bdd->prepare('SELECT * FROM transactions WHERE account_id=? ORDER BY id DESC LIMIT ?');
	$req->bind_param("ii", $account, $number);
	$req->execute();

	$result = $req->get_result();
	$lastTransactions = $result->fetch_all(MYSQLI_ASSOC);
	return $lastTransactions;
}

function getAccountsForUser( $userId ) {
	global $bdd;

	// first we search for all accounts
	$req = $bdd->prepare('SELECT id, name FROM accounts WHERE user_id = ?');
	$req->bind_param( "i", $userId );

	$req->execute();

	$result = $req->get_result();
	$userAccounts = $result->fetch_all(MYSQLI_ASSOC);

	$return = array();

	//then for each account we are going to search for the last transaction
	foreach($userAccounts as $key=>$account) {
		$userAccounts[$key]['balance'] = getLastTransactions($account['id'], 1)[0]['accountBalance'];
	}
	return $userAccounts;
}
?>
