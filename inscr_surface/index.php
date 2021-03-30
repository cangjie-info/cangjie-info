<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

trim_POST();
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
if($action === NULL){
	$action = 'display';
}

// EDIT ACTION
if($action === 'edit'){
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$img_rot = filter_input(INPUT_POST, 'img_rot', FILTER_VALIDATE_INT);
	$img_x = filter_input(INPUT_POST, 'img_x', FILTER_VALIDATE_INT);
	$qry = 'UPDATE inscr_surfaces '
		. 'SET name=:name, img_rot=:img_rot, img_x=:img_x '
		. 'WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':name', $name);
	$stmt->bindValue(':img_rot', $img_rot);
	$stmt->bindValue(':img_x', $img_x);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	header('location: .' . "?id=$id");
}

// DISPLAY ACTION
if($action === 'display'){
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if($id === null){
		$error_message = 'Need an integer id.';
		include('../includes/error.php');
		exit;
	}
	// query surface
	$qry = 'SELECT * FROM inscr_surfaces WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$surface = $stmt->fetch(PDO::FETCH_ASSOC);
	if($surface === false){
		$error_message = 'No surface with that id.';
		include('../includes/error.php');
		exit;
	}
	$rot = $surface['img_rot'];
	$x = $surface['img_x'];
	$y = $surface['img_y'];
	$h = $surface['img_h'];
	$w = $surface['img_w'];
	// query insriptions
	$qry = 'SELECT * FROM inscrs WHERE inscr_surface_id = :id';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $surface['id']);
	$stmt->execute();
	$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
	require_once('../includes/all_html_top.html.php');
	require_once('inscr_surface.html.php');
	require_once('../includes/all_html_bottom.html.php');
	exit;
}
?>
