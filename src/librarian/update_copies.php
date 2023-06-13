<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Update copies</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/update_copies_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<legend>Enter The Details</legend>

			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" type='text' name='b_isbn' id="b_isbn" placeholder=" Book ISBN" required />
				</div>
					
				<div class="icon">
					<select class="b-copies" name="b_copies" id="b_copies" required>
						<option value="">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Select Status</option>
						<option value="Available">Available</option>
						<option value="On Hold">On Hold</option>
						<option value="Checked Out">Checked Out</option>
					</select>
				</div>
						
				<input type="submit" name="b_add" value="Update Copies" />
		</form>
	</body>
	
	<?php
		if(isset($_POST['b_add']))
		{
			$query = $con->prepare("SELECT isbn FROM book WHERE isbn = ?;");
			$query->bind_param("s", $_POST['b_isbn']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_with_field("Invalid ISBN", "b_isbn");
			else
			{
				$query = $con->prepare("SELECT * FROM bookcopy WHERE isbn = ?;");
				$query->bind_param("s", $_POST['b_isbn']);
				$query->execute();
				$result = $query->get_result();
				$row = mysqli_fetch_array($result);

				$query = $con->prepare("CALL updateBookCopy(?, ?, ?, ?);");
				$query->bind_param("isis", $row[0], $_POST['b_isbn'], $row[2], $_POST['b_copies']);
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn\'t add copies"));
				echo success("Copies successfully updated");
			}
		}
	?>
</html>