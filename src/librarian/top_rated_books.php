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
   	$query = $con->prepare("SELECT * FROM TopRatedBooks");
   	$query->execute();
   	$result = $query->get_result();
   	if(!$result)
   		die("ERROR: Couldn't fetch top rated books");
   	$rows = mysqli_num_rows($result);
   	if($rows == 0)
   		echo "<h2 align='center'>No top rated books available</h2>";
   	else
   	{
   		echo "<form class='cd-form' method='POST' action='#'>";
   		echo "<legend>Top Rated Books</legend>";
   		echo "<div class='error-message' id='error-message'>
   				<p id='error'></p>
   			</div>";
   		echo "<table width='100%' cellpadding='10' cellspacing='10'>";
   		echo "<tr>
   				<th>ISBN<hr></th>
   				<th>Title<hr></th>
   				<th>Average Rating<hr></th>
   			</tr>";

   		for($i=0; $i<$rows; $i++)
   		{
   			$row = mysqli_fetch_array($result);
   			echo "<tr>";
   			echo "<td>".$row['ISBN']."</td>"; // ISBN
   			echo "<td>".$row['title']."</td>"; // Title
   			echo "<td>".$row['avg_rating']."</td>"; // Average Rating
   			echo "</tr>";
   		}
   		echo "</table>";
   		echo "</form>";
   	}
   ?>



	</body>
</html>