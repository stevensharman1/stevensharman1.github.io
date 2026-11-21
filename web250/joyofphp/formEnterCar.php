
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Add Vehicle</title>
</head>

<body>

<h1>Sam's Used Cars</h1>
<h2>Add a Vehicle</h2>

<form action="SubmitCar.php" method="post">

    <label for="VIN">VIN:</label>
    <input name="VIN" id="VIN" type="text" maxlength="17" required>
    <br><br>

    <label for="Make">Make:</label>
    <input name="Make" id="Make" type="text" required>
    <br><br>

    <label for="Model">Model:</label>
    <input name="Model" id="Model" type="text" required>
    <br><br>

    <label for="Asking_Price">Price:</label>
    <input name="Asking_Price" id="Asking_Price" type="number" min="0" step="0.01" required>
    <br><br>

    <input name="Submit1" type="submit" value="Submit">

</form>

<br>

<a href="ViewCarsWithStyle2.php">Return to Current Inventory</a>

</body>
</html>
