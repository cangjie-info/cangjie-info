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
	<label>img_y</label>
	<input type='text' name='img_y' value='<?php echo $y; ?>'>
	<label>img_w</label>
	<input type='text' name='img_w' value='<?php echo $w; ?>'>
	<label>img_h</label>
	<input type='text' name='img_h' value='<?php echo $h; ?>'>
	<input type='submit' value='Submit'>
</form>
<p><a href='../inscr_object/?id=<?php echo $surface['inscr_object_id']; ?>'>RETURN TO INSCRIBED OBJECT</a></p>
<img src='../image/?table=surface&id=<?php echo $surface['id'];?>'>
<?php foreach($inscriptions as $key => $inscription): ?>
<p>Inscription #<?php echo $key; ?></p>
<form method='post' action='.'>
	<input type='hidden' name='surf_id' value='<?php echo $id;?>'>
	<input type='hidden' name='inscr_id' value='<?php echo $inscription['id'];?>'>
	<input type='hidden' name='action' value='add_inscr_text'>
	<label>Text:</label>
	<textarea name='text' maxlength='1000'></textarea>
	<input type='submit' value='Submit'>
</form>

<?php endforeach; ?> 
