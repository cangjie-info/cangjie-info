<?php 

class TxtSubcollection {
	public int $id = 0;
	public int $collection_id;
	public string $name_zh;
	public string $name_en;
	public int $number;
	public $narratives = array();

	public static function getById($id) {
		global $db;
		$qry = 'SELECT * FROM txt_subcollections WHERE id=:id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id); 
		$stmt->execute();
		$subcollection = $stmt->fetchObject('TxtSubcollection');
		if(!$subcollection) { // if no subcollection found for this id
			exit('invalid id');
		}
		return $subcollection;
	}

   public function insert() {
      global $db;
      $qry = 'INSERT INTO txt_subcollections (collection_id, name_zh, name_en, number) '
         . 'VALUES (:collection_id, :name_zh, :name_en, :number);';
      $stmt = $db->prepare($qry);
      $stmt->bindValue(':collection_id', $this->collection_id);
      $stmt->bindValue(':name_zh', $this->name_zh);
      $stmt->bindValue(':name_en', $this->name_en);
      $stmt->bindValue(':number', $this->number);
      $stmt->execute();
      $this->id = $db->lastInsertId();
   }

	public function appendNarratives() { 	
		global $db;
		$qry= 'SELECT * FROM txt_narratives '
			. 'WHERE subcollection_id=:id '
			. 'ORDER BY number;';
		$stmt= $db->prepare($qry);
		$stmt->bindValue(':id', $this->id);
		$stmt->execute();
		$this->narratives = $stmt->fetchAll(PDO::FETCH_CLASS, "TxtNarrative");
	}

	public function appendNarrative(TxtNarrative $narrative) {
		$this->narratives[] = $narrative;
	}
	
	public function getNarrativeCount() {
		return count($this->narratives);
	}
}
