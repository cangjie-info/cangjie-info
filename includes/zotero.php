<?php 

//library of functions for Zotero integration.
// 38nTdQsBI8B9911gNJBsWYQs
// https://api.zotero.org/groups/2469003/items/ED8LAGL6?v=3&format=bib&style=elsevier-harvard&key=38nTdQsBI8B9911gNJBsWYQs
//
function getZotItemFormatted($key) {
	$url = 'https://api.zotero.org/groups/2469003/items/' . $key . '?v=3';
	$zot_data = file_get_contents($url);
	return $url;
}


function getZotItem($key) {
  $url = 'https://api.zotero.org/groups/2469003/items/' . $key . '?v=3';
  $zot_data_json = file_get_contents($url);
  $zot_data = json_decode($zot_data_json, true)['data'];
  return $zot_data;
}

function getZotBibString($key) {
  $zot_data = getZotItem($key);
  return zotDataToString($zot_data);
}

function zotDataToString($zot_data) {
  $bib_string = '';
  if ($zot_data['itemType'] == 'book') {
    $bib_string .= $zot_data['creators'][0]['name'] . '. ';
    $bib_string .= $zot_data['title'] . '. ';
    $bib_string .= $zot_data['place'] . ': ';
    $bib_string .= $zot_data['publisher'] . ' (';
    $bib_string .= $zot_data['date'] . ').';
  }
  else {
    $bib_string .= $zot_data['title'] . ' NO FORMAT FOR THIS TYPE - PLEASE ADD ME!';
  }
  return $bib_string;
}

?>
