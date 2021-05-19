<?php

class TxtSentence {
   public int $id = 0;
   public int $narrative_id = 0;
   public int $number = 0; // number in narrative
   public int $next_id = 0; // next sentence in same narrative, 0 if none
   public int $prev_id = 0; // prev sentence in same narrative, 0 if none
   public $graphs = array();

   public function setById(int $id) {
      global $db;
      $qry= 'SELECT * FROM txt_sentences WHERE id=:id;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':id', $id);
      $stmt->setFetchMode(PDO::FETCH_INTO, $this);
      $stmt->execute();
      $stmt->fetch();
   }

/* public function __construct() {
      if($id) setNextPrevId(); // constructor called after PDO fetch initializes $id.
   } */

   public function getPrev() {
      $sentence = new TxtSentence;
      if($this->prev_id > 0) {
         $sentence->setById($this->prev_id);
      }
      return $sentence;
   }

   public function getNext() {
      $sentence = new TxtSentence;
      if($this->next_id) {
         $sentence->setById($this->next_id);
      }
      return $sentence;
   }

   public function insert() {
      // inserts TxtSentence into db txt_sentences table, and
      // inserts all its InscrGraphs into the inscr_graphs table.
      // the latter is a single insert, and so no usable insert id can be retrieved.
      // TODO protect against injeciton, and accidental use of quotes in graph string.
      // TODO separate graph insertion
      // TODO creat mechansm to insert all graphs (or sentences, etc.) from an entire collection in a single INSERT
      global $db;
      $qry = 'INSERT INTO txt_sentences (narrative_id, number) '
         . 'VALUES (:narrative_id, :number);';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':narrative_id', $this->narrative_id);
      $stmt->bindValue(':number', $this->number);
      $stmt->execute();
      $this->id = $db->lastInsertId();
      $qry = 'INSERT INTO inscr_graphs (inscr_id, number_inscr, markup, punc, sentence_id, '
         . 'number_sentence, graph) '
         . 'VALUES ';
      $first = true;
      foreach($this->graphs as $graph){
         if(!$first){
            $qry .= ', ';
         }
         $qry .= '( NULL, '
            . 'NULL, '
            . $graph->markup . ', '
            . $graph->punc . ', '
            . $this->id . ', ' // TODO hacky
            . $graph->number_sentence . ', '
            . '"' . $graph->graph . '")';
         $first = false;
      }
      $qry .= ';';
      $stmt = $db->prepare($qry);
      $stmt->execute();
   }

   public function appendGraphsFromDb() {
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
      return $this;
   }

   public function appendGraph(InscrGraph $graph) {
      $graph->sentence_id = $this->id;
      $graph->number_sentence = count($this->graphs) + 1;
      $this->graphs[] = $graph;
   }

   public function setNextPrevId() {
      global $db;
      for($offset = -1; $offset <= 1; $offset += 2){
         $qry= 'SELECT id FROM txt_sentences ' 
            . 'WHERE narrative_id=:narrative_id '
            . 'AND number=:number;';
         $stmt= $db->prepare($qry);
         $stmt->bindValue(':narrative_id', $this->narrative_id);
         $stmt->bindValue(':number', $this->number + $offset);
         $stmt->execute();
         $result = $stmt->fetch();
         if(!$result) {
            if($offset === 1) $this->next_id = 0;
            else $this->prev_id = 0;
         }
         else {
            if($offset === 1) $this->next_id = $result['id'];
            else $this->prev_id = $result['id'];
         }
      }
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
