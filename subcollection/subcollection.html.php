<a href="../collection/?id=<?php echo $subcollection->collection_id; ?>">UP TO COLLECTION</a> |
<a href="../collections/">ALL COLLECTIONS</a>

<h1>Subcollection: <?php echo $subcollection->name_en . 
  ' ' . $subcollection->name_zh; ?></h1>

<?php foreach($subcollection->narratives as $narrative) : ?>
	<p>
		<a href="../narrative/?id=<?php echo $narrative->id; ?>"/>
			<?php echo $narrative->getIncipit()->toString(); ?>
		</a>
	</p>
<?php endforeach; ?>
