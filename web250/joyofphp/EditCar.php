
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Saved</title>
</head>
<body>

<h1>Sam's Used Cars</h1>

<?php
// Capture the values posted from the form.
$VIN = $_POST['VIN'] ?? '';
$Make = $_POST['Make'] ?? '';
$Model = $_POST['Model'] ?? '';
$Price = $_POST['Asking_Price'] ?? '';

// Connect to the database.
include 'db.php';

// Update the existing vehicle.
$query = "UPDATE inventory
          SET Make = ?, Model = ?, ASKING_PRICE = ?
          WHERE VIN = ?";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("Error preparing update: " . htmlspecialchars($mysqli->error));
}

$stmt->bind_param("ssds", $Make, $Model, $Price, $VIN);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<p>You successfully updated $Make $Model.</p>";
    } else {
        echo "<p>No changes were made. The vehicle may already have these values, or the VIN was not found.</p>";
    }
} else {
    echo "<p>Error updating vehicle: " .
         htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$mysqli->close();
?>

<p><a href="ViewCarsWithStyle2.php">View Cars with Edit Links</a></p>

</body>
</html>
