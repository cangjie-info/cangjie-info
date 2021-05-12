<?php

class TxtNarrative {
   // DB table fields
   public int $id = 0;
   public int $subcollection_id = 0;
   public string $name_en;
   public string $name_zh;
   public int $number = 0;
   // Not DB table fields
   public int $next_id = 0; // id of next narrative in subcollection, 0 if none.
   public int $prev_id = 0; // id of prev narrative, 0 if none.
   public $sentences = array();

   public static function getById(int $id) {
      global $db;
      $qry= 'SELECT * FROM txt_narratives WHERE id=:id;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':id', $id);
      $stmt->execute();
      $narrative = $stmt->fetchObject('TxtNarrative');
      if(!$narrative) { // if no narrative corresponds to id
         exit('no narrative with that id');
      }
      return $narrative;
   }

   public function insert() {
      global $db;
      $qry = 'INSERT INTO txt_narratives (subcollection_id, name_zh, name_en, number) '
         . 'VALUES (:subcollection_id, :name_zh, :name_en, :number);';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':subcollection_id', $this->subcollection_id);
      $stmt->bindValue(':name_zh', $this->name_zh);
      $stmt->bindValue(':name_en', $this->name_en);
      $stmt->bindValue(':number', $this->number);
      $stmt->execute();
      $this->id = $db->lastInsertId();
   }

   public function appendSentencesGraphs() {
      // TODO THIS IS NOT EFFICIENT - uses separate query for each sentence.
      global $db;
      $qry= 'SELECT txt_sentences.id, narrative_id, txt_sentences.number FROM txt_sentences '
         . 'INNER JOIN txt_narratives ON txt_sentences.narrative_id = txt_narratives.id '
         . 'WHERE txt_sentences.narrative_id=:id '
         . 'ORDER BY txt_sentences.number;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      $this->sentences = $stmt->fetchAll(PDO::FETCH_CLASS, 'TxtSentence');
      foreach($this->sentences as $sentence) {
         $sentence->appendGraphs();
      }
   }

   public function setNextPrevId() {
      $this->setNextId();
      $this->setPrevId();
   }

   public function setNextId() {
      global $db;
      $qry= 'SELECT id FROM txt_narratives '
         . 'WHERE subcollection_id=:subcollection_id '
         . 'AND number=:number+1;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':subcollection_id', $this->subcollection_id);
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
      $qry= 'SELECT id FROM txt_narratives '
         . 'WHERE subcollection_id=:subcollection_id '
         . 'AND number=:number-1;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':subcollection_id', $this->subcollection_id);
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

   public function getIncipit() {
      global $db;
      $incipit = new TxtSentence;
      $qry= 'SELECT graph, punc FROM inscr_graphs ' .
            'INNER JOIN txt_sentences ON sentence_id=txt_sentences.id ' .
            'INNER JOIN txt_narratives ON txt_narratives.id=narrative_id ' .
            'WHERE txt_narratives.id=:id ' .
            'ORDER BY txt_narratives.number, txt_sentences.number, number_sentence ' .
            'LIMIT 10;';
      $stmt = $db->prepare($qry);         
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      while($graph = $stmt->fetchObject('InscrGraph')) {
         $incipit->appendGraph($graph);
      }
      return $incipit;
   }

  public function appendSentence(TxtSentence $sentence) {
    $this->sentences[] = $sentence;
  }

  public function getSentenceCount() {
    return count($this->sentences);
  }

  public function getGraphCount() {
    $count = 0;
    foreach($this->sentences as $sentence) {
      $count += $sentence->getLength();
    }
    return $count;
  }

  public function toString() {
    $str = '';
    foreach($this->sentences as $sentence) {
      $str .= $sentence->toString();
    }
    return $str;
  }
}
