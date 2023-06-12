<html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,700">
		<link rel="stylesheet" type="text/css" href="css/header_librarian_style.css" />
	</head>
	<body>
		<header>
			<div id="cd-logo">
				<a href="../">
					<img src="img/ic_logo.svg" alt="Logo" />
					<p>LIBRARY</p>
				</a>
			</div>
			
			<div class="dropdown">
				<button class="dropbtn">
					<p id="librarian-name"><?php echo $_SESSION['username'] ?></p>
				</button>
				<div class="dropdown-content">
					<a href=".././librarian/insert_book.php">Insert Book</a>
                    <a href=".././librarian/book_details.php">Book Details</a>
                    <a href=".././librarian/copies_per_branch.php">Copies Per Branch</a>
                    <a href=".././librarian/top_rated_books.php">Top Rated Books</a>
                    <a href=".././librarian/borrower_total_fine.php">Borrower Total Fine</a>
                    <a href=".././librarian/overdue_loans.php">Overdue Loans</a>
                    <a href=".././librarian/stats.php">Stats</a>
                    <a href="../logout.php">Logout</a>
				</div>
			</div>
		</header>
	</body>
</html>