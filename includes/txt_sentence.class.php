<?php

class TxtSentence {
   public int $id;
   public int $narrative_id;
   public int $number;
   public int $next_id; // next sentence in same narrative, 0 if none
   public int $prev_id; // prev sentence in same narrative, 0 if none
   public $graphs = array();

   public function appendGraphs() {
      global $db;
      $qry= 'SELECT inscr_id, number_inscr, '
         . 'markup, punc, sentence_id, number_sentence, graph '
         . 'FROM txt_sentences '
         . 'INNER JOIN inscr_graphs '
         . 'ON txt_sentences.id=sentence_id '
         . 'WHERE txt_sentences.id=:id '
         . 'ORDER BY number_sentence;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      while($graph = $stmt->fetchObject('InscrGraph')) {
          $this->appendGraph($graph);
      }
   }

  public function appendGraph(InscrGraph $graph) {
    $this->graphs[] = $graph;
  }
   
  public function getLength() {
    return count($this->graphs);
  }

  public function toString() {
    $sentence_string = '';
    foreach ($this->graphs as $graph) {
      $sentence_string .= $graph->toString();
    }
    return $sentence_string;
  }
}
