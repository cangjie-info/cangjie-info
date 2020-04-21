<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

// figure out what collection we are paginating
// figure out what publication we are paginating
$collection_id = filter_input(INPUT_POST, 'collection_id', FILTER_SANITIZE_NUMBER_INT);
$pub_id = filter_input(INPUT_POST, 'pub_id', FILTER_SANITIZE_NUMBER_INT);

if(!isset($collection_id) or !isset($pub_id)) {
  $qry = 'SELECT * FROM pubs;';
  $stmt = $db->prepare($qry);
  $stmt->execute();
  $allPubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $stmt->closeCursor();
  $qry = 'SELECT * FROM txt_collections;';
  $stmt = $db->prepare($qry);
  $stmt->execute();
  $allCollections = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $stmt->closeCursor();
  require_once('choose_pub.html.php');
  exit();
} 
else {
  echo "collection_id = " . $collection_id . ", pub_id = " . $pub_id;
}

// get all graphs in collection with their ids and row numbers by collection
// order
$qry = 'SELECT graph, inscr_graphs.id AS id ' .
  'FROM inscr_graphs ' .
  'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
  'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
  'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
  'WHERE collection_id=:collection_id ' .
  'ORDER BY txt_subcollections.number, txt_narratives.number, txt_sentences.number, number_sentence;';
$stmt = $db->prepare($qry);
$stmt->bindValue(':collection_id', $collection_id);
$stmt->execute();
$collection_graphs_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);

// figure out if the pagination mappings are already there
// if not, figure out how many pages there are and add those to the pagination
// table, with interpolated graph_id.

$qry = 'SELECT name, page_offset, pubs_graph_page.id as id, graph_id, pub_page, interpolated ' . 
  'FROM pubs_graph_page ' . 
  'RIGHT JOIN pubs ON pubs.id = pubs_graph_page.pub_id ' .
  'WHERE pubs.id = :pub_id ' .
  'ORDER BY pub_page;';
$stmt = $db->prepare($qry);
$stmt->bindValue(':pub_id', $pub_id);
$stmt->execute();
$pagination = $stmt->fetchAll(PDO::FETCH_ASSOC);
if($pagination === false) {
  echo '$pagination === false at line 41';
  exit();
}
if(is_null($pagination[0]['id'])) {
  $pub_name = $pagination[0]['name'];
  echo "No pagination exists for pub_id = $pub_name.\n";
  $page_count = check_pub_dir($pub_name);
  if($page_count === false) {
    echo "check_pub_dir() returned false.";
    exit();
  }
  echo "page_count = $page_count.\n";
  echo 'graph count in pub_id = '  . $pub_id . ' = ' . count($collection_graphs_ids) . "\n";
  $qry = "INSERT INTO pubs_graph_page (pub_id, graph_id, pub_page) VALUES ";
  for($n = 1; $n <= $page_count; $n++) {
    $collection_row_number = intval(count($collection_graphs_ids) * ($n - 1) / $page_count);
    $qry_row = " ( " . $pub_id . ", " . 
      $collection_graphs_ids[$collection_row_number]['id'] . 
      ", " . ($n + intval($pagination[0]['page_offset'])) . " ),";
    $qry .= $qry_row;
  }
  // remove final comma
  $qry = rtrim($qry, ',');
  $qry .= ";";
  echo $qry . "\n";
  $db->exec($qry);
}
else {
  var_dump($pagination);
}

/*
//
// Allow setting number of graphs at a time.
//
// set page to first page.
// set graph to first graph in collection.
//
// display page
// display graphs (include marker for existing page breaks) by printing 
// corresponding 100 graph text chunk into form.
// 
// allow moving both page and graphs using buttons, etc.
// if page is not interpolated, update first graph with it. Otherwise, leave
// first gaph where it is. Update graphs button. Update page button.
//
// Button for setting page.
  // Check that nothing other than page break markers has been added.
  // Write marked graph id to relevant pagination row. If there is more than
  // one maker, write all. Setting inerpolated to false.
  // look ahead to next non-interpolated row, if exists, interpolate pages in between
  // look back to prev non-interpolated row, if exists, inerpolate pages in between.
//
// Button for setting 
//
//

// need to know the following:
// id for the publication to paginate, id for the collection to paginate
// how many pages there are
// how many characters there are in the collection.
// number of the page to attempt to paginate
// id of the graph to attempt the pagination from.
  // this may be looked up (interpolated or not), or set by hand.

// get id of graph to start the pagination at
$id = getFilteredId();
$collection_id = 40;

//get row_number of graph with that id
$qryRowNumber = '';
$qryRowNumber .= 'SELECT x.row_number FROM (';
$qryRowNumber .= 'SELECT @row_number:=@row_number+1 AS row_number, graph, inscr_graphs.id ' .
  'FROM (SELECT @row_number:=0) AS t, inscr_graphs ' .
  'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
  'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
  'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
  'WHERE collection_id=:collection_id ' .
  'ORDER BY txt_subcollections.number, txt_narratives.number, txt_sentences.number, number_sentence ';
$qryRowNumber .= ') AS x WHERE id=:id;';
$stmtRowNumber = $db->prepare($qryRowNumber);
$stmtRowNumber->bindValue(':collection_id', $collection_id);
$stmtRowNumber->bindValue('id', $id);
$stmtRowNumber->execute();
$rowNumber=$stmtRowNumber->fetch(PDO::FETCH_ASSOC);
$rn = $rowNumber['row_number'];

//need read lock!!

//get range of graphs
$span = 50; // number of graphs either side of target id
$offset = $rn > $span ? $rn - $span : 0;
$double_span = $span * 2;
$qryRange = 'SELECT x.graph, x.id FROM (';
$qryRange .= 'SELECT @row_number:=@row_number+1 AS row_number, graph, inscr_graphs.id ' .
  'FROM (SELECT @row_number:=0) AS t, inscr_graphs ' .
  'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
  'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
  'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
  'WHERE collection_id=:collection_id ' .
  'ORDER BY txt_subcollections.number, txt_narratives.number, txt_sentences.number, number_sentence ';
$qryRange .= ") AS x LIMIT $offset, $double_span;";
$stmtRange = $db->prepare($qryRange);
$stmtRange->bindValue(':collection_id', $collection_id);
$stmtRange->execute();
$range=$stmtRange->fetchAll(PDO::FETCH_ASSOC);
echo $qryRange;
var_dump($range);

/*


// get all data relevant to position of start graph
$qryStart = 'SELECT inscr_graphs.number_sentence AS graph_number, ' . 
  'txt_sentences.number AS senence_number, ' . 
  'txt_narratives.number AS narrative_number, ' . 
  'txt_subcollections.number AS subcollection_number, ' . 
  'txt_subcollections.collection_id, ' . 
  'graph FROM inscr_graphs ' .
  'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
  'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
  'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
  'WHERE inscr_graphs.id = :id;';
$stmtStart = $db->prepare($qryStart);
$stmtStart->bindValue(':id', $id);
$stmtStart->execute();
$start = $stmtStart->fetch(PDO::FETCH_ASSOC);
$stmtStart->closeCursor();

// echo $id . '|' . $start['graph'];
// var_dump($start);

$qry = 'SELECT @row_number:=@row_number+1 AS row_number, graph FROM inscr_graphs, (SELECT @row_number:=0) AS t WHERE @row_number < 20 LIMIT 50;';
$stmt = $db->prepare($qry);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// var_dump($result);

$qry= 'SELECT row_number FROM ' . 
      '(SELECT @row_number:=@row_number+1 AS row_number, graph, inscr_graphs.id AS id FROM (SELECT @row_number:=0) AS t, inscr_graphs ' . 
      'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
      'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
      'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
      'WHERE collection_id=40 ' .
      'ORDER BY txt_subcollections.number, txt_narratives.number, txt_sentences.number, number_sentence) ' .
      "WHERE id=1;";
$stmt= $db->prepare($qry);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
var_dump($result);

$qryGraphs = 'SELECT inscr_graphs.id, graph FROM inscr_graphs ' .
  'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
  'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
  'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
  'WHERE collection_id=:collection_id '.
  'AND (txt_subcollections.number > :subcollection_number ' .
  'OR (txt_subcollections.number = :subcollection_number AND ' .
  'OR 
  'OR 
  'ORDER BY txt_subcollections.number, txt_narratives.number, txt_sentences.number, inscr_graphs.number_sentence ' .
  'LIMIT 500,50;';
$stmtGraphs = $db->prepare($qryGraphs);
$stmtGraphs->execute();
$graphs = $stmtGraphs->fetchAll();
$stmtGraphs->closeCursor();
foreach($graphs as $graph) {
  echo $graph['graph'];
}
 */
?>



