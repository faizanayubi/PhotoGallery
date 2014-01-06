<?php
	function redirect_to($location) {
		header("Location: {$location}");
		exit;
	}

	function refresh() {
		$page = $_SERVER['PHP_SELF'];
		$sec = "10";
		header("Refresh: $sec; url=$page");
	}

	function get_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		return $ip_address;
	}

	function log_action($action, $message="") {
		$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
		$new = file_exists($logfile) ? false : true;
		if ($handle = fopen($logfile, 'a')) {
			$timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
			$content = "{$timestamp} | {$action} : {$message}\n";
			fwrite($handle, $content);
			fclose($handle);
			if ($new) { chmod($logfile, 0755);}
		} else {
			echo "Could not open log file for writing";
		}
	}

	function strip_zeros_from_date($marked_string="") {
		//first remove mark zeroes
		$no_zeros = str_replace("*0", "", $marked_string);
		//then remove any remaining marks
		$cleaned_string = str_replace("*", "", $no_zeros);
		return $cleaned_string;
	}

	function output_message($message="") {
		if (!empty($message)) {
			return "<div class=\"alert alert-error\"><p class=\"message\">{$message}</p></div>";
		} else {
			return "";
		}
		
	}

	function include_layout_template($template="") {
		include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
	}

	function __autoload($class_name) {
		$class_name = strtolower($class_name);
		$path = LIB_PATH.DS."{$class_name}.php";
		if (file_exists($path)) {
			require_once $path;
		} else {
			die("The file {$class_name}.php could not be found...");
		}
	}

	function datetime_to_text($datetime="") {
		$unixdatetme = strtotime($datetime);
		return strftime("%B %d %Y at %I:%M %p", $unixdatetme);
	}
?>