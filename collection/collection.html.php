<a href="../collections/">ALL COLLECTIONS</a>

<h1>Collection: <?php echo $collection->name_en . 
  ' ' . $collection->name_zh; ?></h1>

<?php foreach($collection->subcollections as $subcollection) : ?>
<p><a href="../subcollection/?id=<?php echo $subcollection->id; ?>"/>
<?php echo $subcollection->name_en . ' ' . $subcollection->name_zh; ?>
</a>
</p>
<?php endforeach; ?>

