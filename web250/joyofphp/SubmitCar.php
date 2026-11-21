
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Car Saved</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Sam's Used Cars</h1>

<?php
// Make sure the form was submitted.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("<p>Please submit the Add Vehicle form.</p>
          <p><a href='formEnterCar.php'>Add a Vehicle</a></p>");
}

// Capture the submitted values.
$VIN = trim($_POST['VIN'] ?? '');
$YEAR = (int)($_POST['YEAR'] ?? 0);
$Make = trim($_POST['Make'] ?? '');
$Model = trim($_POST['Model'] ?? '');
$TRIM = trim($_POST['TRIM'] ?? '');
$EXT_COLOR = trim($_POST['EXT_COLOR'] ?? '');
$INT_COLOR = trim($_POST['INT_COLOR'] ?? '');
$ASKING_PRICE = $_POST['ASKING_PRICE'] ?? '';
$SALE_PRICE = $_POST['SALE_PRICE'] ?? '0';
$PURCHASE_PRICE = $_POST['PURCHASE_PRICE'] ?? '0';
$MILEAGE = (int)($_POST['MILEAGE'] ?? 0);
$TRANSMISSION = trim($_POST['TRANSMISSION'] ?? '');
$PURCHASE_DATE = $_POST['PURCHASE_DATE'] ?? '';
$SALE_DATE = $_POST['SALE_DATE'] ?? '';

// Validate required fields.
if (
    $VIN === '' ||
    strlen($VIN) !== 17 ||
    $YEAR < 1900 ||
    $Make === '' ||
    $Model === '' ||
    $ASKING_PRICE === '' ||
    !is_numeric($ASKING_PRICE) ||
    !is_numeric($SALE_PRICE) ||
    !is_numeric($PURCHASE_PRICE) ||
    (float)$ASKING_PRICE < 0 ||
    (float)$SALE_PRICE < 0 ||
    (float)$PURCHASE_PRICE < 0 ||
    $MILEAGE < 0
) {
    exit("<p>Please check the vehicle information and try again.</p>
          <p><a href='formEnterCar.php'>Return to Add Vehicle</a></p>");
}

// Convert prices to numbers.
$ASKING_PRICE = (float)$ASKING_PRICE;
$SALE_PRICE = (float)$SALE_PRICE;
$PURCHASE_PRICE = (float)$PURCHASE_PRICE;

// Convert empty dates to NULL.
$PURCHASE_DATE = ($PURCHASE_DATE === '') ? null : $PURCHASE_DATE;
$SALE_DATE = ($SALE_DATE === '') ? null : $SALE_DATE;

// Connect to the database.
require_once 'db.php';

// Insert all 14 vehicle fields.
$query = "INSERT INTO inventory (
    VIN, YEAR, Make, Model, TRIM, EXT_COLOR, INT_COLOR,
    ASKING_PRICE, SALE_PRICE, PURCHASE_PRICE, MILEAGE,
    TRANSMISSION, PURCHASE_DATE, SALE_DATE
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    exit("<p>Database error: " .
         htmlspecialchars($mysqli->error) . "</p>");
}

// Bind all 14 values.
$stmt->bind_param(
    "sisssssdddisss",
    $VIN,
    $YEAR,
    $Make,
    $Model,
    $TRIM,
    $EXT_COLOR,
    $INT_COLOR,
    $ASKING_PRICE,
    $SALE_PRICE,
    $PURCHASE_PRICE,
    $MILEAGE,
    $TRANSMISSION,
    $PURCHASE_DATE,
    $SALE_DATE
);

// Save the vehicle.
if ($stmt->execute()) {
    echo "<h2>Vehicle Successfully Added!</h2>";
    echo "<p>You added " .
         htmlspecialchars($YEAR . " " . $Make . " " . $Model) .
         " to the inventory.</p>";
} else {
    echo "<p>Error entering vehicle: " .
         htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$mysqli->close();
?>

<p><a href="formEnterCar.php">Add Another Vehicle</a></p>
<p><a href="ViewCarsWithStyle2.php">View Current Inventory</a></p>
<p><a href="index.php">Home</a></p>

</body>
</html>
