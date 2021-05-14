<a href="../collection/?id=<?php echo $subcollection->collection_id; ?>">UP TO COLLECTION</a> |
<a href="../collections/">ALL COLLECTIONS</a>

<h1>Subcollection: <?php echo $subcollection->name_zh; ?> <i><?php echo $subcollection->name_en; ?></i></h1>

<?php foreach($subcollection->narratives as $narrative) : ?>
	<h2>
		<a href="../narrative/?id=<?php echo $narrative->id; ?>"/>
         <?php echo $narrative->number . '. <i>' 
                  . $narrative->name_en . '</i> ' 
                  . $narrative->name_zh; ?>
      </a> 
	</h2>
   <p><?php echo  $narrative->getIncipit()->toString(); ?></p>
<?php endforeach; ?>
