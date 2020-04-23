<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');

// figure out what collection we are paginating
// figure out what publication we are paginating
$collection_id = filter_input(INPUT_POST, 'collection_id', FILTER_SANITIZE_NUMBER_INT);
$pub_id = filter_input(INPUT_POST, 'pub_id', FILTER_SANITIZE_NUMBER_INT);

// if the collection and publication are not known,
// we need to present the choice from those available to the user.
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
// Get all graphs in collection with their ids ordered within collection
$qry = 'SELECT graph, inscr_graphs.id AS id, pub_page, interpolated ' .
  'FROM inscr_graphs ' .
  'INNER JOIN txt_sentences ON txt_sentences.id = inscr_graphs.sentence_id ' .
  'INNER JOIN txt_narratives ON txt_narratives.id = txt_sentences.narrative_id ' .
  'INNER JOIN txt_subcollections ON txt_subcollections.id = txt_narratives.subcollection_id ' .
  'LEFT JOIN pubs_graph_page ON inscr_graphs.id=pubs_graph_page.graph_id ' .
  'WHERE collection_id=:collection_id ' .
  'AND (pubs_graph_page.pub_id=:pub_id OR pubs_graph_page.pub_id IS NULL) ' .
  'ORDER BY txt_subcollections.number, txt_narratives.number, txt_sentences.number, number_sentence;';
$stmt = $db->prepare($qry);
$stmt->bindValue(':collection_id', $collection_id);
$stmt->bindValue(':pub_id', $pub_id);
$stmt->execute();
$collection_graphs_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);

// figure out if the pagination mappings are already there.
// if not, figure out how many pages there are and add those to the pagination
// table, with interpolated graph_id.

$qry = 'SELECT name, page_offset, pubs_graph_page.id as id, graph_id, pub_page, interpolated ' . 
  'FROM pubs_graph_page ' . 
  'RIGHT JOIN pubs ON pubs.id = pubs_graph_page.pub_id ' .
  'WHERE pubs.id = :pub_id ' .
  'ORDER BY pub_page;';
//TODO currently the query will not handle the case where a publication maps to 
//more than one collection. Fix this, by joining through txt hierarchy and adding
//collection id to where clause. Possibly do this in a single query with
//previous query which is similar.
$stmt = $db->prepare($qry);
$stmt->bindValue(':pub_id', $pub_id);
$stmt->execute();
$pagination = $stmt->fetchAll(PDO::FETCH_ASSOC);
if($pagination === false) { // if query fails...
  echo '$pagination === false at line 41';
  exit();
}
if(is_null($pagination[0]['id'])) { // if no pagination exsits for this publication...
  $pub_name = $pagination[0]['name'];
  echo "No pagination exists for pub_id = $pub_name.\n";
  $page_count = check_pub_dir($pub_name); // function verifies that publication img files are correct.
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
else { // pagination table already exists for this publication
  $offset = $pagination[0]['page_offset'];
  $graph_span = filter_input(INPUT_POST, 'graph_span', FILTER_SANITIZE_NUMBER_INT);
  if(!isset($gaph_span)) {
    $graph_span = 100;
  }
  // get graph number in collection ordering (must be in range) or =1
  $graph_number = filter_input(INPUT_POST, 'graph_number', FILTER_SANITIZE_NUMBER_INT);
  if(!isset($graph_number) || $graph_number < 0) {
    $graph_number = 0;
    //todo set graph number accoring to page
  }
  elseif ($graph_number >= count($collection_graphs_ids)) {
    $graph_number = count($collection_graphs_ids) - $graph_span;
  }
  // get page number (must be in range) or =1 + offset
  $page_number = filter_input(INPUT_POST, 'page_number', FILTER_SANITIZE_NUMBER_INT);
  if(!isset($page_number) || $page_number - $offset < 1) {
    $page_number = 1 + $offset;
    //todo set page number according to graph
  }
  elseif($page_number >= count($pagination) + $offset) {
    $page_number = count($pagination) + $offset - 1;
  }
  // get graphs in range
  $str_graphs = '';
  for($n = $graph_number; $n < $graph_number + $graph_span; $n++) {
    $str_graphs .= $collection_graphs_ids[$n]['graph'];
    // insert page markers into graph sequence
    if(isset($collection_graphs_ids[$n]['pub_page'])) {
      $marker = "[" . $collection_graphs_ids[$n]['pub_page'];
      if($collection_graphs_ids[$n]['interpolated'] == 1) {
        $marker .= 'i';
      }
      $marker .= ']';
      $str_graphs .= $marker;
    }
  }
  echo $str_graphs;
  require_once('paginator.html.php');

  // display page
}

/*
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



