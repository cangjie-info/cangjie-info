<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

// get action from $_POST variable
$action = filter_input(INPUT_POST, 'action');
// default to "display"
if($action === NULL){
	$action = "display";
}

/* 

// DELETE ACTION
if($action == "delete"){
	// do deleting here
	header('location: .'); // action will default to "display" on reload. 
	exit;
}

// ADD ACTION
else if($action == "add"){
	trim_POST();
	// get data fields
	// validate input
	if (not valid) {
		$error_message = "Invalid excavation data.";
		include('../includes/error.php');
		exit;
	}
	else {
		$query = 'INSERT INTO etc. etc.';
		header('location: .');
		exit;
	}
}

 */

// DISPLAY ACTION
if($action == "display"){
	$id = filter_input(INPUT_GET, 'id');
	if($id === NULL){
		$error_message = 'Need an integer id for context.';
		include('../includes/error.php');
		exit;
	}
	else {
		$qry = 'SELECT * FROM arch_contexts '
			. 'WHERE id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$context = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
	}
	if(count($context) === 0){
		$error_message = 'No context with that id exists.';
		include('../includes/error.php');
		exit;
	}
	else {
		$context = $context[0];
		$qry = 'SELECT * FROM arch_objects '
			. 'WHERE arch_context_id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$objects = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$qry = 'SELECT name_en, name_zh, year '
			. 'FROM arch_excavations WHERE id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $context['arch_excavation_id']);
		$stmt->execute();
		$excavation = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		require_once('../includes/all_html_top.html.php');
		require_once('context.html.php');
		require_once('../includes/all_html_bottom.html.php');
		exit;
	}
}

?>
