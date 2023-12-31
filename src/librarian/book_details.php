<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "header_librarian.php";
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
    	$query = $con->prepare("SELECT * FROM BookDetails ORDER BY title");
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
    		echo "<legend>Book Details</legend>";
    		echo "<div class='error-message' id='error-message'>
    				<p id='error'></p>
    			</div>";
    		echo "<table width='100%' cellpadding='10' cellspacing='10'>";
    		echo "<tr>
    				<th></th>
    				<th>ISBN<hr></th>
    				<th>Title<hr></th>
    				<th>Publication Date<hr></th>
    				<th>Author<hr></th>
    				<th>Publisher<hr></th>
    			</tr>";

    		for($i=0; $i<$rows; $i++)
    		{
    			$row = mysqli_fetch_array($result);
    			echo "<tr>
    					<td>
    						<label class='control control--radio'>
    							<input type='radio' name='rd_book' value=".$row['ISBN']." />
    						<div class='control__indicator'></div>
    					</td>";

    			echo "<td>".$row['ISBN']."</td>"; // ISBN
    			echo "<td>".$row['title']."</td>"; // Title
    			echo "<td>".$row['publication_date']."</td>"; // Publication Date
    			echo "<td>".$row['author']."</td>"; // Author
    			echo "<td>".$row['publisher']."</td>"; // Publisher

    			echo "</tr>";
    		}
    		echo "</table>";
    		echo "</form>";
    	}
    ?>

	</body>
</html>