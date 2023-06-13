<?php
    require "../db_connect.php";
    require "../message_display.php";
    require "verify_librarian.php";
    require "header_librarian.php";
?>

<html>
<head>
    <title>Overdue Loans</title>
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
        $query = $con->prepare("SELECT * FROM OverdueLoans");
        $query->execute();
        $result = $query->get_result();
        if(!$result)
            die("ERROR: Couldn't fetch overdue loans");
        $rows = mysqli_num_rows($result);
        if($rows == 0)
            echo "<h2 align='center'>No overdue loans available</h2>";
        else
        {
            echo "<form class='cd-form' method='POST' action='#'>";
            echo "<legend>Overdue Loans</legend>";
            echo "<div class='error-message' id='error-message'>
                    <p id='error'></p>
                </div>";

            // Add filter input field
            echo "<input style='font-size:25px' type='text' id='filter-input' class='filter-input' placeholder='Filter by borrower'>";
            echo "<br>";

            echo "<table id='overdue-loans-table' width='100%' cellpadding='10' cellspacing='10'>";
            echo "<tr>
                    <th>Loan ID<hr></th>
                    <th>Checkout Date<hr></th>
                    <th>Due Date<hr></th>
                    <th>Borrower<hr></th>
                </tr>";

            for($i=0; $i<$rows; $i++)
            {
                $row = mysqli_fetch_array($result);
                echo "<tr>";
                echo "<td>".$row['loan_id']."</td>"; // Loan ID
                echo "<td>".$row['checkout_date']."</td>"; // Checkout Date
                echo "<td>".$row['due_date']."</td>"; // Due Date
                echo "<td>".$row['borrower']."</td>"; // Borrower
                echo "</tr>";
            }
            echo "</table>";
            echo "</form>";
        }
    ?>


    <!-- JavaScript to handle the filter functionality -->
    <script>
        // Function to filter the table based on the input value
        function filterTable() {
            var input = document.getElementById('filter-input');
            var filter = input.value.toUpperCase();
            var table = document.getElementById('overdue-loans-table');
            var rows = table.getElementsByTagName('tr');

            // Loop through all table rows, and hide those that don't match the filter
            for (var i = 1; i < rows.length; i++) { // Start from index 1 to exclude the table header row
                var borrowerCell = rows[i].getElementsByTagName('td')[3]; // Get the borrower cell
                var borrower = borrowerCell.textContent || borrowerCell.innerText;
                if (borrower.toUpperCase().indexOf(filter) > -1) {
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
