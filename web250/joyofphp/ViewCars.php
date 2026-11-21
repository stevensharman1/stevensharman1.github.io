
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

    echo "<table style='border-collapse: collapse; width: 80%; margin: auto;'>";

    echo "<tr>";
    echo "<th style='padding: 8px 12px; text-align: left;'>Make</th>";
    echo "<th style='padding: 8px 12px; text-align: left;'>Model</th>";
    echo "<th style='padding: 8px 12px; text-align: left;'>Asking Price</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";

        echo "<td style='padding: 8px 12px;'>" . htmlspecialchars($row['Make']) . "</td>";
        echo "<td style='padding: 8px 12px;'>" . htmlspecialchars($row['Model']) . "</td>";
        echo "<td style='padding: 8px 12px;'>$" . number_format((float)$row['ASKING_PRICE'], 2) . "</td>";

        echo "</tr>";
    }

    echo "</table>";

} else {
    echo "<p>No vehicles found.</p>";
}
?>

<p style="text-align: center; margin-top: 20px;">
    <a href="index.php">Home</a>
</p>

<?php
// Close the database connection.
if ($result) {
    $result->free();
}

$mysqli->close();
?>

</body>
</html>
