<h1>Inscribed surface:
<?php echo $surface['name'];?>
</h1>
<h2>Edit:</h2>
<form method='post' action='.'>
	<input type='hidden' name='id' value='<?php echo $surface['id'];?>'>
	<input type='hidden' name='action' value='edit'>
	<label>name</label>
	<input type='text' name='name' value='<?php echo $surface['name'] ?>'>
	<label>img_rot</label>
	<input type='text' name='img_rot' value='<?php echo $rot; ?>'>
	<label>img_x</label>
	<input type='text' name='img_x' value='<?php echo $x; ?>'>
	<input type='submit' value='Submit'>
</form>
<p><a href='../inscr_object/?id=<?php echo $surface['inscr_object_id']; ?>'>RETURN TO INSCRIBED OBJECT</a></p>
<img src='../image/?table=surface&id=<?php echo $surface['id'];?>'>
