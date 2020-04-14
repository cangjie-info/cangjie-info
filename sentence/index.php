<?php 

require_once('../includes/all_php.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/db.php');

$id = getFilteredId();

$qrySentence = 'SELECT * FROM txt_sentences WHERE id=:id;';
$stmtSentence = $db->prepare($qrySentence);
$stmtSentence->bindValue(':id', $id);
$stmtSentence->execute();
$sentence = $stmtSentence->fetchObject('TxtSentence');
if(!isset($sentence->id)) {
  exit('invalid id');
}

$qryGraphs = 'SELECT inscr_id, number_inscr, markup, punc, sentence_id, number_sentence, graph ' .
  'FROM txt_sentences ' .
  'INNER JOIN inscr_graphs ON txt_sentences.id=sentence_id ' .
  'WHERE txt_sentences.id=:id ' .
  'ORDER BY number_sentence;';
$stmtGraphs = $db->prepare($qryGraphs);
$stmtGraphs->bindValue(':id', $id);
$stmtGraphs->execute();
while($graph = $stmtGraphs->fetchObject('InscrGraph')) {
  $sentence->appendGraph($graph);
}
$stmtGraphs->closeCursor();

echo $sentence->toString();
echo '<a href="../narrative/?id=' . $sentence->narrative_id . '" >LINK TO NARRATIVE</a>';

?>
