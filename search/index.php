<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/search.class.php');

$action = filter_input(INPUT_POST, 'action');
if($action == NULL) {
   $action = 'search_form';
}

if($action == 'search_form') {
   include('search_form.html');
   exit;
}

if($action == 'search') {
   $target = filter_input(INPUT_POST, 'graph', FILTER_SANITIZE_STRING);
   echo "search for $target";
   $search = new Search;
   $search->target_graph = $target;
   $search->doSearch();
   exit;
}

