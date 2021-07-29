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
<hr>
<h2>Edit archeologcial object properties</h2>
<p>Fields are initialized to current values. Edit and press 'Update' button.</p>
<form action="." method="post">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="hidden" name="action" value="edit">
	<label>Archeological object name:</label>
	<input type="text" name="name" value="<?php echo $object['object_name']; ?>">
	<label>Early date:</label>
	<input type="text" name="date_early" value="<?php echo $object['date_early']; ?>">
	<label>Late date:</label>
	<input type="text" name="date_late" value="<?php echo $object['date_late']; ?>">
	<input type="submit" value="Update">
</form>
<hr>

<h2>
	<a href='../inscr_object/?id=<?php echo $inscr_object['id']; ?>'>
	Inscribed object: <?php echo $inscr_object['name'] . ' (' . $inscr_object['object_type'] . ')'; ?></a>
</h2>
<?php foreach($inscr_object['inscr_surfaces'] as $surface) { ?>
	<h3>
		<a href='../inscr_surface/?id=<?php echo $surface['id'];?>'>
Inscribed surface: <?php echo $surface['name']; ?></a>
	</h3>
	<?php foreach($surface['inscrs'] as $inscr) { ?>
		<h4>
                    <a href='../inscr/?id=<?php echo $inscr['id']; ?>'>Inscription: <?php echo $inscr['name']; ?></a>
		</h4>
	<?php }
}
