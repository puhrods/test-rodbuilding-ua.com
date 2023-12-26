<?php
	echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
	$error = 0;	
	
	require_once 'config.php';
	
	if (defined('DB_PORT')) $conn = @mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);  
	else $conn = @mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE); 
	if (mysqli_connect_errno()) {
		echo 'Unable connect to the database. See config.php';
	}

	$sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'key`( '.
       'id INT NOT NULL AUTO_INCREMENT, '.
       'value text, '.
       'main_key  VARCHAR(256), '.
       'license_key text, '.       
       'primary key ( id ))';
	
	$retval = $conn->query($sql);
	if(!$retval ) die('Could not create table: ' . mysql_error());
	echo "Table key created successfully<br />";	
	$conn->close();

	if (defined('DB_PORT')) $conn = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	else $conn = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	
	$query = "select * from `" . DB_PREFIX . "key` where `main_key`='local_key'";	
	$retval = $conn->query($query);
	if(!$retval) die('Could not read table: ' . mysql_error());	
	$rows = $retval->fetch_assoc();
	if (empty($rows)) {
		$query = "INSERT INTO `" . DB_PREFIX . "key` SET `value` = '', `main_key` = 'local_key', `license_key` = ''";
		$query_res = $conn->query($query);
		if($query_res) echo " Open new license";
		else echo " Can not write to table 'key'";
	}
	$conn->close();
	if (!$error) echo " MODULE SUCCESSFULLY INSTALLED";
	else echo 'Please, check ' . $error . ' error(s)';
	

?>
