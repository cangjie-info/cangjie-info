<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

$action = filter_input(INPUT_POST, 'action');
if($action == NULL){
	$action = "display";
}

if($action == "delete"){
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$qryDelete = 'DELETE FROM arch_excavations ' .
		'WHERE id = ' . $id . ';';
	$stmtDelete = $db->prepare($qryDelete);
	$stmtDelete->execute();
	header('Location: .?action=display');
}

else if($action=="add"){
	// trim $_POST
	function trim_value(&$value)
	{
		 $value = trim($value);
	}
	array_filter($_POST, 'trim_value');
	// get data fields
	$name_zh = filter_input(INPUT_POST, 'name_zh', FILTER_SANITIZE_SPECIAL_CHARS);
	$name_en = filter_input(INPUT_POST,	'name_en', FILTER_SANITIZE_SPECIAL_CHARS);
	$year = filter_input(INPUT_POST,	'year', FILTER_VALIDATE_INT);
	$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_SPECIAL_CHARS);
	$latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
	$longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
	$excavator = filter_input(INPUT_POST, 'excavator', FILTER_SANITIZE_SPECIAL_CHARS);
	// validate input
	if($name_zh == null || $name_en == null || $year == null || $year == false 
		|| $location == null || $latitude == null || $latitude == false 
		|| $longitude == null || $longitude == false || $excavator == null) {
		$error_message = "Invalid excavation data.";
		include('../includes/error.php');
	}
	else {
		$query = 'INSERT INTO arch_excavations (name_zh , name_en, year, location, 
				latitude, longitude, excavator)
			VALUES (:name_zh, :name_en, :year, :location, 
				:latitude, :longitude, :excavator)';
		$statement = $db->prepare($query);
		$statement->bindValue(':name_zh', $name_zh);
		$statement->bindValue(':name_en', $name_en);
		$statement->bindValue(':year', $year);
		$statement->bindValue(':location', $location);
		$statement->bindValue('latitude', $latitude);
		$statement->bindValue(':longitude', $longitude);
		$statement->bindValue(':excavator', $excavator);
		$statement->execute();
		$statement->closeCursor();
		header('Location: .?action=display');
	}
}

else if($action == "display"){
	$qryExcavations = 'SELECT * FROM arch_excavations ' .
	  'ORDER BY name_en;';
	$stmtExcavations = $db->prepare($qryExcavations);
	$stmtExcavations->execute();
	$excavations = $stmtExcavations->fetchAll(PDO::FETCH_ASSOC);
	$stmtExcavations->closeCursor();

	$json_excavations = json_encode($excavations);

	require_once('../includes/all_html_top.html.php');
	require_once('excavations.html.php');
	require_once('../includes/all_html_bottom.html.php');
}

?>
