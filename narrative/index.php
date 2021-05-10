<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_narrative.class.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/inscr_graph.class.php');

$id = getFilteredId();
  
$narrative = TxtNarrative::getById($id);
$narrative->appendSentencesGraphs();
$narrative->setNextPrevId();

require_once('../includes/all_html_top.html.php');
require_once('narrative.html.php');
require_once('../includes/all_html_bottom.html.php');
