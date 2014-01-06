<?php
require_once '../includes/initialize.php';

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$total_count = Photograph::count_all();

$pagination = new Pagination($page, $per_page, $total_count);
$sql  = "SELECT * FROM photographs ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";
$photos = Photograph::find_by_sql($sql);

?>
<?php include_layout_template("header.php"); ?>

<?php foreach ($photos as $photo): ?>
	<div style="float: left; margin-left: 20px;">
		<a href="photo.php?id=<?php echo $photo->id; ?>">
			<img src="<?php echo $photo->image_path(); ?>" width=200 class="img-polaroid">
		</a>
		<p class="text-success"><?php echo $photo->caption; ?></p>
	</div>
<?php endforeach; ?>
<div class="pagination pagination-large pagination-centered" style="clear: both;">
<?php
if ($pagination->total_pages() > 1) {
	if ($pagination->has_previous_page()) {
		echo "<a href=\"index.php?page=";
		echo $pagination->previous_page();
		echo "\">&laquo; <button class=\"btn btn-mini btn-primary\" type=\"button\">Previous</button></a> ";
	}

	for($i=1; $i<=$pagination->total_pages(); $i++) {
		if ($i == $page) {
			echo "<span class=\"selected\"> <button class=\"btn btn-mini\" type=\"button\">{$i}</button> </span>";
		} else {
			echo "<a href=\"index.php?page={$i}\"><button class=\"btn btn-mini btn-primary\" type=\"button\">{$i}</button></a>";
		}
	}

	if ($pagination->has_next_page()) {
		echo "<a href=\"index.php?page=";
		echo $pagination->next_page();
		echo "\"><button class=\"btn btn-mini btn-primary\" type=\"button\">Next</button>  &raquo;</a> ";
	}
}
?>
</div>
<?php include_layout_template("footer.php"); ?>