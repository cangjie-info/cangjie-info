<h1>Search results for <?php echo $target; ?></h1>
<p>Total instances = <?php echo $search->count; ?></p>
<p>Page = <?php echo $search->page; ?></p>
<?php foreach ($search->results as $result) : ?>
   <p><a href='../sentence/?id=<?php echo $result['sentence_id']; ?>'>
      <?php echo '(' 
   . $result['short_name'] 
   . ')' 
   . $result['sentence']->toString(); ?>
   </a></p>
<?php endforeach ?>
