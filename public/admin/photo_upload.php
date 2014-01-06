<?php
require_once '../../includes/initialize.php';

if (!$session->is_logged_in()) { redirect_to("index.php");}
?>
<?php
	$max_file_size = 1048576;
	if (isset($_POST['submit'])) {
		$photo = new Photograph();
		$photo->caption = $_POST['caption'];
		$photo->attach_file($_FILES['file_upload']);
		if ($photo->save()) {
			log_action('Photo Upload : ', "{$photo->caption} by user_ id  {$session->user_id}.");
			$session->message("Photograph uploaded successfully");
			redirect_to("list_photos.php");
		} else {
			$message = join("<br />", $photo->errors);
		}
	}
?>
<?php include_layout_template("header_admin.php");?>

<h2>Photo Upload</h2>
<div class="alert alert-error">
<?php echo output_message($message); ?>    
</div>
<form method="POST" enctype="multipart/form-data" action="photo_upload.php">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
	<p><input type="file" name="file_upload"></p>
	<p>Caption: <input type="text" name="caption" value="<?php ?>"></p>
	<input type="submit" name="submit" value="Upload">
</form>

<?php include_layout_template("footer_admin.php");?>