<h1>All Collections:</h1>

<?php foreach($collections as $collection) : ?>
	<p>
		<a href="../collection/?id=<?php echo $collection->id; ?>"/>
		<?php echo $collection->name_en . ' ' . $collection->name_zh; ?>
		</a>
	</p>
<?php endforeach; ?>
