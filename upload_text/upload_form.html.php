<h1>Text data file upload</h1>
<p>Click on Browse... button to find the local file to be uploaded, then click Upload button.
Filetype must be of type "text/plain", with a maximum size of <?php echo $max_file_size; ?>.</p>
<form enctype="multipart/form-data" action="." method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>">
   <input type="hidden" name="action" value="uploaded">
   Upload this file: <input name="userfile" type="file">
   <input type="submit" value="Upload">
</form>
