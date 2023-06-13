<?php
require "../db_connect.php";
require "../message_display.php";
require "verify_librarian.php";
require "header_librarian.php";
?>

<html>
<head>
	<title>Add Book</title>
	<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
	<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
	<link rel="stylesheet" href="css/insert_book_style.css">
</head>
<body>
	<form class="cd-form" method="POST" action="#">
		<div class="baslik">Enter Book Details</div>

		<div class="error-message" id="error-message">
			<p id="error"></p>
		</div>

		<div class="icon">
			<input class="b-isbn" id="b_isbn" type="number" name="b_isbn" placeholder="ISBN" required />
		</div>

		<div class="icon">
			<input class="b-title" type="text" name="b_title" placeholder="Title" required />
		</div>

		<div class="icon">
			<input class="b-publication-date" type="date" name="b_publication_date" placeholder="Publication Date" required />
		</div>

		<div class="icon">
			<input class="b-num-pages" type="number" name="b_num_pages" placeholder="Number of Pages" required />
		</div>

		<div class="icon">
			<input class="b-author" type="number" name="b_author_id" placeholder="Author ID" required />
		</div>

		<div class="icon">
			<input class="b-subject" type="text" name="b_subject_name" placeholder="Subject Name" required />
		</div>

		<div class="icon">
			<input class="b-genre" type="text" name="b_genre_name" placeholder="Genre Name" required />
		</div>

		<div class="icon">
			<input class="b-language" type="text" name="b_language_name" placeholder="Language Name" required />
		</div>

		<div class="icon">
			<input class="b-series" type="text" name="b_series_name" placeholder="Series Name" required />
		</div>

		<div class="icon">
			<input class="b-format" type="text" name="b_format_name" placeholder="Format Name" required />
		</div>

		<div class="icon">
			<input class="b-publisher" type="number" name="b_publisher_id" placeholder="Publisher ID" required />
		</div>

		<div class="icon">
			<input class="b-rating" type="number" name="b_rating_id" placeholder="Rating ID" required />
		</div>

		<div class="icon">
			<input class="b-review" type="number" name="b_review_id" placeholder="Review ID" required />
		</div>

		<div class="icon">
			<input class="b-edition" type="text" name="b_edition" placeholder="Edition" required />
		</div>

		<br />
		<input class="b-isbn" type="submit" name="b_add" value="Add Book" />
	</form>

	<?php
	if (isset($_POST['b_add'])) {
		$query = $con->prepare("SELECT ISBN FROM book WHERE ISBN = ?;");
		$query->bind_param("s", $_POST['b_isbn']);
		$query->execute();

		if (mysqli_num_rows($query->get_result()) != 0)
			echo error_with_field("A book with that ISBN already exists", "b_isbn");
		else {
			$query = $con->prepare("INSERT INTO book (ISBN, title, publication_date, num_pages, author_id, subject_name, genre_name, language_name, series_name, format_name, publisher_id, rating_id, review_id, edition) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
			$query->bind_param(
				"ssiiisssssiiis",
				$_POST['b_isbn'],
				$_POST['b_title'],
				$_POST['b_publication_date'],
				$_POST['b_num_pages'],
				$_POST['b_author_id'],
				$_POST['b_subject_name'],
				$_POST['b_genre_name'],
				$_POST['b_language_name'],
				$_POST['b_series_name'],
				$_POST['b_format_name'],
				$_POST['b_publisher_id'],
				$_POST['b_rating_id'],
				$_POST['b_review_id'],
				$_POST['b_edition']
			);

			if (!$query->execute())
				die(error_without_field("ERROR: Couldn't add book"));
			echo success("Successfully added book");
		}
	}
	?>
</body>
</html>
