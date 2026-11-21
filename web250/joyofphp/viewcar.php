
<?php
/**
 * Joy of PHP
 * Displays information about a selected vehicle.
 */

require_once 'db.php';

// Retrieve the VIN.
$vin = trim($_GET['VIN'] ?? '');

if ($vin === '') {
    die("No vehicle VIN was provided.");
}

// Prepare the database query.
$query = "SELECT * FROM inventory WHERE VIN = ?";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("Database query failed.");
}

$stmt->bind_param("s", $vin);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>Vehicle Not Found</h2>";
    echo "<p>No vehicle was found with that VIN.</p>";
} else {
    $car = $result->fetch_assoc();

    // Display vehicle information safely.
    echo "<h1>Sam's Used Cars</h1>";

    echo "<h2>"
        . htmlspecialchars($car['YEAR']) . " "
        . htmlspecialchars($car['Make']) . " "
        . htmlspecialchars($car['Model'])
        . "</h2>";

    echo "<p>Trim: " . htmlspecialchars($car['TRIM']) . "</p>";

    echo "<p>Asking Price: $"
        . number_format((float)$car['ASKING_PRICE'], 2)
        . "</p>";

    echo "<p>Exterior Color: "
        . htmlspecialchars($car['EXT_COLOR']) . "</p>";

    echo "<p>Interior Color: "
        . htmlspecialchars($car['INT_COLOR']) . "</p>";

    echo "<p>Mileage: "
        . htmlspecialchars($car['MILEAGE']) . "</p>";

    echo "<p>Transmission: "
        . htmlspecialchars($car['TRANSMISSION']) . "</p>";
}

$stmt->close();
$mysqli->close();
?>

<p><a href="ViewCars.php">Back to Vehicle List</a></p>
<p><a href="index.php">Joy of PHP Home</a></p>
</body>
</html>
