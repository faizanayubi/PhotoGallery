<?php
require_once '../../includes/initialize.php';

log_action('logout : ', "user_id - {$session->user_id}.");
$session->logout();
redirect_to("index.php");

?>