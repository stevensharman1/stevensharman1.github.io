
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Complete Inventory</title>
</head>

<body>

<h1>Sam's Used Cars</h1>
<h3>Complete Inventory</h3>

<?php
// Connect to the database.
require_once 'db.php';

// Check the database connection.
if (!isset($mysqli) || $mysqli->connect_error) {
    die("<p>Database connection failed.</p>");
}

// Get all vehicles, ordered by Make.
$query = "SELECT * FROM inventory ORDER BY Make";

$result = $mysqli->query($query);

// Check for query errors.
if (!$result) {
    die("<p>Error getting cars from the database: "
        . htmlspecialchars($mysqli->error) . "</p>");
}

// Display the inventory table.
echo "<table id='Grid' style='width: 80%'>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "</tr>";

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

echo "</table>";

// Close database resources.
$result->free();
$mysqli->close();
?>

<p><a href="index.php">Home</a></p>

</body>
</html>
