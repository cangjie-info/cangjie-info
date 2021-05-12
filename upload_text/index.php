<?php

// MECHANISM TO ACCEPT AN UPLOADED TEXT FILE CONTAINING INSCR AND/OR TXT DATA
// OPTIONS TO 1. CHECK FILE
// 2. LOAD DATA INTO DB.

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_collection.class.php');
require_once('../includes/txt_subcollection.class.php');
require_once('../includes/txt_narrative.class.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/inscr_graph.class.php');

$action = filter_input(INPUT_POST, 'action');
if($action === NULL) {
   $action = 'display_upload_form';
}

if($action == 'display_upload_form'){
   $max_file_size = 100000;
   require_once('../includes/all_html_top.html.php');
   require_once('upload_form.html.php');
   require_once('../includes/all_html_bottom.html.php');
   exit;
}

if($action == 'uploaded'){
   $name = $_FILES["userfile"]["name"];
   $type = $_FILES["userfile"]["type"];
   $size = $_FILES["userfile"]["size"];
   $temp = $_FILES["userfile"]["tmp_name"];
   $error = $_FILES["userfile"]["error"];

   if($error) {
      echo "Failed with error code $error.";
      exit;
   }
   if($type != 'text/plain') {
      echo "Filetype was $type. Must be text/plain.";
      exit;
   }
   echo "Succesfully uploaded $name, size = $size.<br>";
   process_upload($temp);
}

function process_upload($temp){
   // delete existing Lunyu
   $collection = TxtCollection::getByShortName('LY');
   $collection->delete();
   // 
   $file = fopen($temp, 'r');
   $collection = null;
   $subcollection = null;
   $narrative = null;
   $sentence = null;
   $line_number = 0;
   while(($line = fgets($file)) !== false) {
      $line_number++;
      // remember htmlentitites(), etc.
      // remember utf8 vs other encodings.
      $pattern = '/^@(\w+):(.+)$/'; //@ followed by word followed by : followed by stuff.
      if(preg_match($pattern, $line, $matches)) {
         // command line with @.
         // flush sentence if it exists
         if($sentence and $sentence->getLength() > 0) {
            $sentence->insert();
            $sentence = null;
         }
         $object = $matches[1];
         $values = explode('/', $matches[2]);
         switch($object) {
         case 'collection':
            $collection = new TxtCollection;
            $collection->short_name = $values[0];
            $collection->name_zh = $values[1];
            $collection->name_en = $values[2];
            $collection->insert();
            $subcollection = null;
            $narrative = null;
            echo "<hr>$collection->id:$collection->name_zh";
            break;
         case 'subcollection':
            if(!$collection) {
               $error_message = "Collection not set at line $line_number.";
               include('../includes/error.php');
               exit;
            }
            if($subcollection) {
               $number = $subcollection->number+1;
            }
            else {
               $number = 1;
            }
            $subcollection = new TxtSubcollection;
            $subcollection->collection_id = $collection->id;
            $subcollection->name_zh = $values[0];
            $subcollection->name_en = $values[1];
            $subcollection->number = $number;
            $subcollection->insert();
            $narrative = null;
            break;
         case 'narrative':
            if(!$subcollection){
               $error_message = "Subcollection not set at line $line_number.";
               include('../includes/error.php');
               exit;
            }
            if($narrative){
               $number = $narrative->number+1;
            }
            else {
               $number = 1;
            }
            $narrative = new TxtNarrative;
            $narrative->subcollection_id = $subcollection->id;
            $narrative->name_zh = $values[0];
            $narrative->name_en = $values[1];
            $narrative->number = $number;
            $narrative->insert();
            break;
         default:
            break;
         }
      }
      else if($line === '') {
         // blank line, do nothing.
      }
      else { // this is a text line, with no @ command.
         if(!$narrative) {
            echo "No narrative at line $line_number.";
            exit;
         }
         if(!$sentence) { // only executes once? Check! TODO
            $sentence = new TxtSentence;
            $sentence->number = 1;
         }
         $sentence->narrative_id = $narrative->id;
         $graph_array = InscrGraph::stringToInscrGraphs($line);
         foreach($graph_array as $graph) {
            $sentence->appendGraph($graph);
            if($sentence->getLength() > 100 or $graph->isSentenceFinal()) {
               $sentence->insert();
               $sentence->graphs = array();
               $sentence->id = 0;
               $sentence->number++;
            }
         }
      }
   } // end while
   if($sentence and $sentence->getLength() > 0) {
      $sentence->insert();
   }
   fclose($file);
   exit;
      // @collection:short_name/name_zh/name_en
      // @collection=id
      //    flush remaining sentence if narrative id is set
      //    insert collection if not exsists
      //    reset subcollection and narrative and sentence counts (to zero, or to append)
      //    next line must be subcollection.
      // @subcollection:name_zh/name_en
      //    flush remaining sentence
      //    insert subcollection if not exsists
      //    reset narrative and sentence counts (to zero or to append).
      //    next line must be narrative
      // @narrative:name_zh/name_en
      //    flush remaining sentence
      //    insert narrative
      //    reset sentence count
      // @excavation:
      //    flush remaining inscription if set
      //    insert excavation if not exists
      //    reset context and object and surface and inscr counts (to zero or to append)
      //    next line must be context
      // @context:
      //    flush remaining inscription if set
      //    insert context if not exsists
      //    reset object and surface and inscr counts (to zero or to append)
      //    next line must be object
      // @object: // assume arch and inscr are one
      //    etc
      // @surface:
      //    etc
      // @inscr:
      //    etc.
      // @museums:
      //    etc.
      // 
      // running text
      //    assemble sentence object and write to 
      // EOF
      //  flush remaining sentence 
      //  flush remaining inscription
}
