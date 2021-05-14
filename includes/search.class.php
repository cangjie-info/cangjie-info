<?php

class Search {
   public string $target_graph = '';

   public function doSearch() {
      global $db;
      $qry = 'SELECT * FROM inscr_graphs '
         . 'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id '
         . 'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id '
         . 'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id '
         . 'INNER JOIN txt_collections ON txt_collections.id = txt_subcollections.collection_id '
         . 'WHERE inscr_graphs.graph = :graph ' 
         . 'LIMIT 30;';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':graph', $this->target_graph);
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($results as $result) {
         var_dump($result);
      }
   }
}
