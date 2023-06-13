<?php
	require "../db_connect.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Home</title>
		<link rel="stylesheet" type="text/css" href="css/home_style.css" />
	</head>
	<body>
		<div id="allTheThings">
			<a href="pending_registrations.php">
				<input type="button" value="Pending registrations" />
			</a><br />
			<a href="insert_book.php">
				<input type="button" value="Add a new book" />
			</a><br />
			<a href="update_copies.php">
				<input type="button" value="Update copies of a book" />
			</a><br />
		</div>
	</body>
</html>