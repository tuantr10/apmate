<?php
session_start();
$username="";
$_SESSION['ses_username']="";
$_SESSION['ses_level']="";
$_SESSION['ses_userid']="";
$_SESSION['ses_announce']=0;
$_SESSION['ses_subject_code']="0";
//Log in button clicked
if(isset($_POST['ok'])) {
	$u=$p="";
	if(($_POST['username'] == NULL)||($_POST['password'] == NULL)) {
		?>
		<script>alert("Please enter both username and password");</script>;
		<?
	} else {
		$u=$_POST['username'];
		$p=$_POST['password'];
	}

	if($u && $p) {
		require("connect.php");
		$sql="select * from users where user_name='".$u."' and user_password='".md5($p)."'"; 
		$query=mysql_query($sql);
		if(mysql_num_rows($query) == 0) {
			?>
			<script>alert("Wrong User ID or password, please try again. \n If you don't have an account, please create one.");</script>
			<?
		} else {
			$data=mysql_fetch_assoc($query);
			$_SESSION['ses_username']=$data['user_name'];
			$_SESSION['ses_level']=$data['user_level'];
			$_SESSION['ses_userid']=$data['user_id'];
			if ($data['user_level']=="2") {
			//If user == admin
			header("location:register.php");
			} else {
			//If user != admin
				header("location:timetableadvance.php");
			}
			exit();
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
		<title>APMate - Course Registration Simulation</title>

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
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5&appId=835251209954572";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
		<!-- Top content -->
		<div class="top-content">
			<div class="inner-bg">
				<div class="container">
					<div class="row">
						<div class="col-sm-8 col-sm-offset-2 text">
							<h1><strong>APU</strong> Course Registration Simulation</h1>
							<div class="description">
								<p>
									<strong>This is a simulation site where students can arrange their schedule before the legend 'clicking' war. <br />
									The schedule in this site is based on the excel file that can be downloaded here <a href="http://en.apu.ac.jp/academic/uploads/fckeditor/public/registration/17SPTimetable_0317u.xlsx">http://en.apu.ac.jp..</a>.</strong>
								</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3 form-box">
							<div class="text">
								<h1 class="description">Updated for SPRING 2017!</h1>
							</div>
						</div>
						<div class="col-sm-6 col-sm-offset-3 form-box">
							<div class="form-top" style="align: center">
								<br />
								<div class="fb-page" data-href="https://www.facebook.com/APMate-207060032988344/" data-width="500" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>	
 							</div>
							<div class="form-bottom">
								<form action="index.php" method="post" class="login-form">
								<div class="form-top-left">
									<h3>Login to our site</h3>
									<p>Enter your username and password to log in:</p>
								</div>
								<div class="form-top-right">
									<i class="fa fa-mortar-board"></i>
								</div>
									<div class="form-group">
										<label class="sr-only" for="username">Username</label>
										<input type="text" name="username" placeholder="Username..." class="form-username form-control" id="username">
									</div>
									<div class="form-group">
										<label class="sr-only" for="password">Password</label>
										<input type="password" name="password" placeholder="Password..." class="form-password form-control" id="password">
									</div>
									<strong>Don't have an account? Click <a href="register.php">here</a>.</strong><br/>
									<strong>アカウントの作成は<a href="register.php">こちら</a>から。</strong>
									<button type="submit" name="ok" class="btn">Sign in!</button>
									<div class="fb-like" data-share="true" data-width="450" data-show-faces="true"></div>
									<div id="radio">
										<input type="radio" id="en" name="language" value="en" />
										<label for="en">English <img src="image/lang_en.png" alt="lang_en"></label>
										<input type="radio" id="ja" name="language" value="ja" />
										<label for="ja">日本語<img src="image/lang_ja.png" alt="lang_ja"></label>
									</div>
								</form>
							</div>
						</div>
					</div>
					<!-- will be implemented -->
					<!--<div class="row">
						<div class="col-sm-6 col-sm-offset-3 social-login">
							<h3>...or login with:</h3>
							<div class="social-login-buttons">
								<a class="btn btn-link-1 btn-link-1-facebook" href="#">
									<i class="fa fa-facebook"></i> Facebook
								</a>
								<a class="btn btn-link-1 btn-link-1-twitter" href="#">
									<i class="fa fa-twitter"></i> Twitter
								</a>
								<a class="btn btn-link-1 btn-link-1-google-plus" href="#">
									<i class="fa fa-google-plus"></i> Google Plus
								</a>
							</div>
						</div>
					</div>-->
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
		<footer>If you have any feedbacks or comments please contact us at our fb page ↑<br/>
		Best use with Google Chrome.</footer>
	</body>

</html>