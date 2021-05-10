<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="." method="POST">
   <!-- MAX_FILE_SIZE must precede the file input field -->
   <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
   <input type="hidden" name="action" value="uploaded">
   <!-- Name of input element determines name in $_FILES array -->
   Send this file: <input name="userfile" type="file" />
   <input type="submit" value="Send File" />
</form>
