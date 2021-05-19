<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/inscr_graph.class.php');

$id = getFilteredId();

$sentence = new TxtSentence;
$sentence->setById($id);
$sentence->appendGraphsFromDb();
$sentence->setNextPrevId();

require_once('../includes/all_html_top.html.php');
require_once('sentence.html.php');
require_once('../includes/all_html_bottom.html.php');

?>
