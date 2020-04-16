<?php

require_once('txt_sentence.class.php');

class TxtNarrative {
  public $id;
  public $subcollection_id;
  public $name_en;
  public $name_zh;
  public $number;
  public $next_id; // id of next narrative in subcollection
  public $prev_id; // id of prev narrative
  public $sentences = array();

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


