<?php 

class TxtSubcollection {
	public int $id = 0;
	public int $collection_id;
	public string $name_en;
	public string $name_zh;
	public int $number;
	public $narratives = array();

	public static function getById($id) {
		global $db;
		$qry = 'SELECT * FROM txt_subcollections WHERE id=:id;';
		$stmt = $db->prepare($qry);
		$stmt->bindValue(':id', $id); 
		$stmt->execute();
		$subcollection = $stmt->fetchObject('TxtSubcollection');
		if(!isset($subcollection->id)) { // if no subcollection found for this id
			exit('invalid id');
		}
		return $subcollection;
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
