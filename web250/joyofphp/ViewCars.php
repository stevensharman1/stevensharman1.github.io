
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sam's Used Cars - Complete Inventory</title>
</head>

<body>

<h1>Sam's Used Cars</h1>
<h3>Complete Inventory</h3>

<?php
// Connect to the database.
include 'db.php';

// Get all vehicles, ordered by Make.
$query = "SELECT * FROM inventory ORDER BY Make";

$result = $mysqli->query($query);

if (!$result) {
    die("<p>Error getting cars from the database: " .
        htmlspecialchars($mysqli->error) . "</p>");
}

// Display the inventory table.
echo "<table id='Grid' style='width: 80%'>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "</tr>";

$class = "odd";

if ($result->num_rows > 0) {

    while ($result_ar = $result->fetch_assoc()) {

        echo "<tr class='$class'>";

        echo "<td>" .
             htmlspecialchars($result_ar['Make']) .
             "</td>";

        echo "<td>" .
             htmlspecialchars($result_ar['Model']) .
             "</td>";

        echo "<td>$" .
             htmlspecialchars($result_ar['ASKING_PRICE']) .
             "</td>";

        echo "</tr>";

        // Alternate row classes.
        $class = ($class == "odd") ? "even" : "odd";
    }

} else {
    echo "<tr><td colspan='3'>No vehicles found.</td></tr>";
}

echo "</table>";

// Close the database connection.
$mysqli->close();
?>

<p><a href="index.php">Home</a></p>

</body>
</html>
