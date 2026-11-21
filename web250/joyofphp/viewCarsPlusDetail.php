
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Complete Inventory</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Sam's Used Cars</h1>
<h3>Complete Inventory</h3>

<?php
$apiUrl = 'https://stevensharman1.wasmer.app/web250/joyofphp/cars_list_api.php';

$response = file_get_contents($apiUrl);
$cars = json_decode($response, true);

if ($response === false || json_last_error() !== JSON_ERROR_NONE) {
    echo "<p>Unable to read vehicle data.</p>";
} elseif (isset($cars['error'])) {
    echo "<p>" . htmlspecialchars($cars['error']) . "</p>";
} else {
    echo "<table id='Grid'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>VIN</th>";
    echo "<th>Year</th>";
    echo "<th>Make</th>";
    echo "<th>Model</th>";
    echo "<th>Trim</th>";
    echo "<th>Exterior Color</th>";
    echo "<th>Interior Color</th>";
    echo "<th>Asking Price</th>";
    echo "<th>Sale Price</th>";
    echo "<th>Purchase Price</th>";
    echo "<th>Mileage</th>";
    echo "<th>Transmission</th>";
    echo "<th>Purchase Date</th>";
    echo "<th>Sale Date</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    if (!empty($cars)) {
        $rowNumber = 0;

        foreach ($cars as $car) {
            $class = ($rowNumber % 2 === 0) ? "odd" : "even";

            echo "<tr class='$class'>";
            echo "<td>" . htmlspecialchars($car['VIN'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['YEAR'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['Make'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['Model'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['TRIM'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['EXT_COLOR'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['INT_COLOR'] ?? '') . "</td>";
            echo "<td>$" . number_format((float)($car['ASKING_PRICE'] ?? 0), 2) . "</td>";
            echo "<td>$" . number_format((float)($car['SALE_PRICE'] ?? 0), 2) . "</td>";
            echo "<td>$" . number_format((float)($car['PURCHASE_PRICE'] ?? 0), 2) . "</td>";
            echo "<td>" . number_format((int)($car['MILEAGE'] ?? 0)) . "</td>";
            echo "<td>" . htmlspecialchars($car['TRANSMISSION'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['PURCHASE_DATE'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($car['SALE_DATE'] ?? '') . "</td>";
            echo "</tr>";

            $rowNumber++;
        }
    } else {
        echo "<tr><td colspan='14'>No vehicles found.</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";
}
?>

<p><a href="index.php">Home</a></p>

</body>
</html>
