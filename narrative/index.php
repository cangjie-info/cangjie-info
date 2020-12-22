<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_narrative.class.php');

$id = getFilteredId();
  
// get narrative based on id
$qryNarrative = 'SELECT * FROM txt_narratives WHERE id=:id;';
$stmtNarrative = $db->prepare($qryNarrative);
$stmtNarrative->bindValue(':id', $id);
$stmtNarrative->execute();
$narrative = $stmtNarrative->fetchObject('TxtNarrative');
if(!isset($narrative->id)) { // if no narrative corresponds to id
  exit('no narrative with that id');
}
$stmtNarrative->closeCursor();

// get all sentences in narrative
$qrySentences = 'SELECT txt_sentences.id, narrative_id, txt_sentences.number FROM txt_sentences ' .
  'INNER JOIN txt_narratives ON txt_sentences.narrative_id = txt_narratives.id ' .
  'WHERE txt_sentences.narrative_id=:id ' .
  'ORDER BY txt_sentences.number;';
$stmtSentences = $db->prepare($qrySentences);
$stmtSentences->bindValue(':id', $id);
$stmtSentences->execute();
while($sentence = $stmtSentences->fetchObject('TxtSentence')) {
  //get all graphs for each sentence
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

// get id for next and previous narratives in subcollection
// and set member vars in TxtNarrative object
// 'false' if there is no next or previous
$qryNextId = 'SELECT id FROM txt_narratives ' . 
  'WHERE subcollection_id=:subcollection_id ' .
  'AND number=:number+1;';
$stmtNextId = $db->prepare($qryNextId);
$stmtNextId->bindValue(':subcollection_id', $narrative->subcollection_id);
$stmtNextId->bindValue(':number', $narrative->number);
$stmtNextId->execute();
$result = $stmtNextId->fetch();
if(!$result) {
	$narrative->next_id = FALSE;
}
else {
	$narrative->next_id = $result['id'];
}

$qryPrevId = 'SELECT id FROM txt_narratives ' . 
  'WHERE subcollection_id=:subcollection_id ' .
  'AND number=:number-1;';
$stmtPrevId = $db->prepare($qryPrevId);
$stmtPrevId->bindValue(':subcollection_id', $narrative->subcollection_id);
$stmtPrevId->bindValue(':number', $narrative->number);
$stmtPrevId->execute();
$result = $stmtPrevId->fetch();
if(!$result) {
	$narrative->prev_id = FALSE;
}
else {
	$narrative->prev_id = $result['id'];
}

// html view of TxtNarrative object
require_once('../includes/all_html_top.html.php');
require_once('narrative.html.php');
require_once('../includes/all_html_bottom.html.php');


?>
