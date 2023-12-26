<?php  date_default_timezone_set('Europe/Kiev');
	
	require_once 'config.php';
	
	if (defined('DB_PORT')) $db = @mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);  
	else $db = @mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE); 
	if (mysqli_connect_errno()) {
		echo 'Unable connect to the database. See config.php';
	}	
	mysqli_select_db($db, DB_DATABASE) or die("Data Base not found");
	mysqli_query($db, "SET NAMES 'utf8'");
	mysqli_query($db, "SET CHARACTER SET utf8");
	mysqli_query($db, "SET CHARACTER_SET_CONNECTION=utf8");	
?>