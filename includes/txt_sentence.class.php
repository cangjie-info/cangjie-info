<?php

require_once('inscr_graph.class.php');

class TxtSentence {
  public $id;
  public $narrative_id;
  public $number;
  public $next_id; // next sentence in same narrative, false if none
  public $prev_id; // prev sentence in same narrative, false if none
  public $graphs = array();


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

