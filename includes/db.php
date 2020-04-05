<?php

require('../../credentials.php');

try {
	$db = new PDO($dsn, $username, $password);
}
catch(PDOException $e) {
	$error_message = $e->getMessage();
	include('db_error.php');
	exit();
}

echo "Success";

?>

