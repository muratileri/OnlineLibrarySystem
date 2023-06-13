<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Pending Registrations</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_registrations_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM useraccount where access_to_system = 0");
			$query->execute();
			$result = $query->get_result();
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>No registrations pending</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<legend>Pending Registrations</legend>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Username<hr></th>
							<th>Email<hr></th>
							<th>Address<hr></th>
							<th>Phone Number<hr></th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					if($row[0] > 10){
						
						echo "<tr>";
						echo "<td>
								<label class='control control--checkbox'>
									<input type='checkbox' name='cb_".$i."' value='".$row[0]."' />
									<div class='control__indicator'></div>
								</label>
							</td>";
						$j;
						for($j=1; $j<6; $j++){
							if($j !== 2){
								echo "<td>".$row[$j]."</td>";
							}
						}
					}
					
						
				}
				echo "</table><br /><br />";
				echo "<div style='float: right;'>";
				echo "<input type='submit' value='Delete Selected' name='l_delete' />&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Confirm Selected' name='l_confirm' />";
				echo "</div>";
				echo "</form>";
			}
			
			if(isset($_POST['l_confirm']))
			{
				$members = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$userid =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT * FROM useraccount WHERE user_id = ?;");
						$query->bind_param("s", $userid);
						$query->execute();
						$row = mysqli_fetch_array($query->get_result());
						
						$query = $con->prepare("CALL UpdateUserAccount(?, ?, ?, ?, ?, ?, ?, ?);");
						if(!$query->execute([$userid, $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], true]))
							die(error_without_field("ERROR: Couldn\'t insert values"));
						$members++;
						
					}
				}
				if($members > 0)
					echo success("Successfully added ".$members." members");
				else
					echo error_without_field("No registration selected");
			}
			
			if(isset($_POST['l_delete']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$userid =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT borrower_id FROM borrower WHERE user_id = ?;");
						$query->bind_param("s", $userid);
						$query->execute();
						$borrowerId = mysqli_fetch_array($query->get_result())[0];

						$query = $con->prepare("CALL DeleteBorrower(?);");
						$query->bind_param("s", $borrowerId);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						
						$query = $con->prepare("CALL DeleteUserAccount(?);");
						$query->bind_param("s", $userid);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						$requests++;
					}
				}
				if($requests > 0)
					echo success("Successfully deleted ".$requests." requests");
				else
					echo error_without_field("No registration selected");
			}
		?>
	</body>
</html>