
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Upload Image</title>
</head>
<body>

<h1>Sam's Used Cars</h1>
<h3>Upload Vehicle Image</h3>

<form action="upload_file.php" method="post" enctype="multipart/form-data">

    <label for="VIN">Vehicle VIN:</label>
    <input type="text" name="VIN" id="VIN" required>
    <br><br>

    <label for="file">Filename:</label>
    <input type="file" name="file" id="file" accept="image/*" required>
    <br><br>

    <input type="submit" name="submit" value="Submit">

</form>

</body>
</html>
