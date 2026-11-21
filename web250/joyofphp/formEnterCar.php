
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Add Vehicle</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Sam's Used Cars</h1>
<h2>Add a Vehicle</h2>

<form action="SubmitCar.php" method="post">

    <label for="VIN">VIN:</label>
    <input name="VIN" id="VIN" type="text" maxlength="17" required>

    <label for="YEAR">Year:</label>
    <input name="YEAR" id="YEAR" type="number" required>

    <label for="Make">Make:</label>
    <input name="Make" id="Make" type="text" required>

    <label for="Model">Model:</label>
    <input name="Model" id="Model" type="text" required>

    <label for="TRIM">Trim:</label>
    <input name="TRIM" id="TRIM" type="text">

    <label for="EXT_COLOR">Exterior Color:</label>
    <input name="EXT_COLOR" id="EXT_COLOR" type="text">

    <label for="INT_COLOR">Interior Color:</label>
    <input name="INT_COLOR" id="INT_COLOR" type="text">

    <label for="ASKING_PRICE">Asking Price:</label>
    <input name="ASKING_PRICE" id="ASKING_PRICE" type="number" min="0" step="0.01" required>

    <label for="SALE_PRICE">Sale Price:</label>
    <input name="SALE_PRICE" id="SALE_PRICE" type="number" min="0" step="0.01" value="0">

    <label for="PURCHASE_PRICE">Purchase Price:</label>
    <input name="PURCHASE_PRICE" id="PURCHASE_PRICE" type="number" min="0" step="0.01" value="0">

    <label for="MILEAGE">Mileage:</label>
    <input name="MILEAGE" id="MILEAGE" type="number" min="0">

    <label for="TRANSMISSION">Transmission:</label>
    <input name="TRANSMISSION" id="TRANSMISSION" type="text">

    <label for="PURCHASE_DATE">Purchase Date:</label>
    <input name="PURCHASE_DATE" id="PURCHASE_DATE" type="date">

    <label for="SALE_DATE">Sale Date:</label>
    <input name="SALE_DATE" id="SALE_DATE" type="date">

    <br><br>

    <input name="Submit1" type="submit" value="Add Vehicle">

</form>

<p style="text-align: center;">
    <a href="ViewCarsWithStyle2.php">Return to Current Inventory</a>
</p>

</body>
</html>
