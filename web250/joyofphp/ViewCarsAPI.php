
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sam's Used Cars - Complete Inventory</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

<h1>Sam's Used Cars</h1>
<h3>Complete Inventory</h3>

<?php
// Get vehicle data from the API.
$apiUrl = 'cars_list_api.php';
$response = file_get_contents($apiUrl);

if ($response === false) {
    die("<p>Error fetching car data from the API.</p>");
}

// Convert the JSON response into a PHP array.
$cars = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("<p>Error reading vehicle data: " .
        htmlspecialchars(json_last_error_msg()) . "</p>");
}

if (isset($cars['error'])) {
    die("<p>Error: " .
        htmlspecialchars($cars['error']) . "</p>");
}

// Display the inventory table.
echo "<table id='Grid' style='width: 80%'>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "</tr>";

$class = "odd";

if (!empty($cars)) {

    foreach ($cars as $car) {

        echo "<tr class='$class'>";

        echo "<td>" .
             htmlspecialchars($car['Make'] ?? '') .
             "</td>";

        echo "<td>" .
             htmlspecialchars($car['Model'] ?? '') .
             "</td>";

        echo "<td>$" .
             htmlspecialchars((string)($car['ASKING_PRICE'] ?? '')) .
             "</td>";

        echo "</tr>";

        $class = ($class == "odd") ? "even" : "odd";
    }

} else {
    echo "<tr><td colspan='3'>No vehicles found.</td></tr>";
}

echo "</table>";
?>

<p><a href="index.php">Home</a></p>

</body>
</html>
