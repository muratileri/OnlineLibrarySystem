<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../header.php";
	require '../logger.php';
?>

<html>
	<head>
		<title>Register</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" href="css/register_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<legend>Enter your details</legend>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="m-user" type="text" name="m_user" id="m_user" placeholder="Username" required />
				</div>
				
				<div class="icon">
					<input class="m-pass" type="password" name="m_pass" placeholder="Password" required />
				</div>
				
				<div class="icon">
					<input class="m-email" type="email" name="m_email" id="m_email" placeholder="Email" required />
				</div>
				
				<div class="icon">
					<input class="m-address" type="text" name="m_address" id="m_address" placeholder="Address" required />
				</div>
				
				<div class="icon">
					<input class="m-phone" type="text" name="m_phone" id="m_phone" placeholder="Phone Number" required />
				</div>
				
				<div class="icon">
					<select class="m-type" name="m_type" id="m_type" required>
						<option value="">Select Borrower Type</option>
						<option value="Student">Student</option>
						<option value="Faculty">Faculty</option>
						<option value="Staff">Staff</option>
						<option value="Community">Community</option>
					</select>
				</div>

				<br />
				<input type="submit" name="m_register" value="Register" />
		</form>
	</body>
	
	<?php
		if(isset($_POST['m_register']))
		{
			$query = $con->prepare("(SELECT username FROM useraccount WHERE username = ?);");
			$query->bind_param("s", $_POST['m_user']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 0)
				echo error_with_field("The username you entered is already taken", "m_user");
			else
			{
				$query = $con->prepare("(SELECT email FROM useraccount WHERE email = ?);");
				$query->bind_param("s", $_POST['m_email']);
				$query->execute();
				if(mysqli_num_rows($query->get_result()) != 0)
					echo error_with_field("An account is already registered with that email", "m_email");
				else
				{
					$query = $con->prepare("CALL InsertUserAccount(?, ?, ?, ?, ?);");
					$username = $_POST['m_user'];
					$password = sha1($_POST['m_pass']);
					$email = $_POST['m_email'];
					$address = $_POST['m_address'];
					$phone = $_POST['m_phone'];
					$borrower_type = $_POST['m_type'];
					
					$query->bind_param("sssss", $username, $password, $email, $address, $phone);

					if($query->execute()){
						$con->commit();
						$query = $con->prepare("SELECT user_id FROM useraccount WHERE username = ?;");
						$query->bind_param("s", $_POST['m_user']);
						$query->execute();
						$result = $query->get_result();
						$new_user_id = mysqli_fetch_array($result)[0];

						$query = $con->prepare("CALL InsertBorrower(?, ?);");
						$borrower_type = $_POST['m_type'];
						$query->bind_param("is", $new_user_id, $borrower_type);
						$query->execute();

						logActivity('User is registered.');

						echo success("Buraya işte onaylanmanız lazımdır tarzı bir şey.");
						
					}
					else
						echo error_without_field("Couldn\'t record details. Please try again later");
				}
			}

		}
	?>
	
</html>