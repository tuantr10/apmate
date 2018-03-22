<?php
include("settings.php");
$conn = mysqli_connect(constant('HOST'), constant('USERNAME'), constant('PASSWORD'), constant('DBNAME'));

/* check connection */
if (mysqli_connect_errno()) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
}

if (!mysqli_set_charset($conn, "utf8")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($conn));
    exit();
}
?>