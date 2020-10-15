<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

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

?>
