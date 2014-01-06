<?php

if(isset($_GET['search'])) {
	//STEP 3 Declair Variables
	$Search = $_GET['search'];
	$Find_Query1 = mysql_query("SELECT * FROM  WHERE url LIKE '%$Search%' ");

	if(!$Find_Query1) {
		die(mysql_error());
	}

	while($row = mysql_fetch_assoc($Find_Query1)) {
		$URL = $row['url'];
		echo "<b><a href="$URL">".$URL."</a></b>";
		echo "<i>$URL</i>";
	}
}
?>