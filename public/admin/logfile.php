<?php
require_once '../../includes/initialize.php';
if (!$session->is_logged_in()) { redirect_to("login.php");}
refresh();
$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
if (isset($_GET['clear']) && $_GET['clear'] == 'true') {
	file_put_contents($logfile, '');
	log_action('Logs Cleared', "by User ID {$session->user_id}");
	redirect_to('logfile.php');
}
?>
<?php include_layout_template("header_admin.php");?>
<br>
<a href="index.php">&laquo; Back</a><br>
<h2>Log File</h2>
<p><a href="logfile.php?clear=true">Clear log file</a></p>
<?php
	if (file_exists($logfile)&&is_readable($logfile)&&$handle=fopen($logfile, 'r')) {
		echo "<table class=\"table table-hover\">";
		while (!feof($handle)) {
			$entry = fgets($handle);
			if (trim($entry) != "") {
				echo "<tr><td>{$entry}</td></tr>";
			}
		}
		echo "</table>";
		fclose($handle);
	} else {
		echo "Could not read from {$logfile}";
	}
	
?>

<?php include_layout_template("footer_admin.php");?>
