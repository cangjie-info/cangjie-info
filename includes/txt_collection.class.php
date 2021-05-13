<?php

class TxtCollection {
   public int $id = 0;
   public string $short_name;
   public string $name_zh;
   public string $name_en;
   public $subcollections = array();

   public static function getById(int $id) {
      global $db;
      $qry = 'SELECT * FROM txt_collections WHERE id=:id;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':id', $id);
      $stmt->execute();
      $collection = $stmt->fetchObject('TxtCollection');
      return $collection;
   }

   public static function getByShortName(string $short_name) {
      global $db;
      $qry = 'SELECT * FROM txt_collections '
         . 'WHERE short_name = :short_name;';

      $stmt = $db->prepare($qry);
      $stmt->bindValue(':short_name', $short_name);
      $stmt->execute();
      $collection = $stmt->fetchObject('TxtCollection');
      return $collection;
   }

   public function delete() {
      global $db;
      // deletes collection after calling delete on all its subcollections.
      // TODO conside what to do about childless items in the inscr_ and arch_ hirearchy.
      // TODO wrap in a transaction
      // delete inscr_graphs
      $qry = 'DELETE inscr_graphs '
         . 'FROM inscr_graphs '
         . 'INNER JOIN txt_sentences ON inscr_graphs.sentence_id = txt_sentences.id '
         . 'INNER JOIN txt_narratives ON txt_sentences.narrative_id = txt_narratives.id '
         . 'INNER JOIN txt_subcollections ON txt_narratives.subcollection_id = txt_subcollections.id '
         . 'INNER JOIN txt_collections ON txt_subcollections.collection_id = txt_collections.id '
         . 'WHERE txt_collections.id = :id;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      // delete sentences
      $qry = 'DELETE txt_sentences '
         . 'FROM txt_sentences '
         . 'INNER JOIN txt_narratives ON txt_sentences.narrative_id = txt_narratives.id '
         . 'INNER JOIN txt_subcollections ON txt_narratives.subcollection_id = txt_subcollections.id '
         . 'INNER JOIN txt_collections ON txt_subcollections.collection_id = txt_collections.id '
         . 'WHERE txt_collections.id = :id;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      // delete narratives  
      $qry = 'DELETE txt_narratives '
         . 'FROM txt_narratives '
         . 'INNER JOIN txt_subcollections ON txt_narratives.subcollection_id = txt_subcollections.id '
         . 'INNER JOIN txt_collections ON txt_subcollections.collection_id = txt_collections.id '
         . 'WHERE txt_collections.id = :id;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      // delete subcontainers
      $qry = 'DELETE txt_subcollections '
         . 'FROM txt_subcollections '
         . 'INNER JOIN txt_collections ON txt_subcollections.collection_id = txt_collections.id '
         . 'WHERE txt_collections.id = :id;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      // delete container
      $qry = 'DELETE FROM txt_collections WHERE txt_collections.id = :id;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
   }

   public function insert(){
      global $db;
      $qry = 'INSERT INTO txt_collections (short_name, name_zh, name_en)'
         . 'VALUES (:short_name, :name_zh, :name_en);';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':short_name', $this->short_name);
      $stmt->bindValue(':name_zh', $this->name_zh);
      $stmt->bindValue(':name_en', $this->name_en);
      $stmt->execute();
      $this->id = $db->lastInsertId();
   }

   public function appendSubcollections() { // TODO rename.
      global $db;
      $qry= 'SELECT id, name_en, name_zh ' .
         'FROM txt_subcollections ' .
         'WHERE collection_id=:id ' .
         'ORDER BY number;';
      $stmt= $db->prepare($qry);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      while($subcollection = $stmt->fetchObject('TxtSubcollection')) {
         $this->appendSubcollection($subcollection);
      }
   }

  public function appendSubcollection(TxtSubcollection $subcollection) {
     $subcollection->number = count($this->subcollections) + 1;
     $this->subcollections[] = $subcollection;
  }

  public static function getAllCollections() {
     global $db;
     $qry = 'SELECT * FROM txt_collections '
        . 'ORDER BY name_zh;';
     $stmt = $db->prepare($qry);
     $stmt->execute();
     return $stmt->fetchAll(PDO::FETCH_CLASS, 'TxtCollection');
  }
}
