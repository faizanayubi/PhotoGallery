<?php
require_once('includes/initialize.php');
log_action('Site Visit', "From : ".get_ip());
	redirect_to("public/index.php");

?>