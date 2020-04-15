<!DOCTYPE html>
<html>
  <head>
    <title>Site name goes here</title>
    <link rel="stylesheet" type="text/css" href="../includes/style.css">
  </head>
  <body>
    <nav>
      <a href="../home/">HOME</a>|<a href="../search/">SEARCH</a>
    </nav>
    <hr>
    <?php if(!$sentence->prev_id === false) : ?>
      <a href="./?id=<?php echo $sentence->prev_id; ?>">PREVIOUS SENTENCE</a>
    <?php endif; ?>
    <a href="../narrative/?id=<?php echo $sentence->narrative_id; ?>">UP TO NARRATIVE</a>
    <?php if(!$sentence->next_id === false) : ?>
    <a href="./?id=<?php echo $sentence->next_id; ?>">NEXT SENTENCE</a>
    <?php endif; ?>
    <h1>Sentence:</h1>
    <p>
      <?php echo $sentence->toString(); ?>
    </p>

    <h1>Graphs:</h1>
    <?php foreach($sentence->graphs as $graph) : ?>
    <div class="graph">
      <?php echo $graph->graph; ?>
    </div>
    <?php endforeach; ?>

  </body>
</html>
