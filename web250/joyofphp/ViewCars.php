<?php
require_once 'db.php';

$query = "SELECT Make, Model, ASKING_PRICE FROM inventory ORDER BY Make";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Inventory</title>
    <link rel="stylesheet" href="Grid.css">
</head>
<body>

<h1>Sam's Used Cars</h1>
<h2>Complete Inventory</h2>

<table id="Grid">
    <thead>
        <tr>
            <th>Make</th>
            <th>Model</th>
            <th>Asking Price</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $rowNumber = 0;

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $class = ($rowNumber % 2 == 0) ? "odd" : "even";

                echo "<tr class='$class'>";
                echo "<td>" . htmlspecialchars($row['Make']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Model']) . "</td>";
                echo "<td>$" . number_format((float)$row['ASKING_PRICE'], 2) . "</td>";
                echo "</tr>";

                $rowNumber++;
            }
        } else {
            echo "<tr><td colspan='3'>No vehicles found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<p style="text-align:center;">
    <a href="index.php">Home</a>
</p>

<?php
if ($result) {
    $result->free();
}
$mysqli->close();
?>

</body>
</html>
