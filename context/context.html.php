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
<!-- ARCH_OBJECTS IN CONTEXT -->
<!-- ADD NEW ARCH_OBJECT -->
	<!-- create inscr_object to go with it by default -->
<!-- ASSOCIATED INSCR_OBJECTS -->
<!-- ASSOCIATED INSCR_SURFS -->
<!-- ASSOCIATED INSCRS -->
