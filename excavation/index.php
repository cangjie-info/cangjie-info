<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/zotero.php');

// DISPLAY EXCAVATION BY ID, WITH REFS AND CONTEXTS
// ADD REFS (zot_item_key, pages, note)
// ADD CONTEXT

// get action from $_POST
$action = filter_input(INPUT_POST, 'action');
// default to "display"
if($action == NULL){
	$action = "display";
}

// ADD CONTEXT ACTION
if($action === "add_context"){
	trim_POST();
	// get data fields
	// id must be from POST not GET
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$context_type = filter_input(INPUT_POST, 'context_type', FILTER_SANITIZE_SPECIAL_CHARS);
	$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
	$date_early = filter_input(INPUT_POST, 'date_early', FILTER_VALIDATE_INT);
	$date_late = filter_input(INPUT_POST, 'date_late', FILTER_VALIDATE_INT);
	// validate input
	if($id === null || $id === false || $name === null || $context_type === null
		|| $description === null || $date_early === null || $date_early === false 
		|| $date_late === null || $date_late === false) {
		$error_message = "Bad context data or fields missing.";
		include('../includes/error.php');
		exit;
	}
	else {
		$qry = 'INSERT INTO arch_contexts '
			. '(arch_excavation_id, name, context_type, description, date_early, date_late) '
			. 'VALUES (:id, :name, :context_type, :description, :date_early, :date_late);';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':context_type', $context_type);
		$stmt->bindValue(':description', $description);
		$stmt->bindValue(':date_early', $date_early);
		$stmt->bindValue(':date_late', $date_late);
		$stmt->execute();
		header('location: .' . "?id=$id");
		exit;
	}
}


// DELETE CONTEXT ACTION

//DELETE REF ACTION
if($action === 'delete_ref'){
	trim_POST();
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$ref_id = filter_input(INPUT_POST, 'ref_id', FILTER_VALIDATE_INT);
	$qry = 'DELETE FROM arch_excavation_refs WHERE id = :id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $ref_id);
	$stmt->execute();
	header('location: .' . "?id=$id");
	exit;
}

//ADD REF ACTION
if($action === 'add_ref'){
	trim_POST();
	//get data fields
	//NB id must come from POST, not GET
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$zot_item_key = filter_input(INPUT_POST, 'zot_item_key', FILTER_SANITIZE_SPECIAL_CHARS);
	$pages = filter_input(INPUT_POST,	'pages', FILTER_SANITIZE_SPECIAL_CHARS);
	$note = filter_input(INPUT_POST,	'note', FILTER_SANITIZE_SPECIAL_CHARS);
	// validate input 
	if($zot_item_key == null){
		$error_message = "Zotero item key required.";
		include('../includes/error.php');
		exit;
	}
	else {
		$qry = 'INSERT INTO arch_excavation_refs '
			. '(arch_excavation_id, zot_item_key, pages, note) '
			. 'VALUES (:id, :zot_item_key, :pages, :note);';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':zot_item_key', $zot_item_key);
		$stmt->bindValue(':pages', $pages);
		$stmt->bindValue(':note', $note);
		$stmt->execute();
		header('location: .' . "?id=$id");
		exit;
	}
}

//EDIT ACTION
if($action === 'edit'){
	trim_POST();
	//get data fields
	//NB id must come from POST, not GET
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name_zh = filter_input(INPUT_POST, 'name_zh', FILTER_SANITIZE_SPECIAL_CHARS);
	$name_en = filter_input(INPUT_POST,	'name_en', FILTER_SANITIZE_SPECIAL_CHARS);
	$year = filter_input(INPUT_POST,	'year', FILTER_VALIDATE_INT);
	$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_SPECIAL_CHARS);
	$latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
	$longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
	$excavator = filter_input(INPUT_POST, 'excavator', FILTER_SANITIZE_SPECIAL_CHARS);
	// validate input (all fields must be filled)
	if($name_zh == null || $name_en == null || $year == null || $year == false 
		|| $location == null || $latitude == null || $latitude == false 
		|| $longitude == null || $longitude == false || $excavator == null) {
		$error_message = "Invalid excavation data.";
		include('../includes/error.php');
		exit;
	}
	else {
		$qry = 'UPDATE arch_excavations SET name_zh = :name_zh, name_en = :name_en, '
			. 'year = :year, location = :location, latitude = :latitude, '
			. 'longitude = :longitude, excavator = :excavator '
			. 'WHERE id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':name_zh', $name_zh);
		$stmt->bindValue(':name_en', $name_en);
		$stmt->bindValue(':year', $year);
		$stmt->bindValue(':location', $location);
		$stmt->bindValue('latitude', $latitude);
		$stmt->bindValue(':longitude', $longitude);
		$stmt->bindValue(':excavator', $excavator);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$stmt->closeCursor();
		header('location: .' . "?id=$id");
		exit;
	}
}

// DISPLAY
if($action === 'display'){
	// get excavation id from &_GET
	$id = filter_input(INPUT_GET, 'id');
	if($id === NULL){
		$error_message = 'Need an integer id for exavation.';
		include('../includes/error.php');
		exit;
	}
	else {
		$qry = 'SELECT name_zh, name_en, year, location, '
			. 'latitude, longitude, excavator FROM arch_excavations WHERE id = :id';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$excavation = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
	}
	if(count($excavation) === 0) {
		$error_message = 'No excavation with that id exists.';
		include('../includes/error.php');
		exit;
	}
	else {
		$excavation = $excavation[0];
		// query arch_excavation_refs
		$qry = 'SELECT id, zot_item_key, pages, note '
			. 'FROM arch_excavation_refs WHERE arch_excavation_id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$refs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		// query arch_contexts
		$qry = 'SELECT * FROM arch_contexts WHERE arch_excavation_id = :id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$contexts = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		require_once('../includes/all_html_top.html.php');
		require_once('excavation.html.php');
		require_once('../includes/all_html_bottom.html.php');
		exit;
	}
}

?>
