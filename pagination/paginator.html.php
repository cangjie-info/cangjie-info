<!DOCTYPE html>
<html>
  <head>
    <title>
    </title>
  </head>
  <body>
    <form action="./" method="post">
      <textarea name="graphs" rows="5" cols="50"><?php echo $str_graphs; ?></textarea>
      <input type="submit" value="done" />
      <input type="hidden" name="mode" value="edited" />
      <input type="hidden" name="page_number" value="<?php echo $page_number; ?>" />
      <input type="hidden" name="graph_number" value="<?php echo $graph_number; ?>" />
      <input type="hidden" name="page_span" value="<?php echo $graph_span; ?>" />
      <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>" />
      <input type="hidden" name="pub_id" value="<?php echo $pub_id; ?>" />
    </form>
    <?php /*

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
    */
    ?>
  </body>
</html>
