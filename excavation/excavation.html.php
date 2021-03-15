<h1>
<?php 
echo $excavation['name_en'] . ' ' . $excavation['name_zh'] . ' (' . $excavation['year'] . ')';  
?>
</h1>
<!-- EXCAVATION DETAILS -->
<p>Lat = <?php echo $excavation['latitude'];?></p>
<p>Long = <?php echo $excavation['longitude'];?></p>
<p>Excavated by = <?php echo $excavation['excavator'];?></p>

<!-- EDIT DETAILS -->
<hr>
<h2>Edit excavation data</h2>
<p>Fields are initilized to current values. Edit and press 'Update' button. All fields must have values.</p>
<form action="." method="post" id="edit_form">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="action" value="edit">
<?php 
foreach($excavation as $key => $value){
?>
	<label><?php echo $key; ?>:</label>
	<input type='text' name='<?php echo $key; ?>' value='<?php echo $value; ?>'>
	<br>
<?php } ?>
<input type="submit" value="Update">
</form>

<!-- REFS -->
<hr>
<h2>Refs</h2>
<?php foreach($refs as $ref){
	echo getZotItemFormatted($ref['zot_item_key']);
	if($ref['pages']){
		echo 'page(s): '	. $ref['pages'];
	}
	if($ref['note']){
		echo $ref['note'];
	}
?>
	<form action="." method="post">
	<input type="hidden" name="action" value="delete_ref">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="hidden" name="ref_id" value="<?php echo $ref['id']; ?>">
	<input type="submit" value="delete">
	</form>
<?php
	echo "<br>";
}
?>
<form action="." method="post" id="new_ref">
<input type="hidden" name="action" value="add_ref">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<label>Zotero item key</label>
<input type="text" name="zot_item_key">
<label>Page(s):</label>
<input type="text" name="pages">
<label>Note:</label>
<input type="text" name="note">
<input type="submit" value="Add">
</form>
