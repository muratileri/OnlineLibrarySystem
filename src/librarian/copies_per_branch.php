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
        $query = $con->prepare("SELECT * FROM CopiesPerBranch");
        $query->execute();
        $result = $query->get_result();
        if(!$result)
            die("ERROR: Couldn't fetch copies per branch");
        $rows = mysqli_num_rows($result);
        if($rows == 0)
            echo "<h2 align='center'>No copies available</h2>";
        else
        {
            echo "<form class='cd-form' method='POST' action='#'>";
            echo "<legend>Copies Per Branch</legend>";
            echo "<div class='error-message' id='error-message'>
                    <p id='error'></p>
                </div>";
            echo "<table width='100%' cellpadding='10' cellspacing='10'>";
            echo "<tr>
                    <th>Branch ID<hr></th>
                    <th>Total Copies<hr></th>
                </tr>";

            for($i=0; $i<$rows; $i++)
            {
                $row = mysqli_fetch_array($result);
                echo "<tr>";
                echo "<td>".$row['branch_id']."</td>"; // Branch ID
                echo "<td>".$row['total_copies']."</td>"; // Total Copies
                echo "</tr>";
            }
            echo "</table>";
            echo "</form>";
        }
    ?>


	</body>
</html>