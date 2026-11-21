
<?php
// Connect to the database.
require_once 'db.php';

// Get the vehicle inventory.
$query = "SELECT Make, Model, ASKING_PRICE FROM inventory ORDER BY Make";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Car Inventory</title>
</head>
<body>

<h1>Sam's Used Cars</h1>
<h2>Basic Car Inventory</h2>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>";

    echo "<tr>";
    echo "<th>Make</th>";
    echo "<th>Model</th>";
    echo "<th>Asking Price</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";

        echo "<td>" . htmlspecialchars($row['Make']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Model']) . "</td>";
        echo "<td>$" . number_format((float)$row['ASKING_PRICE'], 2) . "</td>";

        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No vehicles found.</p>";
}
?>

<p><a href="index.php">Home</a></p>

<?php
// Close the database connection.
if ($result) {
    $result->free();
}

$mysqli->close();
?>

</body>
</html>
