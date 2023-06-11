<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";
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
    	$query = $con->prepare("SELECT * FROM OverdueLoans");
    	$query->execute();
    	$result = $query->get_result();
    	if(!$result)
    		die("ERROR: Couldn't fetch overdue loans");
    	$rows = mysqli_num_rows($result);
    	if($rows == 0)
    		echo "<h2 align='center'>No overdue loans available</h2>";
    	else
    	{
    		echo "<form class='cd-form' method='POST' action='#'>";
    		echo "<legend>Overdue Loans</legend>";
    		echo "<div class='error-message' id='error-message'>
    				<p id='error'></p>
    			</div>";
    		echo "<table width='100%' cellpadding='10' cellspacing='10'>";
    		echo "<tr>
    				<th>Loan ID<hr></th>
    				<th>Checkout Date<hr></th>
    				<th>Due Date<hr></th>
    				<th>Borrower<hr></th>
    			</tr>";

    		for($i=0; $i<$rows; $i++)
    		{
    			$row = mysqli_fetch_array($result);
    			echo "<tr>";
    			echo "<td>".$row['loan_id']."</td>"; // Loan ID
    			echo "<td>".$row['checkout_date']."</td>"; // Checkout Date
    			echo "<td>".$row['due_date']."</td>"; // Due Date
    			echo "<td>".$row['borrower']."</td>"; // Borrower
    			echo "</tr>";
    		}
    		echo "</table>";
    		echo "</form>";
    	}
    ?>

	</body>
</html>