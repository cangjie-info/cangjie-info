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
// ADD ACTION
if($action == "add"){
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

// EDIT ACTION
if($action === 'edit'){
	trim_POST();
	// get data fields
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$context_type = filter_input(INPUT_POST, 'context_type', FILTER_SANITIZE_SPECIAL_CHARS);
	$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
	$date_early = filter_input(INPUT_POST, 'date_early', FILTER_VALIDATE_INT);
	$date_late = filter_input(INPUT_POST, 'date_late', FILTER_VALIDATE_INT);
	// validate
	if($name == null || $context_type == null 
		|| $date_early == null || $date_early == false
		|| $date_late == null || $date_late == false){
		$error_message = "Invalid context data";
		include('../includes/error.php');
		exit;
	}
	else {
		$qry = 'UPDATE arch_contexts ' 
			. 'SET name = :name, context_type = :context_type, '
			. 'description = :description, '
			. 'date_early = :date_early, date_late = :date_late '
			. 'WHERE id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':context_type', $context_type);
		$stmt->bindValue(':description', $description);
		$stmt->bindValue(':date_early', $date_early);
		$stmt->bindValue(':date_late', $date_late);
		$stmt->execute();
		header('location: .' . "?id=$id");
		exit();
	}
}

// DISPLAY ACTION
else if($action == "display"){
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
