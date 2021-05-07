<?php

require('../../credentials.php');

try {
	$db = new PDO($dsn, $username, $password);
}
catch(PDOException $e) {
	$error_message = $e->getMessage();
	include('../includes/error.php');
	exit();
}
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
?>
