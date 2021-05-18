<?php

require_once('../includes/all_php.php');
require_once('../includes/db.php');
require_once('../includes/search.class.php');
require_once('../includes/txt_sentence.class.php');
require_once('../includes/inscr_graph.class.php');

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
   $search = new Search;
   $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);
   if($page) {
      $search->page = $page;
   }
   else {
      $page = 1;
   }
   $search->target_graph = $target;
   $search->doSearch();
   include('result.html.php');
   exit;
}

