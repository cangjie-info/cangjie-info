<?php

// MECHANISM TO ACCEPT AN UPLOADED TEXT FILE CONTAINING INSCR AND/OR TXT DATA
// OPTIONS TO 1. CHECK FILE
// 2. LOAD DATA INTO DB.

require_once('../includes/all_php.php');

$action = filter_input(INPUT_POST, 'action');
if($action === NULL) {
   $action = 'display_upload_form';
}

if($action == 'display_upload_form'){
   require_once('../includes/all_html_top.html.php');
   require_once('upload_form.html.php');
   require_once('../includes/all_html_bottom.html.php');
   exit;
}

if($action == 'uploaded'){
   $name= $_FILES["userfile"]["name"];
   $type= $_FILES["userfile"]["type"];
   $size= $_FILES["userfile"]["size"];
   $temp= $_FILES["userfile"]["tmp_name"];
   $error= $_FILES["userfile"]["error"];

   echo "$name<br>$type<br>$size<br>$temp<br>$error";
   exit;
}

/*
$fp = fopen($_FILES['uploadFile']['tmp_name'], 'rb');
    while ( ($line = fgets($fp)) !== false) {
      echo "$line<br>";
    }

   $handle = @fopen($temp, "r");
   if ($handle) {
       while (($buffer = fgets($handle, 4096)) !== false) {
           echo $buffer;
       }
       if (!feof($handle)) {
           echo "Error: unexpected fgets() fail\n";
       }
       move_uploaded_file($temp, './success.txt');
       fclose($handle);
   }
}
 */
