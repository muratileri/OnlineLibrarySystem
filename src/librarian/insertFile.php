<?php
require "../db_connect.php";
require "../message_display.php";
require "verify_librarian.php";
require "header_librarian.php";
?>


<html>
<head>
    <title>File Upload</title>
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
    <link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
    <link rel="stylesheet" href="css/insert_book_style.css">
</head>
<body>
    <h1>File Upload Form</h1>
    <?php
    if(isset($_POST["submit"])) {
        $targetDirectory = ".././uploads/"; // Directory where uploaded files will be stored
        $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]); // Path to the uploaded file

        // Check if file already exists
        if(file_exists($targetFile)) {
            echo "File already exists.";
        } else {
            // Move the uploaded file to the target directory
            if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                echo "File uploaded successfully.";
            } else {
                echo "Error uploading file.";
            }
        }
    }
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload" name="submit">
    </form>
</body>
</html>
