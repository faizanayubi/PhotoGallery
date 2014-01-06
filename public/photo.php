<?php require_once '../includes/initialize.php';?>
<?php
	if (empty($_GET['id'])) {
		$session->message("No Photograph ID was provided.");
		redirect_to("index.php");
	}

	$photo = Photograph::find_by_id($_GET['id']);
	if (!$photo) {
		$session->message("The Photo could not be located.");
		redirect_to('index.php');
	}

	if (isset($_POST['submit'])) {
		$author = trim($_POST['author']);
		$body = trim($_POST['body']);

		$new_comment = Comment::make($photo->id, $author, $body);
		if ($new_comment && $new_comment->save()) {
			log_action('Comment', "{$author} wrote {$body}.");
			//$new_comment->try_to_send_notification();
			redirect_to("photo.php?id={$photo->id}");
		} else {
			$message = "There was an error that prevented the comment from being saved.";
		}
		
	} else {
		$author = "";
		$body = "";
	}
	$comments = $photo->comments();
?>
<?php include_layout_template("header.php"); ?>
	
<a href="index.php">&laquo; Back</a><br>
<br>
<div style="margin-left: 20px;">
	<img src="<?php echo $photo->image_path(); ?>">
	<p><?php echo $photo->caption; ?></p>
</div>

<div id="comments">
	<?php foreach ($comments as $comment):?>
	<div class="comment" style="margin-bottom: 2em;">
		<div class="author">
			<?php echo htmlentities($comment->author); ?> Wrote : 
		</div>
		<div class="body">
			<?php echo strip_tags($comment->body, '<strong><em><p>'); ?>
		</div>
		<div class="meta-info" style="font-size: 0.8em;">
			<?php echo datetime_to_text($comment->created); ?>
		</div>
	</div>
	<?php endforeach; ?>
	<?php if(empty($comments)) { echo "No Comments."; } ?>
</div>

<div id="comment-form">
	<h3>New Commenet</h3>
	<?php echo output_message($message); ?>
	<form method="POST" class="form-horizontal" action="photo.php?id=<?php echo $photo->id; ?>">
	<div class="control-group">
		<label class="control-label" for="author">Name</label>
		<div class="controls">
			<input type="text" id="author" placeholder="Name" name="author" value="<?php echo $author; ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="author">Comment</label>
		<div class="controls">
			<textarea name="body" rows="3"><?php echo htmlentities($body)?></textarea>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn" name="submit" value="Comment">
		</div>
	</div>
	</form>
</div>
<?php include_layout_template("footer.php"); ?>