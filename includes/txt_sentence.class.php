<?php

class TxtSentence {
   public int $id = 0;
   public int $narrative_id = 0;
   public int $number = 0;
   public int $next_id = 0; // next sentence in same narrative, 0 if none
   public int $prev_id = 0; // prev sentence in same narrative, 0 if none
   public $graphs = array();

   public static function getById(int $id) {
      global $db;
      $qry= 'SELECT * FROM txt_sentences WHERE id=:id;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':id', $id);
      $stmt->execute();
      $sentence = $stmt->fetchObject('TxtSentence');
      if(!isset($sentence->id)) {
        exit('no sentence with that id');
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

   public function appendGraphs() { //shit name -> appendGraphsFromDb
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
      $graph->sentence_id = $this->id;
      $this->graphs[] = $graph;
   }
   
   public function setNextPrevId() {
      $this->setNextId();
      $this->setPrevId();
   }

   public function setNextId() {
      global $db;
      $qry= 'SELECT id FROM txt_sentences ' 
         . 'WHERE narrative_id=:narrative_id '
         . 'AND number=:number+1;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':narrative_id', $this->narrative_id);
      $stmt->bindValue(':number', $this->number);
      $stmt->execute();
      $result = $stmt->fetch();
      if(!$result) {
         $this->next_id = 0;
      }
      else {
         $this->next_id = $result['id'];
      }
   }

   public function setPrevId() {
      global $db;
      $qry= 'SELECT id FROM txt_sentences ' 
         . 'WHERE narrative_id=:narrative_id '
         . 'AND number=:number-1;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':narrative_id', $this->narrative_id);
      $stmt->bindValue(':number', $this->number);
      $stmt->execute();
      $result = $stmt->fetch();
      if(!$result) {
         $this->prev_id = 0;
      }
      else {
         $this->prev_id = $result['id'];
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
