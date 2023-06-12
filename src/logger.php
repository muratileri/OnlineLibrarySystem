<?php
function logActivity($message) {
    $logFile = '../librarian/logfile.log';

    // Get the current timestamp
    $timestamp = date('Y-m-d H:i:s');

    // Get member details
    $username = $_SESSION['username'];

    // Format the log entry
    $logEntry = "[{$timestamp}] - User: {$username} - {$message}" . PHP_EOL;

    // Open the log file in append mode
    $fileHandle = fopen($logFile, 'a');

    if ($fileHandle) {
        // Write the log entry to the file
        fwrite($fileHandle, $logEntry);

        // Close the file
        fclose($fileHandle);
    } else {
        // Failed to open the log file
        echo "Failed to open log file for writing.";
    }
}
?>
