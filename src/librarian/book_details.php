<?php
    require "../db_connect.php";
    require "../message_display.php";
    require "verify_librarian.php";
    require "header_librarian.php";
?>

<html>
<head>
    <title>Book Details</title>
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css">
    <link rel="stylesheet" type="text/css" href="css/home_style.css">
    <link rel="stylesheet" type="text/css" href="../css/custom_radio_button_style.css">
    <style>
        .filter-input {
            margin-bottom: 10px;
        }
    </style>
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

            // Add filter input field
            echo "<input style='font-size:25px' type='text' id='filter-input' class='filter-input' placeholder='Filter by title'>";
            echo "<br>";

            echo "<table id='book-table' width='100%' cellpadding='10' cellspacing='10'>";
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
                echo "<tr>";
                echo "<td>
                        <label class='control control--radio'>
                            <input type='radio' name='rd_book' value=".$row['ISBN']." />
                            <div class='control__indicator'></div>
                        </label>
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

    <script>
        // Function to filter the table based on the input value
        function filterTable() {
            var input = document.getElementById('filter-input');
            var filter = input.value.toUpperCase();
            var table = document.getElementById('book-table');
            var rows = table.getElementsByTagName('tr');

            // Loop through all table rows, and hide those that don't match the filter
            for (var i = 1; i < rows.length; i++) { // Start from index 1 to exclude the table header row
                var titleCell = rows[i].getElementsByTagName('td')[2]; // Get the title cell
                var title = titleCell.textContent || titleCell.innerText;
                if (title.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = ''; // Show the row if it matches the filter
                } else {
                    rows[i].style.display = 'none'; // Hide the row if it doesn't match the filter
                }
            }
        }

        // Add input event listener to the filter input field
        var filterInput = document.getElementById('filter-input');
        filterInput.addEventListener('input', filterTable);
    </script>
</body>
</html>
