<h1>Archeological context: 
	<?php 
		echo $context['name'] . ' (' . $context['context_type'] . ')';
	?>
</h1>
<!-- CONTEXT DETAILS -->
<p><?php echo $context['description'];?></p>
<p>Date: <?php echo $context['date_early'] . ' - ' . $context['date_late']; ?></p>
<p>Excavation: 
<a href='../excavation/?id=<?php echo $context['arch_excavation_id']; ?>'>
<?php echo $excavation['name_en'] . ' ' . $excavation['name_zh'] . ' (' . $excavation['year'] . ')'; ?>
</a>
</p>
<!-- NEXT CONTEXT -->
<!-- PREV CONTEXT -->
<!-- EDIT CONTEXT DETAILS -->
<hr>
<h2>Edit context data</h2>
<p>Fields are initialized to current values. Edit and press 'Update' button.</p>
<form action="." method="post" id="edit_form">
	<input type="hidden" name="id" value="<?php echo $context['id']; ?>">
	<input type="hidden" name="action" value="edit">
	<label>Context name:</label>
	<input type="text" name="name" value="<?php echo $context['name'];?>">
	<label>Context type:</label>
	<input type="text" name="context_type" value="<?php echo $context['context_type'];?>">
	<label>Description:</label>
	<input type="text" name="description" value="<?php echo $context['description'];?>">
	<label>Date early:</label>
	<input type="text" name="date_early" value="<?php echo $context['date_early'];?>">
	<label>Date late:</label>
	<input type="text" name="date_late" value="<?php echo $context['date_late'];?>">
	<input type="submit" value="Udpate">
</form>

<!-- ARCH_OBJECTS IN CONTEXT -->
<hr>
<h2>Archeological objects in context</h2>
<?php foreach($arch_objects as $arch_object){ ?>
<a href="../arch_object/?id=<?php echo $arch_object['id'];?>">
	<?php echo $arch_object['name']; ?>
</a>
<form action='.' method='post'>
	<input type='hidden' name='action' value='delete_arch_object'>
	<input type='hidden' name='context_id' value='<?php echo $context['id']; ?>'>
	<input type='hidden' name='object_id' value='<?php echo $arch_object['id']; ?>'>
	<input type='submit' value='delete'>
</form>
</br>
<?php } ?>
<!-- ADD NEW ARCH_OBJECT -->
<p>Add new archeological object to this context.</p>
<form action="." method="post">
	<input type="hidden" name="action" value="add_arch_object">
	<input type="hidden" name="context_id" value="<?php echo $context['id']; ?>">
	<label>Archeological object name:</label>
	<input type="text" name="arch_object_name">
	<br>
	<label for'inscr_object_id'>EITHER add to existing inscr_object with this id:</label>
	<input type='text' name='inscr_object_id'>
	<br>
	<label for="checkbox">OR check to automatically add new matching 
inscr_object, inscr_surf, and inscr:</label>
	<input id='checkbox' type="checkbox" name='add_inscr' checked>
	<br>
	<label>Object type:</label>
	<input type='text' name='inscr_object_type'>
	<label>Inscr object name:</label>
	<input type='text' name='inscr_object_name'>
	<label>Surface name:</label>
	<input type='text' name='surf_name'>
	<input type="submit" name="add" value="Add">
</form>
	<!-- create inscr_object to go with it by default -->
<!-- ASSOCIATED INSCR_OBJECTS -->
<!-- ASSOCIATED INSCR_SURFS -->
<!-- ASSOCIATED INSCRS -->
