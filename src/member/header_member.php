<html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,700">
		<link rel="stylesheet" type="text/css" href="css/header_member_style.css" />
	</head>
	<body>
		<header>
			<div id="cd-logo">
				<a href="../member/home.php">
					<img src="img/ic_logo.svg" alt="Logo" />
					<p>LIBRARY</p>
				</a>
			</div>
			
			<div class="dropdown">
				<?php
				// Retrieve the image data from the database based on the logged-in user's information
				$query = $con->prepare("SELECT profile_image FROM useraccount WHERE username = ?");
				$username = $_SESSION['username'];
				$query->bind_param("s", $username);
				$query->execute();
				$result = $query->get_result();

				if ($result->num_rows > 0) {
					// Fetch the row containing the image data
					$row = $result->fetch_assoc();
					
					// Assign the image data to the $imgContent variable
					$imgContent = $row['profile_image'];
				}
				?>

				<button class="dropbtn">
				<?php
					// Check if the profile image exists
					if ($imgContent !== null) {
					echo '<img src="data:image/jpeg;base64,' . base64_encode($imgContent) . '" alt="Profile Image" width="24" height="24">';
					}
				?>
				<p id="librarian-name"><?php echo $_SESSION['username'] ?></p>
				</button>


				<div class="dropdown-content">
					
				<a href="my_books.php">My books</a>
                <a href="../logout.php">Logout</a>


				</div>
			</div>
		</header>
	</body>
</html>