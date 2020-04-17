<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_collection.class.php');

$id = getFilteredId();
  
$qryCollection = 'SELECT * FROM txt_collections WHERE id=:id;';
$stmtCollection = $db->prepare($qryCollection);
$stmtCollection->bindValue(':id', $id);
$stmtCollection->execute();
$collection = $stmtCollection->fetchObject('TxtCollection');
if(!isset($collection->id)) { // if no collection corresponds to that id ....
  exit('no collection for that id');
}
$stmtCollection->closeCursor();

$qrySubcollections = 'SELECT id, name_en, name_zh ' .
  'FROM txt_subcollections ' .
  'WHERE collection_id=:id ' .
  'ORDER BY number;';
$stmtSubcollections = $db->prepare($qrySubcollections);
$stmtSubcollections->bindValue(':id', $id);
$stmtSubcollections->execute();
while($subcollection = $stmtSubcollections->fetchObject('TxtSubcollection')) {
  $collection->appendSubcollection($subcollection);
}

require_once('../includes/all_html_top.html.php');
require_once('collection.html.php');
require_once('../includes/all_html_bottom.html.php');

?>

