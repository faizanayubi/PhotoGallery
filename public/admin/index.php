<?php
require_once '../../includes/initialize.php';
if (!$session->is_logged_in()) { redirect_to("login.php");}
?>
<?php include_layout_template("header_admin.php");?>

	<h2>Menu</h2>
	<?php echo output_message($message); ?>
	<ul>
		<li><a href="list_photos.php">List Photos</a></li>
		<li><a href="logfile.php">View Log file</a></li>
		<li><a href="master/static/index.html">Monitor the World</a></li>
		<li><a href="logout.php">Log Out</a></li>
	</ul>

<?php include_layout_template("footer_admin.php");?>