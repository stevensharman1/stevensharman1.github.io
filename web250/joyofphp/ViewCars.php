
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Complete Inventory</title>
    <link rel="stylesheet" href="Grid.css">
</head>

<body>

<h1>Sam's Used Cars</h1>
<h3>Complete Inventory</h3>

<?php
require_once 'db.php';

// Retrieve all vehicles.
$query = "SELECT * FROM inventory ORDER BY Make";
$result = $mysqli->query($query);

if (!$result) {
    die("<p>Error retrieving inventory: "
        . htmlspecialchars($mysqli->error) . "</p>");
}

// Display inventory using the Grid table format.
echo "<table id='Grid'>";
echo "<thead>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

if ($result->num_rows > 0) {

    $class = "odd";

    while ($car = $result->fetch_assoc()) {

        echo "<tr class='$class'>";

        echo "<td>" . htmlspecialchars($car['Make']) . "</td>";
        echo "<td>" . htmlspecialchars($car['Model']) . "</td>";
        echo "<td>$" . number_format((float)$car['ASKING_PRICE'], 2) . "</td>";

        echo "</tr>";

        $class = ($class === "odd") ? "even" : "odd";
    }

} else {
    echo "<tr><td colspan='3'>No vehicles found.</td></tr>";
}

echo "</tbody>";
echo "</table>";

$result->free();
$mysqli->close();
?>

<p><a href="index.php">Home</a></p>

</body>
</html>
