<?php
session_start();
echo $_SESSION['ses_username']."<br />";
//if ($_SESSION['ses_username']=="admin")
//{
  //echo "Welcome ".$_SESSION['ses_username']."<br />";

  if (isset($_POST['register'])) {
    $l=$u=$p=$rp="";
    $l=$_POST['level'];

    if ($_POST['username']==NULL) {
      echo "Please enter username</br>";
    } else {
      $u=$_POST['username'];
    }

    if ($_POST['password']==NULL) {
      echo "Please enter password</br>";
    } else {
      $p=$_POST['password'];
    }

    if ($_POST['re-password'] == NULL) {
      echo "Please enter re-password</br>";
    } else {
      $rp=$_POST['re-password'];
    }

    if ($u & $p & $rp) {
      require("connect.php");
      $sql="select * from users where User_name='".$u."'"; 
      $query=mysql_query($sql);
      if(mysql_num_rows($query)!=0) {
        echo "Username has been registered! Please enter another username!";
      } else {
        if($p!=$rp) {
          echo "Password and Re-Password is not the same! Please try again!";
        } else {
          $add_user=" INSERT INTO users(user_name,user_password,user_level) 
                VALUES('".$u."','".md5($p)."','1')";  
        /*Add workshop here*/
          mysql_query($add_user);
          $new_id="";
          $sql_get_new_id="SELECT user_id 
                   FROM users
                   WHERE user_name='".$u."'";
          $query_get_new_id=mysql_query($sql_get_new_id);
          while($row_get_new_id=mysql_fetch_assoc($query_get_new_id)) {
            $new_id=$row_get_new_id['user_id'];
          }
          ?>
          <script>alert('Sucessfully registered! \nRedirecting to homepage');
          window.location.assign("../");
          </script>
          <?
        header("location:index.php");
        }
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APMate - Registration</title>

    <!-- CSS -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body>
  <?php include_once("analyticstracking.php") ?>
    <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '835251209954572',
        xfbml      : true,
        version    : 'v2.5'
      });
    };

    (function(d, s, id){
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
    <!-- Top content -->
    <div class="top-content">
      <div class="inner-bg">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-sm-offset-2 text">
              <h1><strong>Registration</strong> page</h1>
              <div class="description">
                <p>
                  <strong>This is where you create your account. <br />
                  </strong>
                </p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 col-sm-offset-3 form-box">
              <div class="form-top">
                <div class="form-top-left">
                  <h3>Registration</h3>
                  <p>Enter your desired account information</p>
                </div>
                <div class="form-top-right">
                  <i class="fa fa-lock"></i>
                </div>
              </div>
              <div class="form-bottom">
                <form role="form" action="" method="post" class="login-form">
                  <div class="form-group">
                    <label class="sr-only" for="form-username">Username</label>
                    <input type="text" name="username" placeholder="Username..." class="form-username form-control" id="username">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="form-password">Password</label>
                    <input type="password" name="password" placeholder="Password..." class="form-password form-control" id="password">
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="form-password">Retype Password</label>
                    <input type="password" name="re-password" placeholder="Retype password..." class="form-password form-control" id="password">
                  </div>
                  <button type="submit" name="register" class="btn">Register</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>


    <!-- Javascript -->
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.backstretch.min.js"></script>
    <script src="bower_components/js-cookie/src/js.cookie.js"></script>
    <script src="assets/js/scripts.js"></script>
    
    <!--[if lt IE 10]>
      <script src="assets/js/placeholder.js"></script>
    <![endif]-->

  </body>

</html>  
<?
//} 
//else
//{
//  echo "Hacking?";
//}


?>