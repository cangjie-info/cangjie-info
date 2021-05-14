<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_collection.class.php');

$collections = TxtCollection::getAllCollections();
foreach($collections as $collection){
   $countDistinct = $collection->countGraphs();
   $collection->count = $countDistinct['count'];
   $collection->distinct = $countDistinct['countDistinct'];
}

require_once('../includes/all_html_top.html.php');
require_once('collections.html.php');
require_once('../includes/all_html_bottom.html.php');

?>
