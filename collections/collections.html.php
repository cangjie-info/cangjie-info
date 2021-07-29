<h1>All Collections:</h1>

<?php foreach($collections as $collection) : ?>
	<h2>
		<a href="../collection/?id=<?php echo $collection->id; ?>">
      <?php echo $collection->name_zh . ' ' 
               . '<i>' . $collection->name_en . '</i> ('
               . $collection->short_name . ')'; ?>
		</a>
   </h2>
   <p>Collection size = <?php echo $collection->count; ?>. 
      Number of distinct graphs = <?php echo $collection->distinct; ?>.</p>
<?php endforeach; 
