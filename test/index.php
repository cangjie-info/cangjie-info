<?php

include('../includes/inscr_graph.class.php');
$graph = new InscrGraph;
$string = "，,！!？?；;：:“”‘’『』「」《》。.、";
for ($i = 0; $i < mb_strlen($string); $i++) {
  $char = mb_substr($string, $i, 1);
  echo $char . ' ' . $graph->charToBit($char) . ' | ';
}

?>

