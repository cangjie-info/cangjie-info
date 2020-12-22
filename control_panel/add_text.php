<?php
require_once('../includes/all_php.php');

$file_name = filter_input(INPUT_POST, 'file_name');
echo $file_name . "\n";
// find file in files directory with filename
if($file = fopen($file_name, "r")) {
	echo "file opened.\n";
	while(!feof($file)) {
		$line = rtrim(fgets($file));
		if(substr($line, 0, 3) == "@@@") {
			$line = substr($line, 3);
			$command = substr($line, 0, strpos($line, "="));
			$val = substr($line, strpos($line, "=") + 1);
			echo "command = " . $command . ", value = " . $val . "\n";
			switch ($command) {
			case "COLLECTION":
				$collection_id = add_collection($val, $db);
				$unset($subcollection_id, $narrative_id, $sentence_id);
				break;
			}
		}
		else {
			echo "regular text line.\n";
		}
	}
	fclose($file);
}

// if exists, set up variables: $collection, $subcollection, $narrative, $sentence, $graph
// start reading text file line by line
// if line begins @@@
//		EITHER line resets already set variable in heirarchy, unsetting lower variable, 
//			write to db if new
//		OR line sets next unset variable in hierarchy
//			write to db if new
//			if sets narrative, then set $sentence = 1, and $graph = 1;
//		OR error, abort, roll back.
//	else line is a text line
//		read character by character. Add prepunc, add graph, add postpunc.
//		write graph
//		if postpunc is terminal or if sentence too long, write setence, increment sentence.
//
//
//
else {
	// if doesn't exist, print error
	echo "no such file as " . $file_name;
}

?>
