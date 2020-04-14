<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_subcollection.class.php');

$id = getFilteredId();
  
$qrySubcollection = 'SELECT * FROM txt_subcollections WHERE id=:id;';
$stmtSubcolllection = $db->prepare($qrySubcollection);
$stmtSubcolllection->bindValue(':id', $id); 
$stmtSubcolllection->execute();
$subcollection = $stmtSubcolllection->fetchObject('TxtSubcollection');
if(!isset($subcollection->id)) { // if no subcollection found for this id
  exit('invalid id');
}
$stmtSubcolllection->closeCursor();

echo $subcollection->name_zh;

$qryNarratives = 'SELECT id FROM txt_narratives ' .
  'WHERE subcollection_id=:id ' .
  'ORDER BY number;';
$stmtNarratives = $db->prepare($qryNarratives);
$stmtNarratives->bindValue(':id', $id);
$stmtNarratives->execute();
$narrative_ids = $stmtNarratives->fetchAll(PDO::FETCH_COLUMN, 0);
$stmtNarratives->closeCursor();
foreach($narrative_ids as $narrative_id) {
  $qryIncipit = 'SELECT graph, punc FROM inscr_graphs ' .
    'INNER JOIN txt_sentences ON sentence_id=txt_sentences.id ' .
    'INNER JOIN txt_narratives ON txt_narratives.id=narrative_id ' .
    'WHERE inscr_graphs.number_sentence < 8 ' .
    'AND txt_sentences.number=1 ' .
    'AND txt_narratives.id=:id ' .
    'ORDER BY txt_narratives.number, txt_sentences.number, number_sentence;';
  $stmtIncipit = $db->prepare($qryIncipit);
  $stmtIncipit->bindValue(':id', $narrative_id);
  $stmtIncipit->execute();
  $incipit = '';
  while($graph = $stmtIncipit->fetchObject('InscrGraph')) {
    $incipit .= $graph->toString();
  }
  echo "incipit = $incipit   \n";
}




?>

