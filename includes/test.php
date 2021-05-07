<?php

require_once('all_php.php');

echo yearToBCE(-100);
echo yearToBCE(100);
echo yearToBCE(0);
echo yearToBCE(-1);
?>

<hr>

<?php 
echo rangeToBCE(-99, 100);
echo '<hr>';
echo rangeToBCE(-199, -100);
echo '<hr>';
echo rangeToBCE(99, 100);
echo '<hr>';
echo rangeToBCE(-99, -100);
echo '<hr>';

?>

