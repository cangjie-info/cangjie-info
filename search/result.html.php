<h1>Search results for <?php echo $target; ?></h1>
<p>Total instances = <?php echo $search->count; ?></p>
<p>Page = <?php echo $search->page; ?></p>

<?php if($search->page > 1) : ?>
<form action='.' method='post'>
   <input type='hidden' name='action' value='search'>
   <input type='hidden' name='target' value='<?php echo $search->target; ?>'>
   <input type='hidden' name='page' value='<?php echo $search->page - 1; ?>'>
   <input type='submit' value='Prev page'>
</form>
<?php endif; ?>

<?php if($search->page < $search->count / $search->results_per_page) : ?>
<form action='.' method='post'>
   <input type='hidden' name='action' value='search'>
   <input type='hidden' name='target' value='<?php echo $search->target; ?>'>
   <input type='hidden' name='page' value='<?php echo $search->page + 1; ?>'>
   <input type='submit' value='Next page'>
</form>
<?php endif; ?>

<?php foreach ($search->sentences as $sentence) : ?>
   <p><a href='../sentence/?id=<?php echo $sentence->id; ?>'>
      <?php 
      	if($sentence->prev_id) echo $sentence->getPrev()->appendGraphsFromDb()->toString();
   		echo $sentence->toString();
  	 	if($sentence->next_id) echo $sentence->getNext()->appendGraphsFromDb()->toString();	
  	 	echo '(' . $sentence->short_name . ')'; ?>
   </a></p>
<?php endforeach;
