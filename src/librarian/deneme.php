<?php
require "../db_connect.php";
require "../message_display.php";
require "verify_librarian.php";
require "header_librarian.php";
?>

<html>
<head>
    <title>Statistics Screen</title>
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
    <link rel="stylesheet" type="text/css" href="../css/table_styles.css" />
</head>
<body>
    <h1>Statistics Screen</h1>

    <table class="data-table">
        <caption class="title">Statistical Query Results</caption>
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Number Pages</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT
                        ISBN,
                        title,
                        author,
                        genre,
                        num_pages,
                        status
                    FROM
                        books";
            $result = mysqli_query($con, $query);

            // Check if the query executed successfully
            if (!$result) {
                die("Error retrieving data.");
            }

            // Use mathematical SQL functions to retrieve statistical results
            $query_avg_pages = "SELECT AVG(num_pages) AS avg_pages FROM books";
            $query_max_pages = "SELECT MAX(num_pages) AS max_pages FROM books";
            $query_min_pages = "SELECT MIN(num_pages) AS min_pages FROM books";
            $query_total_books = "SELECT COUNT(*) AS total_books FROM books";
            $query_checked_out_books = "SELECT COUNT(*) AS checked_out_books FROM books WHERE status = 'Checked Out'";
            $query_distinct_authors = "SELECT COUNT(DISTINCT author) AS distinct_authors FROM books";
            $query_distinct_genres = "SELECT COUNT(DISTINCT genre) AS distinct_genres FROM books";
            $query_first_title = "SELECT title AS first_title FROM books ORDER BY ISBN ASC LIMIT 1";
            $query_last_title = "SELECT title AS last_title FROM books ORDER BY ISBN DESC LIMIT 1";

            $result_avg_pages = mysqli_query($con, $query_avg_pages);
            $result_max_pages = mysqli_query($con, $query_max_pages);
            $result_min_pages = mysqli_query($con, $query_min_pages);
            $result_total_books = mysqli_query($con, $query_total_books);
            $result_checked_out_books = mysqli_query($con, $query_checked_out_books);
            $result_distinct_authors = mysqli_query($con, $query_distinct_authors);
            $result_distinct_genres = mysqli_query($con, $query_distinct_genres);
            $result_first_title = mysqli_query($con, $query_first_title);
            $result_last_title = mysqli_query($con, $query_last_title);

            $avg_pages = mysqli_fetch_assoc($result_avg_pages)['avg_pages'];
            $max_pages = mysqli_fetch_assoc($result_max_pages)['max_pages'];
            $min_pages = mysqli_fetch_assoc($result_min_pages)['min_pages'];
            $total_books = mysqli_fetch_assoc($result_total_books)['total_books'];
            $checked_out_books = mysqli_fetch_assoc($result_checked_out_books)['checked_out_books'];
            $distinct_authors = mysqli_fetch_assoc($result_distinct_authors)['distinct_authors'];
            $distinct_genres = mysqli_fetch_assoc($result_distinct_genres)['distinct_genres'];
            $first_title = mysqli_fetch_assoc($result_first_title)['first_title'];
            $last_title = mysqli_fetch_assoc($result_last_title)['last_title'];

            // Display the statistical query results
            echo "<tr>";
            echo "<td>Average Pages</td>";
            echo "<td colspan='5'>$avg_pages</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Maximum Pages</td>";
            echo "<td colspan='5'>$max_pages</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Minimum Pages</td>";
            echo "<td colspan='5'>$min_pages</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Total Books</td>";
            echo "<td colspan='5'>$total_books</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Checked Out Books</td>";
            echo "<td colspan='5'>$checked_out_books</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Distinct Authors</td>";
            echo "<td colspan='5'>$distinct_authors</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Distinct Genres</td>";
            echo "<td colspan='5'>$distinct_genres</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>First Title (by ISBN)</td>";
            echo "<td colspan='5'>$first_title</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Last Title (by ISBN)</td>";
            echo "<td colspan='5'>$last_title</td>";
            echo "</tr>";

            // Display the book details
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$row['ISBN']."</td>";
                echo "<td>".$row['title']."</td>";
                echo "<td>".$row['author']."</td>";
                echo "<td>".$row['genre']."</td>";
                echo "<td>".$row['num_pages']."</td>";
                echo "<td>".$row['status']."</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
