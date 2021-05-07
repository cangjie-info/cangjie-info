<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// HELPER FUNCTIONS

function yearToBCE($year){
	if($year > 0) {
		return $year . ' CE';
	}
	return -$year + 1 . ' BCE';
}

function rangeToBCE($year1, $year2){
	if($year2 <= $year1){
		$error_message = 'Year range error in rangeToBCE: $year1 = ' . $year1 . ', and $year2 = ' . $year2 . '.';
		include('error.php');
		exit();
	}
	if($year2 < 1){
		return (-$year1 + 1) . '-' . (-$year2 + 1) . ' BCE';
	}
	if($year1 > 0){
		return "$year1-$year2 CE";
	}
	return -$year1 + 1 . " BCE - $year2	CE";
}

function trim_POST() {
	function trim_value(&$value) {
		$value = trim($value);
	}
	array_filter($_POST, 'trim_value');
}

function getFilteredId() {
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id === false or $id === NULL) { // if id is not int, or not set
    exit('invalid id');
  }
  return $id;
}

function check_pub_dir($pub_name) {
// checks that dir exists, that files of the correct format are present 
// (at least one), and that their img numbers form a 
// continuous integer sequence starting from one. 
// Files in other formats (including . and ..) are ignored.
// If so, returns the number
// of images. If not, returns false.
  $dir = "../pubs/$pub_name";
  if(!file_exists($dir)) {
    return false;
  }
  $handle = opendir($dir);
  $img_numbers = array();
  while(false !== ($entry = readdir($handle))) {
    if(preg_match("/($pub_name)-([0-9]{3})(\.jpg|\.jpeg)/", $entry, $matches)) {
      $img_numbers[] = intval($matches[2]);
    }
  }
  if(count($img_numbers) == 0) return false;
  sort($img_numbers);
  for($n = 0; $n < count($img_numbers); $n++) {
    if($img_numbers[$n] != $n + 1) return false;
  }
  return count($img_numbers);
}

?>
