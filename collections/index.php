<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_collection.class.php');

$qryCollections = 'SELECT * FROM txt_collections ' .
  'ORDER BY name_en;';
$stmtCollections = $db->prepare($qryCollections);
$stmtCollections->execute();
$collections = $stmtCollections->fetchAll(PDO::FETCH_CLASS, 'TxtCollection');
$stmtCollections->closeCursor();

require_once('../includes/all_html_top.html.php');
require_once('collections.html.php');
require_once('../includes/all_html_bottom.html.php');

?>
