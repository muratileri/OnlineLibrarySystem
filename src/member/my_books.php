<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";
?>

<html>
	<head>
		<title>My books</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/my_books_style.css">
	</head>
	<body>
	
		<?php
			$query = $con->prepare("SELECT lo.loan_id, lo.copy_id, bo.borrower_id FROM loan AS lo JOIN borrower AS bo ON lo.borrower_id=bo.borrower_id JOIN useraccount AS us ON bo.user_id = us.user_id WHERE us.username = ? AND lo.return_date IS NULL;");
			$query->bind_param("s", $_SESSION['username']);
			$query->execute();
			$result = $query->get_result();
			$rows = mysqli_num_rows($result);

			$queryFine = $con->prepare("SELECT fine.fine_amount FROM fine JOIN loan ON loan.loan_id=fine.loan_id JOIN borrower AS bo ON bo.borrower_id=loan.borrower_id JOIN useraccount AS uc ON bo.user_id=uc.user_id WHERE uc.username = ?;");
			$queryFine->bind_param("s", $_SESSION['username']);
			$queryFine->execute();
			$resultFine = $queryFine->get_result();
			$rowsFine = mysqli_num_rows($resultFine);
			if($rowsFine != 0){
				$totalAmount = 0;
				$fetchArray =  mysqli_fetch_array($resultFine);
				for($i=0; $i<$rowsFine; $i++){
					$totalAmount += $fetchArray[0];
				}
				
				echo "<h3 align='center'>You have $totalAmount TL fine. Please go library and pay fine as soon as possible.</h3>";
			}
			
			
			if($rows == 0)
				echo "<h2 align='center'>No books currently issued</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<legend>My books</legend>";
				echo "<div class='success-message' id='success-message'>
						<p id='success'></p>
					</div>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo"<table width='100%' cellpadding='10' cellspacing='10'>
						<tr>
							<th></th>
							<th>ISBN<hr></th>
							<th>Title<hr></th>
							<th>Genre<hr></th>
							<th>Author<hr></th>
							<th>Due Date<hr></th>
						</tr>";
				
				
				for($i=0; $i<$rows; $i++)
				{
					$fetchArray =  mysqli_fetch_array($result);
					$borrower_id = $fetchArray[2];
					$copy_id = $fetchArray[1];
					$loan_id = $fetchArray[0];
					if($copy_id != NULL)
					{
						$queryBook = $con->prepare("SELECT book.ISBN, book.title, book.genre_name, book.author_id FROM book JOIN bookcopy AS bo ON bo.copy_id = ? WHERE bo.ISBN = book.ISBN;");
						$queryBook->bind_param("s", $copy_id);
						$queryBook->execute();
						$innerRow = mysqli_fetch_array($queryBook->get_result());
						echo "<tr>
								<td>
									<label class='control control--checkbox'>
										<input type='checkbox' name='cb_book".$i."' value='".$loan_id."'>
										<div class='control__indicator'></div>
									</label>
								</td>";
						echo "<td>".$innerRow[0]."</td>";
						for($j=1; $j<3; $j++)
							echo "<td>".$innerRow[$j]."</td>";

						$queryAuthor = $con->prepare("SELECT author.author_id, author.name FROM author JOIN book ON author.author_id = ?;");
						$queryAuthor->bind_param("s", $innerRow[3]);
						$queryAuthor->execute();
						$resultAuthor = $queryAuthor->get_result();
						$rowAuthor = $resultAuthor->fetch_assoc();
						$author_name = $rowAuthor['name'];
						echo "<td>".$author_name."</td>";

						$queryDue = $con->prepare("SELECT due_date FROM loan WHERE borrower_id = ? AND copy_id = ? AND return_date IS NULL;");
						$queryDue->bind_param("ss", $borrower_id, $copy_id);
						$queryDue->execute();
						echo "<td>".mysqli_fetch_array($queryDue->get_result())[0]."</td>";
						echo "</tr>";
					}
				}
				echo "</table><br />";
				echo "<input type='submit' name='b_return' value='Return selected books' />";
				echo "</form>";
			}
			
			if(isset($_POST['b_return']))
			{
				$books = 0;
				for($i=0; $i<$rows; $i++)
					if(isset($_POST['cb_book'.$i]))
					{
						$query = $con->prepare("SELECT * FROM loan WHERE loan_id = ?;");
						$query->bind_param("s", $_POST['cb_book'.$i]);
						$query->execute();
						$result = $query->get_result();
						$loanTable = $result->fetch_assoc();
						$loan_id = $loanTable['loan_id'];
						$borrower_id = $loanTable['borrower_id'];
						$copy_id = $loanTable['copy_id'];
						$checkout_date = $loanTable['checkout_date'];
						$due_date = $loanTable['due_date'];
						$current_date = date("Y-m-d");
						
						$query = $con->prepare("SELECT DATEDIFF(CURRENT_DATE, ?);");
						$query->bind_param("s", $due_date);
						$query->execute();
						$days = (int)mysqli_fetch_array($query->get_result())[0];
						
						$query = $con->prepare("CALL UpdateLoan(?,?,?,?,?,?);");
						$query->bind_param("iiisss", $_POST['cb_book'.$i], $borrower_id, $copy_id, $checkout_date, $due_date, $current_date);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t return the books"));
						
						if($days > 0)
						{
							$penalty = 5*$days;
							$query = $con->prepare("CALL InsertFine(?,?,?,?);");
							$reason_penalty = "Overdue";
							$query->bind_param("isss", $loan_id, $penalty, $reason_penalty, $current_date);
							$query->execute();

							$queryTitle = $con->prepare("SELECT bo.title FROM bookcopy AS bc JOIN book AS bo ON bo.ISBN = bc.ISBN WHERE bc.copy_id = ?;");
							$queryTitle->bind_param("i", $copy_id);
							$queryTitle->execute();
							$titleName = mysqli_fetch_array($queryTitle->get_result())[0];
							echo '<script>
									document.getElementById("error").innerHTML += "A penalty of. '.$penalty.'TL was charged for keeping book '.$titleName.' for '.$days.' days after the due date.<br />";
									document.getElementById("error-message").style.display = "block";
								</script>';
						}
						$books++;
					}
				if($books > 0)
				{
					header("Refresh:1");
					echo '<script>
							document.getElementById("success").innerHTML = "Successfully returned '.$books.' books";
							document.getElementById("success-message").style.display = "block";
						</script>';
				}
				else
					echo error_without_field("Please select a book to return");
			}
		?>
		
	</body>
</html>