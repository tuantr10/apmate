<?php
session_start();
$username='';
$_SESSION['ses_username']='';
$_SESSION['ses_level']='';
$_SESSION['ses_userid']='';
$_SESSION['ses_subject_code']='0';
//Log in button clicked
if(isset($_POST['ok'])) {
  $u=$p='';
  if($_POST['username'] == NULL) {
    echo "Please enter username<br />";
  } else {
    $u=$_POST['username'];
  }

  if($_POST['password'] == NULL) {
    echo "Please enter password<br />";
  } else {
    $p=$_POST['password'];
  }

  if($u && $p) {
    require('connect.php');
    $sql="select * from users where user_name='".$u."' and user_password='".$p."'";
    $query=mysql_query($sql);
    if(mysql_num_rows($query) == 0) {
      echo "Wrong User ID or password, please re-type again";
    } else {
      $data=mysql_fetch_assoc($query);
      $_SESSION['ses_username']=$data['user_name'];
      $_SESSION['ses_level']=$data['user_level'];
      $_SESSION['ses_userid']=$data['user_id'];
      if ($data['user_level']=="2") { //If admin
        header("location:register.php");
      } else { // if not admin
        header("location:timetableadvance.php");
      }
      exit();
    }
  }
}

if(isset($_POST['register'])) { //Register button clicked
  header("location:register.php");
}
?>


<style type="text/css">
.Title_ { font-family: Verdana, Geneva, sans-serif; }
.Title_ strong { font-family: Georgia, Times New Roman, Times, serif; }
</style>

<p align="center" class="Title_"><img src="APU_logo.JPG" width="136" height="44" align="left" /></p>
<p align="left" class="Title_">&nbsp;</p>
<p align="left" class="Title_">	<strong>Course Registration Simulation</strong></p>
<p align="center" class="Title_">&nbsp;</p>
<p align="center" class="Title_">&nbsp;</p>
<p align="center" class="Title_">&nbsp;</p>

<form action="database.php" method="post"><p align="center"><strong>Username:&nbsp;</strong>
  <input type="text" name="username" size="25" />
</p>
<p align="center">
  <strong>&nbsp;Password:&nbsp;</strong>
  <input type="password" name="password" size="25" />
</p>
<div align="center">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <input type="submit" name="ok" value="Log in" />
      &nbsp;&nbsp;<input type="submit" name="register" value="Register" />
</div>
</form >
<br />