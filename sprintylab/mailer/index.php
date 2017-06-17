<?php
/**
 * Created by PhpStorm.
 * User: chand
 * Date: 2017-06-17
 * Time: 8:00 AM
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Uploader</title>
</head>

<body>
<form method="POST" name="email_form_with_php"
      action="SendMail.php" enctype="multipart/form-data">

    <label for='name'>Name: </label><br>
    <input type="text" name="name"><br>

    <label for='email'>Email: </label><br>
    <input type="text" name="email"><br>

    <label for='message'>Message:</label><br>
    <textarea name="message"></textarea><br>

    <label for='uploaded_file'>Select A File To Upload:</label><br>
    <input type="file" name="uploaded_file"><br><br>

    <input type="submit" value="Submit" name='submit'><br>
</form>
</body>
</html>