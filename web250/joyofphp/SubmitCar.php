
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Saved</title>
</head>
<body>

<h1>Sam's Used Cars</h1>

<?php
// Capture and clean the values posted from the form.
$VIN = trim($_POST['VIN'] ?? '');
$Make = trim($_POST['Make'] ?? '');
$Model = trim($_POST['Model'] ?? '');
$Price = $_POST['Asking_Price'] ?? '';

// Validate the submitted information.
if ($VIN === '' || $Make === '' || $Model === '' ||
    $Price === '' || !is_numeric($Price) || $Price < 0) {
    die("<p>Please provide a VIN, Make, Model, and valid asking price.</p>
         <p><a href='index.php'>Home</a></p>");
}

// Connect to the database.
include 'db.php';

// Insert the new vehicle.
$query = "INSERT INTO inventory (VIN, Make, Model, ASKING_PRICE)
          VALUES (?, ?, ?, ?)";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("<p>Database error: " .
        htmlspecialchars($mysqli->error) . "</p>");
}

$stmt->bind_param("sssd", $VIN, $Make, $Model, $Price);

// Try to insert the vehicle.
if ($stmt->execute()) {
    echo "<p>You have successfully entered " .
         htmlspecialchars($Make) . " " .
         htmlspecialchars($Model) . " into the database.</p>";
} else {
    echo "<p>Error entering vehicle: " .
         htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$mysqli->close();
?>

<?php include 'footer.php'; ?>

<p><a href="ViewCarsWithStyle2.php">View Cars</a></p>

</body>
</html>

$mysqli->close();
include 'footer.php'
?>
</body>
</html>
