<?php

// require_once('../includes/txt_subcollection.class.php');
require_once('../includes/db.php');

class TxtCollection {
  public int $id = 0;
  public string $short_name;
  public string $name_zh;
  public string $name_en;
  public $subcollections = array();

  public static function getById($id) {
		global $db;
		$qry = 'SELECT * FROM txt_collections WHERE id=:id;';
		$stmt= $db->prepare($qry);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$collection = $stmt->fetchObject('TxtCollection');
		if(!isset($collection->id)) { // if no collection corresponds to that id ....
			exit('no collection for that id');
		}
		return $collection;
  }

	public function appendSubcollections() {
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
