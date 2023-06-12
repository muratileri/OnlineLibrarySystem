<?php
    require "../db_connect.php";
    require "stats.php"; // Replace "your_filename.php" with the actual filename that contains the $tableData array

    // Get the selected table name from the URL parameter
    $selectedTable = isset($_POST['table_name']) ? $_POST['table_name'] : '';

    // Check if the selected table is valid
    $isValidTable = array_key_exists($selectedTable, $tableData);

    // SQL query to fetch data from the selected table (same as before)

    // Execute the query and fetch the results (same as before)

    // Function to generate the report and save it as a file (same as before)

    // Check if the download format is specified and valid
    if (isset($_POST['format']) && ($_POST['format'] === 'txt' || $_POST['format'] === 'pdf')) {
        // Generate the report and save it as a file
        generateReport($tableData[$selectedTable], $selectedTable . '_report', $_POST['format']);
    } else {
        echo "Invalid format specified.";
    }
?>
