<?php 

require_once('../includes/all_php.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/db.php');

$id = getFilteredId();

// get sentence with the given id
$qrySentence = 'SELECT * FROM txt_sentences WHERE id=:id;';
$stmtSentence = $db->prepare($qrySentence);
$stmtSentence->bindValue(':id', $id);
$stmtSentence->execute();
$sentence = $stmtSentence->fetchObject('TxtSentence');
if(!isset($sentence->id)) {
  exit('no sentence with that id');
}
$stmtSentence->closeCursor();

// get all graphs in that sentence
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

// get id for next and previous setences in narrative
// and set member vars in TxtSentence object
// 'false' if there is no next or previous
$qryNextId = 'SELECT id FROM txt_sentences ' . 
  'WHERE narrative_id=:narrative_id ' .
  'AND number=:number+1;';
$stmtNextId = $db->prepare($qryNextId);
$stmtNextId->bindValue(':narrative_id', $sentence->narrative_id);
$stmtNextId->bindValue(':number', $sentence->number);
$stmtNextId->execute();
$result = $stmtNextId->fetch();
if(!$result) {
	$sentence->next_id = FALSE;
}
else {
	$sentence->next_id = $result['id'];
}

$qryPrevId = 'SELECT id FROM txt_sentences ' . 
  'WHERE narrative_id=:narrative_id ' .
  'AND number=:number-1;';
$stmtPrevId = $db->prepare($qryPrevId);
$stmtPrevId->bindValue(':narrative_id', $sentence->narrative_id);
$stmtPrevId->bindValue(':number', $sentence->number);
$stmtPrevId->execute();
$result = $stmtPrevId->fetch();
if(!$result) {
	$sentence->prev_id = FALSE;
}
else {
	$sentence->prev_id = $result['id'];
}

// html view of sentence object
require_once('../includes/all_html_top.html.php');
require_once('sentence.html.php');
require_once('../includes/all_html_bottom.html.php');

?>
