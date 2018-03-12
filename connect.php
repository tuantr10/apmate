<?php include("settings.php"); ?>
<?php	$conn=mysql_connect(HOST, USERNAME, PASSWORD) or die (mysql_error());
		mysql_set_charset('utf8', $conn);
		mysql_select_db(DBNAME, $conn) or die (mysql_error());
?>