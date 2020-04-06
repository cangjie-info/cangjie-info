<?php 
require('../includes/all_php.php');
require('../includes/db.php');
require('../includes/zotero.php');

$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);

$qryAllPubs = 'SELECT * FROM pubs ORDER BY name;';
$stmtAllPubs = $db->prepare($qryAllPubs);
$stmtAllPubs->execute();
$allPubs = $stmtAllPubs->fetchAll();
$stmtAllPubs->closeCursor();

if (!$name) {
  $name = $allPubs[0]['name'];
  $page = 1;
}

//get Zotero info for that name
$qryPub = 'SELECT * FROM pubs WHERE name = :name';
$stmtPub = $db->prepare($qryPub);
$stmtPub->bindValue(':name', $name);
$stmtPub->execute();
$pub = $stmtPub->fetch();
$key = $pub['zotero'];

$bib_string = getZotBibString($key);

$page_img_number = $page - $pub['page_offset'];

// test to see if there is an image file for this page
$file_name = '../pubs/' . $name . '/' . $name . '-' . str_pad($page_img_number, 3, '0', STR_PAD_LEFT) . '.jpg';
$file_exists = false;
if (file_exists($file_name)) {
  $file_exists = true;
}

include('pubviewer.html.php');


?>
