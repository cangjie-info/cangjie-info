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

$qryNarratives = 'SELECT id FROM txt_narratives ' .
  'WHERE subcollection_id=:id ' .
  'ORDER BY number;';
$stmtNarratives = $db->prepare($qryNarratives);
$stmtNarratives->bindValue(':id', $id);
$stmtNarratives->execute();
$narratives = $stmtNarratives->fetchAll(PDO::FETCH_CLASS, "TxtNarrative"); // only id set. Only incipits will be added as TxtSentence objects.
$stmtNarratives->closeCursor();
foreach($narratives as $narrative) {
  $incipit = new TxtSentence;
  $qryIncipit = 'SELECT graph, punc FROM inscr_graphs ' .
    'INNER JOIN txt_sentences ON sentence_id=txt_sentences.id ' .
    'INNER JOIN txt_narratives ON txt_narratives.id=narrative_id ' .
    'WHERE txt_narratives.id=:id ' .
    'ORDER BY txt_narratives.number, txt_sentences.number, number_sentence ' .
    'LIMIT 10;';
  $stmtIncipit = $db->prepare($qryIncipit);
  $stmtIncipit->bindValue(':id', $narrative->id);
  $stmtIncipit->execute();
  while($graph = $stmtIncipit->fetchObject('InscrGraph')) {
    $incipit->appendGraph($graph);
  }
  $narrative->appendSentence($incipit);
  $subcollection->appendNarrative($narrative);
}

require_once('../includes/all_html_top.html.php');
require_once('subcollection.html.php');
require_once('../includes/all_html_bottom.html.php');

?>

