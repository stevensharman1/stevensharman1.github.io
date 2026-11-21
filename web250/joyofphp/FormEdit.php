
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Entry Form</title>
</head>
<body>

<h1>Sam's Used Cars</h1>

<?php
include 'db.php';

// Get the VIN from the URL.
$vin = $_GET['VIN'] ?? '';

if ($vin === '') {
    die("<p>No vehicle VIN was provided.</p>");
}

// Find the vehicle using its VIN.
$query = "SELECT VIN, Make, Model, ASKING_PRICE
          FROM inventory
          WHERE VIN = ?";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("<p>Database error: " .
        htmlspecialchars($mysqli->error) . "</p>");
}

$stmt->bind_param("s", $vin);
$stmt->execute();

$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    die("<p>Sorry, no vehicle was found with VIN " .
        htmlspecialchars($vin) . ".</p>");
}

// Store the vehicle information.
$VIN = $car['VIN'];
$make = $car['Make'];
$model = $car['Model'];
$price = $car['ASKING_PRICE'];

$stmt->close();
$mysqli->close();
?>

<form action="EditCar.php" method="post">

    <input type="hidden" name="VIN"
           value="<?php echo htmlspecialchars($VIN); ?>" />

    <p>
        Make:
        <input name="Make" type="text"
               value="<?php echo htmlspecialchars($make); ?>" />
    </p>

    <p>
        Model:
        <input name="Model" type="text"
               value="<?php echo htmlspecialchars($model); ?>" />
    </p>

    <p>
        Price:
        <input name="Asking_Price" type="number" step="0.01"
               value="<?php echo htmlspecialchars($price); ?>" />
    </p>

    <input name="Submit1" type="submit" value="Save Changes" />

</form>

<p><a href="ViewCarsWithStyle2.php">Back to Vehicle List</a></p>

</body>
</html>
