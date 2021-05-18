<?php

class Search {
   public string $target_graph = '';
   public int $count = 0;
   public int $page = 1; // 1-indexed.
   public int $results_per_page = 10;
   public $results = array();

   public function doSearch() {
      global $db;
      $qry = 'SELECT COUNT(*) AS count FROM inscr_graphs '
         . 'WHERE graph = :graph;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':graph', $this->target_graph);
      $stmt->execute();
      $this->count = $stmt->fetch()['count'];
      $qry = 'SELECT txt_sentences.id AS sentence_id, '
         . 'txt_collections.short_name AS short_name, '
         . 'inscr_graphs.number_sentence AS number_sentence '
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
      $stmt->bindValue(':graph', $this->target_graph);
      $stmt->bindValue(':row_count', $this->results_per_page, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $this->results_per_page * ($this->page - 1), PDO::PARAM_INT);
      $stmt->execute();
      while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $result['sentence'] = TxtSentence::getById($result['sentence_id']);
         $result['sentence']->appendGraphs();
         $this->results[] = $result;
      }
   }
}
