<?php

require('../../credentials.php');

// adds new collection with name = $name_zh
// and returns new id
// if it does not already exist.
// if it does already exist, just return the id. 
/* function add_collection($name_zh, $db) {
	$qry_str = 'INSERT INTO txt_collections ' .
		'(

 */

try {
	$db = new PDO($dsn, $username, $password);
}
catch(PDOException $e) {
	$error_message = $e->getMessage();
	include('../includes/db_error.php');
	exit();
}
?>

