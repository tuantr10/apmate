<?php
include("settings.php");
$conn = mysqli_connect(constant('HOST'), constant('USERNAME'), constant('PASSWORD'), constant('DBNAME'));
/* check connection */
if (mysqli_connect_errno()) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
}
?>