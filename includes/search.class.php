<?php

class Search {
   public string $target = '';
   public int $count = 0;
   public int $page = 1; // 1-indexed.
   public int $results_per_page = 10;
   public $sentences; // output of search

   // retrieve all sentences (as TxtSentence objects) containing target graph
   public function doSearch() {
      global $db;
      $qry = 'SELECT COUNT(*) AS count FROM inscr_graphs '
         . 'WHERE graph = :graph;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':graph', $this->target);
      $stmt->execute();
      $this->count = $stmt->fetch()['count'];
      $qry = 'SELECT txt_sentences.id AS id, txt_sentences.narrative_id AS narrative_id, '
         . 'txt_collections.short_name AS short_name, txt_sentences.number AS number, '
         . 'inscr_graphs.number_sentence AS target_graph_number '
         . 'FROM inscr_graphs '
         . 'JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id '
         . 'JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id '
         . 'JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id '
         . 'JOIN txt_collections ON txt_collections.id = txt_subcollections.collection_id '
         . 'WHERE inscr_graphs.graph = :graph ' 
         . 'ORDER BY short_name, txt_subcollections.number, '
         . 'txt_narratives.number, '
         . 'txt_sentences.number, inscr_graphs.number_sentence '
         . 'LIMIT :offset, :row_count;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':graph', $this->target);
      $stmt->bindValue(':row_count', $this->results_per_page, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $this->results_per_page * ($this->page - 1), PDO::PARAM_INT);
      $stmt->execute();
      $this->sentences = $stmt->fetchAll(PDO::FETCH_CLASS, 'TxtSentence');
      foreach($this->sentences as $sentence) {
         $sentence->setNextPrevId();
         $sentence->appendGraphsFromDb();
      }
   }
}
