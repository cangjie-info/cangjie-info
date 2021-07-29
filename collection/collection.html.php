<a href="../collections/">ALL COLLECTIONS</a>

<h1>Collection: <?php echo $collection->name_zh; ?>
   <i><?php echo $collection->name_en; ?></i>
</h1>

<?php foreach($collection->subcollections as $subcollection) : ?>
   <h2>
      <a href="../subcollection/?id=<?php echo $subcollection->id; ?>">
         <?php echo $subcollection->name_zh; ?>
         <i><?php echo $subcollection->name_en; ?></i>
      </a>
   </h2>
<?php endforeach; ?>

