<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../verify_logged_out.php";
	require "../header.php";
	require '../logger.php';
?>

<html>
	<head>
		<title>Member Login</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" type="text/css" href="css/index_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
		
			<legend>Member Login</legend>
			
			<div class="error-message" id="error-message">
				<p id="error"></p>
			</div>
			
			<div class="icon">
				<input class="m-user" type="text" name="m_user" placeholder="Username" required />
			</div>
			
			<div class="icon">
				<input class="m-pass" type="password" name="m_pass" placeholder="Password" required />
			</div>
			
			<input type="submit" value="Login" name="m_login" />
			
			<br /><br /><br /><br />
			
			<p align="center">Don't have an account?&nbsp;<a href="register.php">Sign up</a>
		</form>
	</body>
	
	<?php
		if(isset($_POST['m_login']))
		{
			$query = $con->prepare("SELECT user_id, access_to_system FROM useraccount WHERE username = ? AND password = ?;");
			$username = $_POST['m_user'];
			$password = sha1($_POST['m_pass']);
			$query->bind_param("ss", $username, $password);
			$query->execute();
			$result = $query->get_result();
			
			if(mysqli_num_rows($result) != 1)
				echo error_without_field("Invalid username/password combination");
			else 
			{
				$resultRow = mysqli_fetch_array($result);
				$access_to_system = $resultRow[1];
				if($access_to_system == true){
					$_SESSION['type'] = "useraccount";
					$_SESSION['user_id'] = $resultRow[0];
					$_SESSION['username'] = $_POST['m_user'];
					logActivity('User is login.');
					header('Location: home.php');
				}
				else{
					echo error_without_field("Buraya düzgün bir şey yazalım.");
				}
			}
		}
	?>
	
</html>