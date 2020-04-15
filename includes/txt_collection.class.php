<?php

require_once('../includes/txt_subcollection.class.php');

class TxtCollection {
  public $id;
  public $short_name;
  public $name_zh;
  public $name_en;
  public $subcollections = array();

  public function appendSubcollection(TxtSubcollection $subcollection) {
    $this->subcollections[] = $subcollection;
  }
}

?>

