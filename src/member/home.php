<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";
	require '../logger.php';
?>

<html>
	<head>
		<title>Welcome</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="css/home_style.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_radio_button_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM book ORDER BY title");
			$query->execute();
			$result = $query->get_result();
			if(!$result)
				die("ERROR: Couldn't fetch books");
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>No books available</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<legend>Books</legend>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>";
				echo "<tr>
						<th></th>
						<th>ISBN<hr></th>
						<th>Title<hr></th>
						<th>Author<hr></th>
						<th>Genre<hr></th>
						<th>Number Pages<hr></th>
						<th>Status<hr></th>
					</tr>";

				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>
							<td>
								<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$row[0]." />
								<div class='control__indicator'></div>
							</td>";

					echo "<td>".$row[0]."</td>"; // ISBN
					echo "<td>".$row[1]."</td>"; // Title

					$queryAuthor = $con->prepare("SELECT author_id, name FROM author");
					$queryAuthor->execute();
					$resultAuthor = $queryAuthor->get_result();
					$authorRows = mysqli_num_rows($resultAuthor);
					
					for($x=0; $x<$authorRows; $x++){
						$author = mysqli_fetch_array($resultAuthor);
						if($author[0] === $row[4]){
							echo "<td>".$author[1]."</td>"; // Author
						}
					}

					echo "<td>".$row[6]."</td>"; // Genre
					echo "<td>".$row[3]."</td>"; // Number Pages

					$queryCopy = $con->prepare("SELECT ISBN, status FROM bookcopy");
					$queryCopy->execute();
					$resultCopy = $queryCopy->get_result();
					$copyRows = mysqli_num_rows($resultCopy);
					
					for($y=0; $y<$copyRows; $y++){
						$copy = mysqli_fetch_array($resultCopy);
						if($copy[0] === $row[0]){
							echo "<td>".$copy[1]."</td>"; // Status
						}
					}

					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><input type='submit' name='m_request' value='Request book' />";
				echo "</form>";
			}
			
			if(isset($_POST['m_request']))
			{
				if(empty($_POST['rd_book']))
					echo error_without_field("Please select a book to issue");
				else
				{
					$query = $con->prepare("SELECT copy_id, status FROM bookcopy WHERE isbn = ?;");
					$query->bind_param("s", $_POST['rd_book']);
					$query->execute();
					$result = $query->get_result();
					$row = $result->fetch_assoc();
					$copy_id = $row['copy_id'];
					$status = $row['status'];

					if($status == "Checked Out")
						echo error_without_field("No copies of the selected book are available.");
					else if($status == "On Hold")
						echo error_without_field("This book is waiting someone else.");
					else if($status == "Available")
					{
						$query = $con->prepare("SELECT us.username, lo.copy_id FROM loan AS lo JOIN borrower AS bo ON lo.borrower_id=bo.borrower_id JOIN useraccount AS us ON bo.user_id = us.user_id WHERE us.username = ? AND lo.return_date IS NULL;");
						$query->bind_param("s", $_SESSION['username']);
						$query->execute();
						$result = $query->get_result();
						$rowLoan = mysqli_num_rows($result);
						$continue = "pass";
						if($rowLoan >= 3){
							echo error_without_field("You already have 3 reservation.");
						}
						for($i=0; $i<$rowLoan; $i++){
							$currentCopyId = mysqli_fetch_array($result)[1];
							echo "$currentCopyId";
							if($currentCopyId == $copy_id){
								echo error_without_field("You already have this book.");
								$continue = "error";
							}
						}
						if($continue == "pass")
						{
							
							$currentDate = date("Y-m-d");
							$query = $con->prepare("SELECT bo.borrower_id FROM useraccount AS us JOIN borrower AS bo ON bo.user_id=us.user_id WHERE us.username = ?;");
							$query->bind_param("s", $_SESSION['username']);
							$query->execute();
							$result = $query->get_result();
							$borrower_id = mysqli_fetch_array($result)[0];

							$query = $con->prepare("CALL InsertReservation(?,?,?);");
							$query->bind_param("sss", $borrower_id, $_POST['rd_book'], $currentDate);
							if(!$query->execute())
								echo error_without_field("ERROR: Couldn\'t request book");
							else
								$query = $con->prepare("CALL InsertLoan(?,?,?,?,?);");
								$futureDate = date("Y-m-d", strtotime($currentDate . "+15 days"));
								$null = null;
								$query->bind_param("iisss", $borrower_id, $copy_id, $currentDate, $futureDate, $null);
								$query->execute();
								logActivity('User is request book.');
								echo success("Book successfully requested.");
						}
					}
				}
			}
		?>
	</body>
</html>