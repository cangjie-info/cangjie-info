<!DOCTYPE html>
<html>
  <head>
    <title>
    </title>
  </head>
  <body>
    <p>Publications list:
    <?php 
    foreach ($allPubs as $pub) {
      echo '<a href="?name=' . $pub['name'] . '&page=1">' . $pub['name'] . "</a> \n"; 
    } 
    ?>
    <!-- previous page button -->
    <form action="./" method="get">
      <input type="hidden" 
             name="name" 
             value="<?php echo htmlspecialchars($name); ?>" 
             />
      <button name="page" type="submit" 
                          value="<?php echo $page - 1; ?>" >prev page</button>
      <button name="page" type="submit" 
                          value="<?php echo $page + 1; ?>" >next page</button>
    </form>
    <form>
      <input type="submit" value="jump to page: " >
      <input type="text" name="page">
      <input type="hidden" 
             name="name" 
             value="<?php echo htmlspecialchars($name); ?>" 
             />
    </form>
    </p>
    <p>
    <?php echo $bib_string . ' p. ' . $page; ?>
    </p>
    <img style="max-width: 100%" src="<?php echo $file_name; ?>" />
  </body>
</html>
