<?php

require_once('txt_narrative.class.php');

class TxtSubcollection {
  public $id;
  public $collection_id;
  public $name_en;
  public $name_zh;
  public $number;
  public $narratives = array();

  public function appendNarrative(TxtNarrative $narrative) {
    $this->narratives[] = $narrative;
  }

  public function getNarrativeCount() {
    return count($this->narratives);
  }

}

?>
