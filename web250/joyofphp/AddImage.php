
<?php
include 'db.php';

$vin = $_GET['VIN'] ?? '';

if ($vin === '') {
    die("No vehicle VIN was provided.");
}

// Find the vehicle using its VIN.
$stmt = $mysqli->prepare("SELECT * FROM INVENTORY WHERE VIN = ?");
$stmt->bind_param("s", $vin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Sorry, a vehicle with VIN $vin cannot be found.");
}

$vehicle = $result->fetch_assoc();

$year = $vehicle['YEAR'];
$make = $vehicle['Make'];
$model = $vehicle['Model'];
$trim = $vehicle['TRIM'];
$color = $vehicle['EXT_COLOR'];
$interior = $vehicle['INT_COLOR'];
$mileage = $vehicle['MILEAGE'];
$transmission = $vehicle['TRANSMISSION'];
$price = $vehicle['ASKING_PRICE'];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Image Upload</title>
</head>

<body background="bg.jpg">

<h1>Sam's Used Cars</h1>
<h3>Add Image</h3>

<p>
    <?= htmlspecialchars("$color $year $make $model") ?><br>
    VIN: <?= htmlspecialchars($vin) ?>
</p>

<p>
    Asking Price: $<?= number_format((float)$price, 0) ?>
</p>

<form action="upload_file.php" method="post" enctype="multipart/form-data">
    <label for="file">Filename:</label>
    <input type="file" name="file" id="file" required><br>

    <input type="hidden" name="VIN" value="<?= htmlspecialchars($vin) ?>">

    <input type="submit" name="submit" value="Submit">
</form>

<br><br>

<?php
// Display images already associated with this vehicle.
$stmt = $mysqli->prepare("SELECT ImageFile FROM images WHERE VIN = ?");
$stmt->bind_param("s", $vin);
$stmt->execute();
$imageResult = $stmt->get_result();

while ($imageRow = $imageResult->fetch_assoc()) {
    $image = $imageRow['ImageFile'];

    echo '<img src="uploads/' . htmlspecialchars($image) . '" width="250" alt="Vehicle image">';
}

$stmt->close();
$mysqli->close();

include 'footer.php';
?>

</body>
</html>
