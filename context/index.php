<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

// get action from $_POST variable
$action = filter_input(INPUT_POST, 'action');
// default to "display"
if($action == NULL){
	$action = "display";
}

// ADD ARCH_OBJECT ACTION
if($action == 'add_arch_object'){
	trim_POST();
	$context_id = filter_input(INPUT_POST, 'context_id', FILTER_VALIDATE_INT);
	$arch_object_name = filter_input(INPUT_POST, 'arch_object_name', FILTER_SANITIZE_SPECIAL_CHARS);
	$inscr_object_name = filter_input(INPUT_POST, 'inscr_object_name', FILTER_SANITIZE_SPECIAL_CHARS);
	$inscr_object_id = filter_input(INPUT_POST, 'inscr_object_id', FILTER_VALIDATE_INT);
	$inscr_object_type = filter_input(INPUT_POST, 'inscr_object_type', FILTER_SANITIZE_SPECIAL_CHARS);
	$surf_name = filter_input(INPUT_POST, 'surf_name', FILTER_SANITIZE_SPECIAL_CHARS);
	// TODO wrap this in a transaction and handle errors.
	if(isset($_POST['add_inscr'])) {
		$qry = 'INSERT INTO inscr_objects (name, object_type) VALUES (:name, :object_type);';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':name', $inscr_object_name); // name matches 
		$stmt->bindValue(':object_type', $inscr_object_type);
		$stmt->execute();
		$inscr_object_id = $db->lastInsertId();
		$qry = 'INSERT INTO inscr_surfaces (inscr_object_id, name) '
			. 'VALUES (:inscr_object_id, :name);';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':inscr_object_id', $inscr_object_id);
		$stmt->bindValue(':name', $surf_name);
		$stmt->execute();
		$inscr_surface_id = $db->lastInsertId();
		$qry = 'INSERT INTO inscrs (inscr_surface_id) '
			. 'VALUES (:inscr_surface_id);';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':inscr_surface_id', $inscr_surface_id);
		$stmt->execute();
	}
	$qry = 'INSERT INTO arch_objects (arch_context_id, name, inscr_object_id) '
			. 'VALUES (:arch_context_id, :name, :inscr_object_id);';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':arch_context_id', $context_id);
	$stmt->bindValue(':name', $arch_object_name);
	$stmt->bindValue(':inscr_object_id', $inscr_object_id);
	$stmt->execute();
	header('location: .' . "?id=$context_id");
	exit;
}

// DELETE ARCH_OBJECT ACTION
// TODO catch attempts to delete arch_object with associated inscr_object?
if($action == 'delete_arch_object'){
	$context_id = filter_input(INPUT_POST, 'context_id', FILTER_VALIDATE_INT);
	$object_id = filter_input(INPUT_POST, 'object_id', FILTER_VALIDATE_INT);
	$qry = 'DELETE FROM arch_objects WHERE id=:id';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $object_id);
	$stmt->execute();
	header('location: .' . "?id=$context_id");
	exit;
}

// EDIT ACTION
if($action == 'edit'){
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
			. 'SET name=:name, context_type=:context_type, '
			. 'description=:description, '
			. 'date_early=:date_early, date_late=:date_late '
			. 'WHERE id=:id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':context_type', $context_type);
		$stmt->bindValue(':description', $description);
		$stmt->bindValue(':date_early', $date_early);
		$stmt->bindValue(':date_late', $date_late);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		header('location: .' . "?id=$id");
		exit;
	}
}

// DISPLAY ACTION
if($action == "display"){
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
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
		// query arch_objects and inscr_objects TODO
		$qry = 'SELECT * FROM arch_objects '
			. 'WHERE arch_context_id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$arch_objects = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		// query context
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
