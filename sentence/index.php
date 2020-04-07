<?php 
require('../includes/all_php.php');
require('../includes/db.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$qrySentence = 'SELECT graph, FROM txt_sentences ' .
  'INNER JOIN inscr_graphs ON txt_sentences.id=sentence_id ' .
  'WHERE txt_sentences.id=:id ' .
  'ORDER BY number_sentence;';
  
$stmtSentence = $db->prepare($qrySentence);
$stmtSentence->bindValue(':id', $id);
$stmtSentence->execute();
$sentence = $stmtSentence->fetchAll();
$stmtSentence->closeCursor();

var_dump($sentence);
?>
