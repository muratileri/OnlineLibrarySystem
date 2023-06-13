<?php
    require "../db_connect.php";
    require "../message_display.php";
    require "verify_librarian.php";
    require "header_librarian.php";
?>

<html>
<head>
    <title>Total Fine</title>
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
        $query = $con->prepare("SELECT * FROM TotalFinesByBorrower");
        $query->execute();
        $result = $query->get_result();
        if(!$result)
            die("ERROR: Couldn't fetch total fines by borrower");
        $rows = mysqli_num_rows($result);
        if($rows == 0)
            echo "<h2 align='center'>No fines available</h2>";
        else
        {
            echo "<form class='cd-form' method='POST' action='#'>";
            echo "<legend>Total Fines by Borrower</legend>";
            echo "<div class='error-message' id='error-message'>
                    <p id='error'></p>
                </div>";

            // Add filter input field
            echo "<input style='font-size:25px' type='text' id='filter-input' class='filter-input' placeholder='Filter by username'>";
            echo "<br>";

            echo "<table id='fines-table' width='100%' cellpadding='10' cellspacing='10'>";
            echo "<tr>
                    <th>Borrower ID<hr></th>
                    <th>Username<hr></th>
                    <th>Total Fines<hr></th>
                </tr>";

            for($i=0; $i<$rows; $i++)
            {
                $row = mysqli_fetch_array($result);
                echo "<tr>";
                echo "<td>".$row['borrower_id']."</td>"; // Borrower ID
                echo "<td>".$row['username']."</td>"; // Username
                echo "<td>".$row['total_fines']."</td>"; // Total Fines
                echo "</tr>";
            }
            echo "</table>";

            // Add the download button
            echo "<br>";
            echo "<button style= 'font-size:25px' type='submit' name='download' id='download-btn'>Download</button>";
            echo "</form>";
        }
    ?>


    <!-- JavaScript to handle the filter and download functionality -->
    <script>
        // Function to filter the table based on the input value
        function filterTable() {
            var input = document.getElementById('filter-input');
            var filter = input.value.toUpperCase();
            var table = document.getElementById('fines-table');
            var rows = table.getElementsByTagName('tr');

            // Loop through all table rows, and hide those that don't match the filter
            for (var i = 1; i < rows.length; i++) { // Start from index 1 to exclude the table header row
                var usernameCell = rows[i].getElementsByTagName('td')[1]; // Get the username cell
                var username = usernameCell.textContent || usernameCell.innerText;
                if (username.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = ''; // Show the row if it matches the filter
                } else {
                    rows[i].style.display = 'none'; // Hide the row if it doesn't match the filter
                }
            }
        }

        // Add input event listener to the filter input field
        var filterInput = document.getElementById('filter-input');
        filterInput.addEventListener('input', filterTable);

        // Function to download the table as a text file
        function downloadTableAsText() {
            // Get the table element
            var table = document.getElementById('fines-table');

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
