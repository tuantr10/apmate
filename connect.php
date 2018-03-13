<?php
include("settings.php");
$conn = mysql_connect(constant('HOST'), constant('USERNAME'), constant('PASSWORD')) or die (mysql_error());
mysql_set_charset('utf8', $conn);
mysql_select_db(constant('DBNAME'), $conn) or die (mysql_error());
?>