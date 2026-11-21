
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sam's Used Cars - Delete Vehicle</title>
</head>

<body>

<h1>Sam's Used Cars</h1>

<?php
// Include the database connection.
include 'db.php';

// Get the VIN from the URL.
$vin = trim($_GET['VIN'] ?? '');

if ($vin === '') {

    echo "<p>No vehicle VIN was provided.</p>";

} else {

    // Prepare the delete query.
    $query = "DELETE FROM inventory WHERE VIN = ?";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        echo "<p>Database error: " .
             htmlspecialchars($mysqli->error) . "</p>";

    } else {

        // Bind the VIN and execute the query.
        $stmt->bind_param("s", $vin);

        if ($stmt->execute()) {

            if ($stmt->affected_rows > 0) {
                echo "<p>The vehicle with VIN " .
                     htmlspecialchars($vin) .
                     " has been deleted successfully.</p>";
            } else {
                echo "<p>Sorry, no vehicle was found with VIN " .
                     htmlspecialchars($vin) . ".</p>";
            }

        } else {
            echo "<p>Unable to delete the vehicle: " .
                 htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    }
}

// Close the database connection.
$mysqli->close();
?>

<p>
    <a href="ViewCarsWithStyle2.php">Return to Current Inventory</a>
</p>

</body>
</html>
