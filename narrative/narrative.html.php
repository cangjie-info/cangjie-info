<?php if($narrative->prev_id) : ?>
   <a href="./?id=<?php echo $narrative->prev_id; ?>">PREVIOUS NARRATIVE</a> | 
<?php endif; ?>
<a href="../subcollection/?id=<?php echo $narrative->subcollection_id; ?>">UP TO SUBCOLLECTION</a> | 
<?php if($narrative->next_id) : ?>
   <a href="./?id=<?php echo $narrative->next_id; ?>">NEXT NARRATIVE</a>
<?php endif; ?>

<h1>Narrative:</h1>
<p>
   <?php foreach($narrative->sentences as $sentence) : ?>
      <a href="../sentence/?id=<?php echo $sentence->id; ?>"/>
   <?php echo $sentence->toString(); ?></a><?php endforeach; ?>
</p> 
