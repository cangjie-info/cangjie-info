<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// HELPER FUNCTIONS

function getFilteredId() {
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id === false or $id === NULL) { // if id is not int, or not set
    exit('invalid id');
  }
  return $id;
}

?>

