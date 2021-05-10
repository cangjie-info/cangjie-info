<?php

class TxtSentence {
   public int $id = 0;
   public int $narrative_id;
   public int $number;
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
