<h1>Archeological object</h1>

<p>
	<a href='../excavation/?id=<?php echo $object['excavation_id']; ?>'>
	<?php echo $object['excavation_en'] . ' ' . $object['excavation_zh'] . ' (' . $object['year'] . ') '; ?>
	</a>
	<a href='../context/?id=<?php echo $object['context_id']; ?>'>
	<?php echo $object['context_name'] . ' : '; ?>
	</a>
	<?php echo $object['object_name']; ?>
</p>

	<h2>Inscribed object: <?php ?></h2>
<p>
<?php var_dump($inscr_object); ?>
</p>
