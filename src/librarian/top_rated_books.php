<?php
    require "../db_connect.php";
    require "../message_display.php";
    require "verify_librarian.php";
    require "header_librarian.php";
?>

<html>
<head>
    <title>Top Rated Books</title>
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

            // Add filter input field
            echo "<input type='text' style= 'font-size:25px' id='filter-input' class='filter-input' placeholder='Filter by title'>";
            echo "<br>";

            echo "<table id='book-table' width='100%' cellpadding='10' cellspacing='10'>";
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

            // Add the download button
            echo "<br>";
            echo "<button style= 'font-size:25px' type='submit' name='download' id='download-btn'>Download</button>";
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
            var titleCell = rows[i].getElementsByTagName('td')[1]; // Get the title cell
            var title = titleCell.textContent || titleCell.innerText;
            if (title.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = ''; // Show the row
            } else {
                rows[i].style.display = 'none'; // Hide the row
            }
        }
    }

    // Add input event listener to the filter input field
    var filterInput = document.getElementById('filter-input');
    filterInput.addEventListener('input', filterTable);

    // Function to download the table as a text file
    function downloadTableAsText() {
        // Get the table element
        var table = document.getElementById('book-table');

        // Generate the table content as text
        var tableContent = "";
        var rows = table.rows.length;
        for (var i = 0; i < rows; i++) {
            var cells = table.rows[i].cells;
            for (var j = 0; j < cells.length; j++) {
                tableContent += cells[j].innerText + "\t"; // Separate values by tabs
            }
            tableContent += "\n"; // Add a line break after each row
        }

        // Create a temporary anchor element to trigger the download
        var downloadAnchor = document.createElement('a');
        downloadAnchor.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(tableContent);
        downloadAnchor.download = 'table_data.txt';
        downloadAnchor.style.display = 'none';
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        document.body.removeChild(downloadAnchor);
    }

    // Add click event listener to the download button
    var downloadBtn = document.getElementById('download-btn');
    downloadBtn.addEventListener('click', downloadTableAsText);
</script>
</body>
</html>
