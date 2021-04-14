<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/inscr_graph.class.php');

trim_POST();
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
if($action === NULL){
	$action = 'display';
}

// ADD INSCRIPTION TEXT
if($action === 'add_inscr_text') {
	$surf_id = filter_input(INPUT_POST, 'surf_id', FILTER_VALIDATE_INT);
	$inscr_id = filter_input(INPUT_POST, 'inscr_id', FILTER_VALIDATE_INT);
	$text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_SPECIAL_CHARS);
	echo $text;
	$chars = mb_str_split($text);
	$inscr_graphs = array();
	$current_graph = new InscrGraph;
	$previous_graph = new InscrGraph;
	$inside_braces = false;
	foreach($chars as $char){
		if($char === ' ' || $char === "\n" || $char === "\r" || $char === "\t") {
			// ignore whitespace
		}
		else if($char === '{') {
			$inside_braces = true;
		}
		else if($inside_braces === false && InscrGraph::isPostPunc($char)) {
			$previous_graph->punc |= InscrGraph::charToBit($char);
		}
		else if($inside_braces === false && InscrGraph::isPrePunc($char)) {
			$current_graph->punc|=InscrGraph::charToBit($char);
		}
		else if($char === '}') {
			$inside_braces = false;
			if($previous_graph->graph) {
				$inscr_graphs[] = $previous_graph;
			}
			$previous_graph = $current_graph;
			$current_graph = new InscrGraph;
		}
		else if($inside_braces) {
			$current_graph->graph .= $char;
		}
		else { // just a graph
			$current_graph->graph = $char;
			if($previous_graph->graph) {
				$inscr_graphs[] = $previous_graph;
			}
			$previous_graph = $current_graph;
			$current_graph = new InscrGraph;
		}
	}
	if($previous_graph->graph) {
		$inscr_graphs[] = $previous_graph;
	}
	foreach($inscr_graphs as $graph){
		echo $graph->toString();
	}
	var_dump($inscr_graphs);
	exit;
}

// EDIT ACTION
if($action === 'edit'){
	$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
	$img_rot = filter_input(INPUT_POST, 'img_rot', FILTER_VALIDATE_INT);
	$img_x = filter_input(INPUT_POST, 'img_x', FILTER_VALIDATE_INT);
	$img_y = filter_input(INPUT_POST, 'img_y', FILTER_VALIDATE_INT);
	$img_w = filter_input(INPUT_POST, 'img_w', FILTER_VALIDATE_INT);
	$img_h = filter_input(INPUT_POST, 'img_h', FILTER_VALIDATE_INT);
	$qry = 'UPDATE inscr_surfaces '
		. 'SET name=:name, img_rot=:img_rot, img_x=:img_x, img_y=:img_y, ' 
		. 'img_w=:img_w, img_h=:img_h '
		. 'WHERE id=:id;';
	$stmt = $db->prepare($qry);
	$stmt->bindValue(':name', $name);
	$stmt->bindValue(':img_rot', $img_rot);
	$stmt->bindValue(':img_x', $img_x);
	$stmt->bindValue(':img_y', $img_y);
	$stmt->bindValue(':img_w', $img_w);
	$stmt->bindValue(':img_h', $img_h);
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
