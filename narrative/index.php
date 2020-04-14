<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_narrative.class.php');

$id = getFilteredId();
  
$qryNarrative = 'SELECT * FROM txt_narratives WHERE id=:id;';
$stmtNarrative = $db->prepare($qryNarrative);
$stmtNarrative->bindValue(':id', $id);
$stmtNarrative->execute();
$narrative = $stmtNarrative->fetchObject('TxtNarrative');
if(!isset($narrative->id)) { // if no narrative corresponds to id
  exit('invalid id');
}
$stmtNarrative->closeCursor();
$qrySentences = 'SELECT txt_sentences.id, narrative_id, txt_sentences.number FROM txt_sentences ' .
  'INNER JOIN txt_narratives ON txt_sentences.narrative_id = txt_narratives.id ' .
  'WHERE txt_sentences.narrative_id=:id ' .
  'ORDER BY txt_sentences.number;';
$stmtSentences = $db->prepare($qrySentences);
$stmtSentences->bindValue(':id', $id);
$stmtSentences->execute();
while($sentence = $stmtSentences->fetchObject('TxtSentence')) {
  $qryGraphs = 'SELECT inscr_id, number_inscr, markup, punc, sentence_id, number_sentence, graph ' .
    'FROM txt_sentences ' .
    'INNER JOIN inscr_graphs ON txt_sentences.id=sentence_id ' .
    'WHERE txt_sentences.id=:id ' .
    'ORDER BY number_sentence;';
  $stmtGraphs = $db->prepare($qryGraphs);
  $stmtGraphs->bindValue(':id', $sentence->id);
  $stmtGraphs->execute();
  while($graph = $stmtGraphs->fetchObject('InscrGraph')) {
    $sentence->appendGraph($graph);
  }
  $stmtGraphs->closeCursor();
  $narrative->appendSentence($sentence);
}
$stmtSentences->closeCursor();
echo '<!DOCTYPE html>';
echo ' <head>
  <meta charset="UTF-8">
</head> ';

echo $narrative->toString();

?>
