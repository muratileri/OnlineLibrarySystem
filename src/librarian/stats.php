<?php
    require "../db_connect.php";
    require "../message_display.php";
    require "header_librarian.php";

    // Get the selected table name from the dropdown
    $selectedTable = isset($_POST['table_name']) ? $_POST['table_name'] : '';

    // Define an array of table names and their corresponding headers
    $tableData = [
            'author' => [
                'Author ID',
                'Name',
                'Biography',
                'Profile Image'
            ],
            'book' => [
                'Title',
                'Publication Date',
                'Author ID',
                'Subject Name',
                'Genre Name',
                'Language Name',
                'Series Name',
                'Format Name',
                'Publisher ID',
                'Rating ID',
                'Review ID',
                'Edition'
            ],
            'bookcopy' => [
                'Copy ID',
                'Branch ID',
                'Status'
            ],
            'borrower' => [
                'Borrower ID',
                'User ID',
                'Borrower Type'
            ],
            'fine' => ['Fine ID', 'Loan ID', 'Fine Amount', 'Fine Reason', 'Fine Date'],
            'finepayment' => [
                'Payment ID',
                'Fine ID',
                'Payment Date',
                'Payment Amount'
            ],
            'librarian' => [
                'Librarian ID',
                'User ID',
                'Hire Date'
            ],
            'librarybranch' => [
                'Branch ID',
                'Name',
                'Location ID'
            ],
            'loan' => [
                'Loan ID',
                'Borrower ID',
                'Copy ID',
                'Checkout Date',
                'Due Date',
                'Return Date'
            ],
            'location' => ['Location ID', 'Address'],
            'publisher' => [
                'Publisher ID',
                'Name',
                'Location ID',
                'Phone Number'
            ],
            'rating' =>  [
                'Rating ID',
                'User ID',
                'Rating Value',
                'Rating Date'
            ],
            'reservation' => [
                'Reservation ID',
                'Borrower ID',
                'Reservation Date'
            ],
            'review' => [
                'Review ID',
                'User ID',
                'Review Text',
                'Review Date'
            ],
            'supplier' => [
                'Supplier ID',
                'Name',
                'Location ID',
                'Phone Number'
            ],
            'useraccount' => [
                'User ID',
                'Username',
                'Password',
                'Email',
                'Address',
                'Phone Number'
            ],
        ];
    // Check if the selected table is valid
    $isValidTable = array_key_exists($selectedTable, $tableData);

    // SQL query to fetch data from the selected table
    $query = $isValidTable ? $con->prepare("SELECT * FROM $selectedTable") : false;

    if ($query) {
        $query->execute();
        $result = $query->get_result();
    }

    // SQL query to fetch count from the selected table
    $countQuery = $isValidTable ? $con->prepare("SELECT COUNT(*) AS count FROM $selectedTable") : false;

    if ($countQuery) {
        $countQuery->execute();
        $countResult = $countQuery->get_result();
        $countRow = mysqli_fetch_assoc($countResult);
        $count = $countRow['count'];
    }

    // SQL query to fetch average fine amount from the fine table
    $avgFineAmountQuery = $selectedTable === 'fine' ? $con->prepare("SELECT AVG(`fine_amount`) AS average FROM $selectedTable") : false;

    if ($avgFineAmountQuery) {
        $avgFineAmountQuery->execute();
        $avgFineAmountResult = $avgFineAmountQuery->get_result();
        $avgFineAmountRow = mysqli_fetch_assoc($avgFineAmountResult);
        $averageFineAmount = $avgFineAmountRow['average'];
    }

    // SQL query to fetch maximum and minimum fine amount from the fine table
    $minMaxFineAmountQuery = $selectedTable === 'fine' ? $con->prepare("SELECT MIN(`fine_amount`) AS min_amount, MAX(`fine_amount`) AS max_amount FROM $selectedTable") : false;

    if ($minMaxFineAmountQuery) {
        $minMaxFineAmountQuery->execute();
        $minMaxFineAmountResult = $minMaxFineAmountQuery->get_result();
        $minMaxFineAmountRow = mysqli_fetch_assoc($minMaxFineAmountResult);
        $minFineAmount = $minMaxFineAmountRow['min_amount'];
        $maxFineAmount = $minMaxFineAmountRow['max_amount'];
    }

    // Function to generate the report and save it as a file
    function generateReport($data, $filename, $format) {
        $content = '';

        // Generate the report content
        foreach ($data as $row) {
            foreach ($row as $header => $value) {
                $content .= $header . ': ' . $value . "\n";
            }
            $content .= "\n";
        }

        // Determine the file extension based on the selected format
        $extension = $format === 'pdf' ? 'pdf' : 'txt';

        // Set the appropriate headers for the download
        header("Content-Type: application/$extension");
        header("Content-Disposition: attachment; filename=$filename.$extension");

        // Output the content to the browser
        if ($format === 'pdf') {
            // Generate PDF using a library like TCPDF, FPDF, etc.
            // Example using TCPDF library:
            require_once('tcpdf/tcpdf.php');

            $pdf = new TCPDF();
            $pdf->SetTitle($filename);
            $pdf->AddPage();
            $pdf->SetFont('times', '', 12);
            $pdf->Write(0, $content);
            $pdf->Output('report.pdf', 'D');
        } else {
            // Output as plain text
            echo $content;
        }

        // Exit to prevent further output
        exit();
    }

    // Check if the download button was clicked
    if (isset($_POST['download'])) {
        // Generate the report and save it as a file
        generateReport($tableData[$selectedTable], $selectedTable . '_report', $_POST['format']);
    }

    //burdan itibaren buton kodları
    if (isset($_POST['submit'])) {
        $submitType = $_POST['submit'];

        if ($submitType === 'Add') {
            $addFields = [];

            foreach ($tableData[$selectedTable] as $header) {
                $fieldName = 'add_'.strtolower(str_replace(' ', '_', $header));

                if (isset($_POST[$fieldName])) {
                    $addFields[$header] = $_POST[$fieldName];
                }
            }

            if (count($addFields) === count($tableData[$selectedTable])) {
                $placeholders = implode(', ', array_fill(0, count($addFields), '?'));

                $addQuery = $con->prepare("INSERT INTO $selectedTable (".implode(', ', $tableData[$selectedTable]).") VALUES ($placeholders)");

                $types = str_repeat('s', count($addFields));
                $addQuery->bind_param($types, ...array_values($addFields));

                if ($addQuery->execute()) {
                    // Success: Added the row
                } else {
                    // Error: Failed to add the row
                }
            } else {
                // Error: Missing fields for adding the row
            }
        } elseif ($submitType === 'Update') {
            // Get the values to update
            $updateFields = [];

            foreach ($tableData[$selectedTable] as $header) {
                $fieldName = 'add_'.strtolower(str_replace(' ', '_', $header));

                if (isset($_POST[$fieldName])) {
                    $updateFields[$header] = $_POST[$fieldName];
                }
            }

            if (count($updateFields) === count($tableData[$selectedTable])) {
                // Get the selected row's index
                $rowIndex = $_POST['row_id'] + 1;

                // Update the table row with the new values
                $table = $con->prepare("UPDATE $selectedTable SET ".implode(' = ?, ', $tableData[$selectedTable])." = ? WHERE id = ?");
                $types = str_repeat('s', count($updateFields));
                $values = array_merge(array_values($updateFields), [$rowIndex]);
                $table->bind_param($types.'i', ...$values);

                if ($table->execute()) {
                    // Success: Updated the row
                } else {
                    // Error: Failed to update the row
                }
            } else {
                // Error: Missing fields for updating the row
            }
        } elseif ($submitType === 'Delete') {
            // Get the selected row's index
            $rowIndex = $_POST['row_id'] + 1;

            // Delete the table row
            $table = $con->prepare("DELETE FROM $selectedTable WHERE id = ?");
            $table->bind_param('i', $rowIndex);

            if ($table->execute()) {
                // Success: Deleted the row
            } else {
                // Error: Failed to delete the row
            }
        }
    }

function openAccordion($rowId) {
    echo "<script>";
    echo "var accordionRow = document.getElementById('add-row-accordion');";
    echo "accordionRow.style.display = 'table-row';";

    echo "var table = document.querySelector('table');";
    echo "var tableRows = table.getElementsByTagName('tr');";
    echo "var updateRow = tableRows[$rowId + 1];";
    echo "var inputFields = accordionRow.getElementsByTagName('input');";
    echo "var updateFields = updateRow.getElementsByTagName('td');";

    echo "for (var i = 0; i < inputFields.length; i++) {";
    echo "    inputFields[i].value = updateFields[i].textContent.trim();";
    echo "}";
    echo "</script>";
}

function deleteRow($rowId) {
    echo "<script>";
    echo "var table = document.querySelector('table');";
    echo "var tableRows = table.getElementsByTagName('tr');";
    echo "var deleteRow = tableRows[$rowId + 1];";
    echo "deleteRow.remove();";
    echo "</script>";
}

?>

<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css">
    <link rel="stylesheet" type="text/css" href="css/home_style.css">
    <link rel="stylesheet" type="text/css" href="../css/custom_radio_button_style.css">
</head>
<body>
    <form class='cd-form' method='POST' action='#'>
        <label>Select a table:</label>
        <select name="table_name" onchange="this.form.submit()">
            <option value="">Select</option>
            <?php
            // Display the dropdown options
            foreach ($tableData as $tableName => $headers) {
                $selected = $selectedTable === $tableName ? 'selected' : '';
                echo "<option value='$tableName' $selected>$tableName</option>";
            }
            ?>
        </select>
    </form>

    <?php
    if (!$selectedTable) {
        echo "<h2 align='center'>Please select a table</h2>";
    } elseif (!$isValidTable) {
        echo "<h2 align='center'>Invalid table selection</h2>";
    } elseif (!$result) {
        echo "<h2 align='center'>ERROR: Couldn't fetch $selectedTable table</h2>";
    } else {
        echo "<form class='cd-form' method='POST' action='#'>";
        echo "<legend>$selectedTable Table</legend>";
        echo "<div class='error-message' id='error-message'>
                <p id='error'></p>
            </div>";
        echo "<table width='100%' cellpadding='10' cellspacing='10'>";
        echo "<tr>";
        foreach ($tableData[$selectedTable] as $header) {
            echo "<th>$header<hr></th>";
        }
        echo "</tr>";

        $rows = mysqli_num_rows($result);
        for ($i = 0; $i < $rows; $i++) {
            $row = mysqli_fetch_array($result);
            echo "<tr>";
            foreach ($tableData[$selectedTable] as $header) {
                echo "<td>".$row[strtolower(str_replace(' ', '_', $header))]."</td>";

            }
            echo "<td>
             <input class='button' type='submit' name='submit' onclick='openAccordion($i)' value='Update'>
             <input class='button' type='submit' name='submit' onclick='deleteRow($i)' value='Delete'>
            </td>";
            echo "</tr>";

        }
        echo "<tr id='add-row-accordion' style='display: none;'>"; // Add a hidden row for the accordion
            foreach ($tableData[$selectedTable] as $header) {
                echo "<td><input type='text' name='add_".strtolower(str_replace(' ', '_', $header))."'></td>";
            }
        echo "<div>
            <input class='button' type='submit' name='submit' value='Add'>
        </div>";

        echo "</table>";

        // Display the count of rows
        echo "<h3>Total Data Count: $count</h3>";

        // Display the average fine amount if the selected table is 'fine'
        if ($selectedTable === 'fine') {
            echo "<h3>Average Fine Amount: $averageFineAmount</h3>";
            echo "<h3>Minimum Fine Amount: $minFineAmount</h3>";
            echo "<h3>Maximum Fine Amount: $maxFineAmount</h3>";
        }

        // Add the download button
        echo "<form class='cd-form' method='POST' action='#'>";
        echo "<input type='hidden' name='format' value='txt'>";
        echo "<input type='submit' name='download' value='Download as TXT'>";
        echo "</form>";
        echo "<form class='cd-form' method='POST' action='#'>";
        echo "<input type='hidden' name='format' value='pdf'>";
        echo "<input type='submit' name='download' value='Download as PDF'>";
        echo "</form>";

        echo "</form>";
    }
    ?>

</body>
</html>
