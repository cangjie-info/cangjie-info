<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$table = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_SPECIAL_CHARS);

if($id === null || $id === false || $table === null || $table === false) {
	$error_message = "Image data missing. Need id and table.";
	include('../includes/error.php');
	exit;
}

switch($table) {
case 'surface':
	$qry = 'SELECT path, img_rot, img_x, img_y, img_w, img_h '
		. 'FROM imgs INNER JOIN inscr_surfaces ON imgs.id=img_id '
		. 'WHERE inscr_surfaces.id=:id';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$img_data = $stmt->fetch(PDO::FETCH_ASSOC);
	if($img_data === false){
		$error_message = 'No surface image with that id.';
		include('../includes/error.php');
		exit;
	}
	$image = new Imagick('http://localhost/IMGS/' . $img_data['path'] . '.jpg');
	if($img_data['img_rot'] === null || $img_data['img_rot'] === '0') {
		// do nothing
	} 
	else {
		$image->rotateImage('white', intval($img_data['img_rot']));
	}
	if($img_data['img_x'] === null) {
		// do nothing
	}
	else {
		$w = $img_data['img_w']; // no need to cast to int
		$h = $img_data['img_h'];
		$x = $img_data['img_x'];
		$y = $img_data['img_y'];
		$image->cropImage($w, $h, $x, $y);
	}
	break;
case 'img':
	$qry = 'SELECT path FROM imgs WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':id', $id);
	$stmt->execute();
	$result = $stmt->fetchColumn();
	if($result === false){
		$error_message = 'No img with that id.';
		include('../includes/error.php');
		exit;
	}
	$image = new Imagick('http://localhost/IMGS/' . $result . '.jpg');
	break;
case 'surface':
	break;
case 'inscription':
	break;
case 'graph':
	break;
default:
	$error_message = "No such table as $table for images.";
	include('../includes/error.php');
	exit;
}

header('Content-type: image/jpeg');

// If 0 is provided as a width or height parameter,
// aspect ratio is maintained
// $image->thumbnailImage(100, 110);

echo $image;

?>
