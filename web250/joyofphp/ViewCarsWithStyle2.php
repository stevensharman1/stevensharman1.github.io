
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sam's Used Cars - Current Inventory</title>

    <style>
        /* Format the inventory table. */
        #Grid {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            width: 80%;
            border-collapse: collapse;
            margin-left: auto;
            margin-right: auto;
        }

        #Grid td, #Grid th {
            font-size: 1em;
            border: 1px solid #61ADD7;
            padding: 3px 7px 2px 7px;
        }

        #Grid th {
            font-size: 1.1em;
            text-align: left;
            padding-top: 5px;
            padding-bottom: 4px;
            background-color: #C2D9FE;
            color: lightslategray;
        }

        #Grid tr.odd td {
            color: #000000;
            background-color: #F2F5A9;
        }

        #Grid tr.even td {
            color: #000000;
            background-color: white;
        }

        .auto-style1 {
            text-align: center;
        }
    </style>
</head>

<body>

<h1>Sam's Used Cars</h1>
<h3>Current Inventory</h3>

<div class="auto-style1">

<?php
// Connect to the database.
include 'db.php';

// Get all vehicles from inventory.
$query = "SELECT VIN, Make, Model, ASKING_PRICE
          FROM inventory
          ORDER BY Make";

$result = $mysqli->query($query);

if (!$result) {
    die("<p>Error getting cars from the database: " .
        htmlspecialchars($mysqli->error) . "</p>");
}

// Create the table headers.
echo "<table id='Grid'>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "<th>Action</th>";
echo "</tr>";

$class = "odd";

// Display each vehicle.
if ($result->num_rows > 0) {

    while ($result_ar = $result->fetch_assoc()) {

        $vin = urlencode($result_ar['VIN']);

        echo "<tr class='$class'>";

        echo "<td><a href='viewcar.php?VIN=" . $vin . "'>" .
             htmlspecialchars($result_ar['Make']) .
             "</a></td>";

        echo "<td>" .
             htmlspecialchars($result_ar['Model']) .
             "</td>";

        echo "<td>$" .
             number_format((float)$result_ar['ASKING_PRICE'], 0) .
             "</td>";

        echo "<td>";
        echo "<a href='FormEdit.php?VIN=" . $vin . "'>Edit</a> | ";
        echo "<a href='deletecar.php?VIN=" . $vin . "'>Delete</a>";
        echo "</td>";

        echo "</tr>";

        // Alternate row colors.
        $class = ($class == "odd") ? "even" : "odd";
    }

} else {
    echo "<tr><td colspan='4'>No vehicles found.</td></tr>";
}

echo "</table>";

// Close the database connection.
$result->free();
$mysqli->close();
?>

</div>

<?php include 'footer.php'; ?>

</body>
</html>
