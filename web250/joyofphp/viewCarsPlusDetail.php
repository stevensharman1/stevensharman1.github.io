
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
// Get vehicle data from the API.
$apiUrl = 'https://stevensharman1.wasmer.app/web250/joyofphp/cars_list_api.php';

$response = file_get_contents($apiUrl);

// Check whether the API returned data.
if ($response === false) {
    die("<p>Error fetching vehicle data from the API.</p>");
}

// Convert JSON into a PHP array.
$cars = json_decode($response, true);

// Check for JSON errors.
if (json_last_error() !== JSON_ERROR_NONE) {
    die("<p>Error reading vehicle data: " .
        htmlspecialchars(json_last_error_msg()) . "</p>");
}

// Check for API errors.
if (isset($cars['error'])) {
    die("<p>Error: " . htmlspecialchars($cars['error']) . "</p>");
}

// Display inventory table.
echo "<table id='Grid' style='width: 80%; margin: auto;'>";

echo "<thead>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";

if (!empty($cars)) {

    $rowNumber = 0;

    foreach ($cars as $car) {

        $class = ($rowNumber % 2 === 0) ? "odd" : "even";

        echo "<tr class='$class'>";

        echo "<td>" .
            htmlspecialchars($car['Make'] ?? '') .
            "</td>";

        echo "<td>" .
            htmlspecialchars($car['Model'] ?? '') .
            "</td>";

        echo "<td>$" .
            number_format((float)($car['ASKING_PRICE'] ?? 0), 2) .
            "</td>";

        echo "</tr>";

        $rowNumber++;
    }

} else {
    echo "<tr><td colspan='3'>No vehicles found.</td></tr>";
}

echo "</tbody>";
echo "</table>";
?>

<p style="text-align: center; margin-top: 20px;">
    <a href="index.php">Home</a>
</p>

</body>
</html>
