<!DOCTYPE html>
<html>
  <head>
    <title>
    </title>
  </head>
  <body>
    <form action="./" method="post">
      <select name="pub_id" >
        <?php foreach($allPubs as $pub) : ?>
        <option value="<?php echo $pub['id']; ?>"><?php echo $pub['name'];?>
        </option>
        <?php endforeach; ?>
      </select>
      <select name="collection_id">
        <?php foreach($allCollections as $collection) : ?>
        <option value="<?php echo $collection['id']; ?>"><?php echo
                $collection['name_zh']; ?>
        </option>
        <?php endforeach; ?>
      </select>
      <input type="submit" value="submit" />
    </form>
  </body>
</html>
