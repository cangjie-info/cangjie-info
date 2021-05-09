<?php


class TxtNarrative {
  public $id;
  public $subcollection_id;
  public $name_en;
  public $name_zh;
  public $number;
  public $next_id; // id of next narrative in subcollection
  public $prev_id; // id of prev narrative
  public $sentences = array();

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
