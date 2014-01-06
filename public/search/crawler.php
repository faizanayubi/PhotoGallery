<?php

session_start();

$domain = "ADD DOMAIN NAME HERE";

if(empty($_SESSION['page'])) {
	$original_file = file_get_contents("http://" . $domain . "/");

	$_SESSION['i'] = 0;

	$connect = mysql_connect("HOST","USERNAME","PASSWORD");

	if (!$connect) {
		die("MySQL could not connect!");
	}

	$DB = mysql_select_db('DATABASE NAME');

	if(!$DB) {
		die("MySQL could not select Database!");
	}
}

if(isset($_SESSION['page'])) {
	$connect = mysql_connect("HOST","USERNAME","PASSWORD");

	if (!$connect) {
		die("MySQL could not connect!");
	}

	$DB = mysql_select_db('DATABASE NAME');

	if(!$DB) {
		die("MySQL could not select Database!");
	}
	$PAGE = $_SESSION['page'];
	$original_file = file_get_contents($PAGE);
}

$stripped_file = strip_tags($original_file, "<a>");
preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $stripped_file, $matches);

foreach($matches[1] as $key => $value) {

	if(strpos($value,"http://") != 'FALSE' && strpos($value,"https://") != 'FALSE') {
		$New_URL = "http://" . $domain . $value;
	} else {
		$New_URL = $value;
	}

	$New_URL = addslashes($New_URL);
	$Check = mysql_query("SELECT * FROM pages WHERE url='$New_URL'");
	$Num = mysql_num_rows($Check);

	if($Num == 0) {
		mysql_query("INSERT INTO pages (url)
		VALUES ('$New_URL')");
		$_SESSION['i']++;
		echo $_SESSION['i'] . "";
	}
	echo mysql_error();
}

$RandQuery = mysql_query("SELECT * FROM pages ORDER BY RAND() LIMIT 0,1");
$RandReturn = mysql_num_rows($RandQuery);

while($row1 = mysql_fetch_assoc($RandQuery)) {
	$_SESSION['page'] = $row1['url'];
}
echo $RandReturn;
echo $_SESSION['page'];
mysql_close();
header("refresh: 0;");

?>