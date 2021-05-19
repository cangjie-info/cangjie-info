<h1>Search results for <?php echo $target; ?></h1>
<p>Total instances = <?php echo $search->count; ?></p>
<p>Page = <?php echo $search->page; ?></p>
<?php foreach ($search->sentences as $sentence) : ?>
   <p><a href='../sentence/?id=<?php echo $sentence->id; ?>'>
      <?php 
      	if($sentence->prev_id) echo $sentence->getPrev()->appendGraphsFromDb()->toString();
   		echo $sentence->toString();
  	 	if($sentence->next_id) echo $sentence->getNext()->appendGraphsFromDb()->toString();	
  	 	echo '(' . $sentence->short_name . ')'; ?>
   </a></p>
<?php endforeach;