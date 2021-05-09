<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/txt_collection.class.php');
require_once('../includes/txt_subcollection.class.php');

$id = getFilteredId();

$collection = TxtCollection::getById($id);
$collection->appendSubcollections();

require_once('../includes/all_html_top.html.php');
require_once('collection.html.php');
require_once('../includes/all_html_bottom.html.php');
