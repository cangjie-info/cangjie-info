<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

$action = filter_input(INPUT_POST, 'action');
if($action === NULL){
	$action = 'display';
}

// EDIT ACTION
if($action == 'edit') {
	trim_POST();
	// get data fields
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$date_early = filter_input(INPUT_POST, 'date_early', FILTER_VALIDATE_INT);
	$date_late = filter_input(INPUT_POST, 'date_late', FILTER_VALIDATE_INT);
	if($name == null || $name == ''){
		$error_message = "Invalid name";
		include('../includes/error.php');
		exit;
	}
	if($date_early === false) {
		$date_early = 'NULL';
	}
	if($date_late === false) {
		$date_late = 'NULL';
	}
	$qry = 'UPDATE arch_objects '
		. 'SET name=:name, date_early=:date_early, '
		. 'date_late=:date_late '
		. 'WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':name', $name);
	$stmt->bindValue(':date_early', $date_early);
	$stmt->bindValue(':date_late', $date_late);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	header('location: .' . "?id=$id");
	exit;
}

// DISPLAY ACTION
if($action == 'display'){
        // TODO use get id function 
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if($id === NULL){
		$error_message = 'Need an integer id.';
		include('../includes/error.php');
		exit;
	}
	$qry = 'SELECT arch_excavations.name_en AS excavation_en, ' 
					. 'arch_excavations.name_zh AS excavation_zh, '
					. 'arch_excavations.year AS year, '
					. 'arch_excavations.id AS excavation_id, '
					. 'arch_contexts.name AS context_name, '
					. 'arch_contexts.id AS context_id, '
					. 'arch_objects.name AS object_name, '
					. 'arch_objects.date_early AS date_early, '
					. 'arch_objects.date_late AS date_late, '
					. 'arch_objects.inscr_object_id AS inscr_object_id '
					. 'FROM arch_objects '
					. 'INNER JOIN arch_contexts ON arch_contexts.id = arch_context_id '
					. 'INNER JOIN arch_excavations ON arch_excavations.id = arch_contexts.arch_excavation_id '
					. 'WHERE arch_objects.id = :id';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$object = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	if(count($object) !== 1){
		echo $qry;
		$error_message = 'No object with that id exists, or (unlikely) more than one does.';
		include('../includes/error.php');
		exit;
	}
	$object = $object[0];
	$qry = 'SELECT name, object_type, id '
		. 'FROM inscr_objects '
		. 'WHERE id = :id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $object['inscr_object_id']);
	$stmt->execute();
	$inscr_object = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$qry = 'SELECT * FROM inscr_surfaces WHERE inscr_object_id = :id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $inscr_object['id']);
	$stmt->execute();
	$inscr_object['inscr_surfaces'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($inscr_object['inscr_surfaces'] as $key => $surface){
		$qry = 'SELECT id, name FROM inscrs WHERE inscr_surface_id = :inscr_surface_id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':inscr_surface_id', $surface['id']);
		$stmt->execute();
		$inscr_object['inscr_surfaces'][$key]['inscrs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	require_once('../includes/all_html_top.html.php');
	require_once('arch_object.html.php');
	require_once('../includes/all_html_bottom.html.php');
	exit;
}