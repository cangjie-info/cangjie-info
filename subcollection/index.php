<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_subcollection.class.php');
require_once('../includes/txt_narrative.class.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/inscr_graph.class.php');

$id = getFilteredId();
  
$subcollection = TxtSubcollection::getById($id);
$subcollection->appendNarratives();

require_once('../includes/all_html_top.html.php');
require_once('subcollection.html.php');
require_once('../includes/all_html_bottom.html.php');

?>

