<h1>Inscribed object: 
	<?php echo $object['name'] . ' (' . $object['type'] . ')'; ?>
</h1>
<hr>
<h2>Edit:</h2>
<p>Fields are populated with current values. Edit as needed and then press Update.</p>
<form method='post' action='.'>
	<input type='hidden' name='id' value='<?php echo $id; ?>'>
	<input type='hidden' name='action' value='edit'>
	<label>name</label>
	<input type='text' name='name' value='<?php echo $object['name']; ?>'>
	<label>type</label>
	<input type='text' name='type' value='<?php echo $object['type']; ?>'>
	<input type='submit' value='Submit'>
</form>
<h2>Archeological objects:</h2>
<?php foreach($arch_objects as $object){?>
	<a href='../arch_object/?id=<?php echo $object['id'];?>'>
		<?php echo $object['name'];?>
	</a>
	<br>
<?php } ?>
<h2>Inscribed surfaces: </h2>
<?php foreach($surfaces as $surface) { ?>
	<h3>Surface: </h3>
	<a href='../inscr_surface/?id=<?php echo $surface['id']; ?>'>
		<?php echo $surface['name']; ?>
	</a>
	<br>
	<h3>Inscriptions:</h3>
	<br>
	<?php foreach($surface['inscriptions'] as $key => $inscription) { ?>
		<a href='../inscription/?id=<?php $inscription['id']; ?>'>
			<?php echo $key . ' ' . $inscription['name']; ?>
		</a>
	<?php } ?>
<?php } ?>
	<h3>Add surface</h3>
	<form method='post' action='.'>
		<input type='hidden' name='id' value='<?php echo $id; ?>'>
		<input type='hidden' name='action' value='add_surf'>
		<label>name:</name>
		<input type='text' name='name'>
		<input type='submit' value='Add'>
	</form>

<?php 
/*

<p>
	<a href='../excavation/?id=<?php echo $object['excavation_id']; ?>'>
	<?php echo $object['excavation_en'] . ' ' . $object['excavation_zh'] . ' (' . $object['year'] . ') '; ?>
	</a>
	<a href='../context/?id=<?php echo $object['context_id']; ?>'>
	<?php echo $object['context_name'] . ' : '; ?>
	</a>
	<?php echo $object['object_name']; ?>
</p>

<h2>
	<a href='../inscr_object/?id=<?php echo $inscr_object['id']; ?>'>
	Inscribed object: <?php echo $inscr_object['name'] . ' (' . $inscr_object['object_type'] . ')'; ?></a>
</h2>
<?php foreach($inscr_object['inscr_surfaces'] as $surface) { ?>
	<h3>
		<a href='../inscr_surface/?id=<?php echo $surface['id'];?>'>
Inscribed surface: <?php echo $surface['name']; ?></a>
	</h3></a>
	<?php foreach($surface['inscrs'] as $inscr) { ?>
		<h4>
			<a href='../inscr/?id=<?php echo $inscr['id']; ?>'>Inscription: <?php echo $inscr['name']; ?>
		</h4>
	<?php } ?>
<?php } ?>
 */
?>
