<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
if($action === NULL){
	$action = 'display';
}

if($action === 'edit') {
	trim_POST();
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
	if($id === null || $id === false || $name === null || $name === ''){
		$error_message = "Invalid input.";
		include('../includes/error.php');
		exit;
	}
	$qry = 'UPDATE inscr_objects '
		. 'SET name=:name, object_type=:type '
		. 'WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':name', $name);
	$stmt->bindValue(':type', $type);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	header('location: .' . "?id=$id");
	exit();
}

else if($action === 'add_surf') {
	trim_POST();
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	if($name === null || $name === false){
		$error_message = "Name needed for every surface.";
		include('../inlcudes/error.php');
		exit();
	}
	$qry = 'INSERT INTO inscr_surfaces (inscr_object_id, name) '
		. ' VALUES (:id, :name);';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->bindValue(':name', $name);
	$stmt->execute();
	$surf_id = $db->lastInsertId();
	$qry = 'INSERT INTO inscrs (inscr_surface_id, name) '
		. 'VALUES (:id, :name)';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $surf_id);
	$stmt->bindValue(':name', '');
	$stmt->execute();
	header('location: .' . "?id=$id");
	exit;
}

// DISPLAY ACTION
else if($action === 'display'){
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if($id === NULL){
		$error_message = 'Need an integer id.';
		include('../includes/error.php');
		exit;
	}
	// query inscribed object 
	$qry = 'SELECT id, name, object_type AS type '
		. 'FROM inscr_objects '
		. 'WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$object = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	if($object === false){
		$error_message = 'No object with that id exists.';
		include('../includes/error.php');
		exit;
	}
	// query archeological objects
	$qry = 'SELECT id, name '
		. 'FROM arch_objects '
		. 'WHERE inscr_object_id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$arch_objects = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	// query surfaces
	$qry = 'SELECT id, name '
		. 'FROM inscr_surfaces '
		. 'WHERE inscr_object_id = :id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$surfaces = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// query inscriptions
	foreach($surfaces as $key => $surface){
		$qry = 'SELECT id, name '
			. 'FROM inscrs '
			. 'WHERE inscr_surface_id = :surf_id';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':surf_id', $surface['id']);
		$stmt->execute();
		$surfaces[$key]['inscriptions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
	}
		


/*

	$qry = 'SELECT arch_excavations.name_en AS excavation_en, ' 
					. 'arch_excavations.name_zh AS excavation_zh, '
					. 'arch_excavations.year AS year, '
					. 'arch_excavations.id AS excavation_id, '
					. 'arch_contexts.name AS context_name, '
					. 'arch_contexts.id AS context_id, '
					. 'arch_objects.name AS object_name, '
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
 */
	require_once('../includes/all_html_top.html.php');
	require_once('inscr_object.html.php');
	require_once('../includes/all_html_bottom.html.php');
	exit;
}
?>
