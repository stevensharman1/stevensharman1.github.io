
<?php
/**
 * Joy of PHP - Sam's Used Cars
 * Adds the Primary_Image column to the inventory table.
 */

include 'db.php';

echo "<h1>Sam's Used Cars - Database Update</h1>";

// Check whether the Primary_Image column already exists.
$check = $mysqli->query("SHOW COLUMNS FROM inventory LIKE 'Primary_Image'");

if (!$check) {
    die("Error checking inventory table: " .
        htmlspecialchars($mysqli->error));
}

if ($check->num_rows > 0) {
    echo "<p>The Primary_Image column already exists.</p>";
} else {

    // Add the Primary_Image column.
    $query = "ALTER TABLE inventory
              ADD Primary_Image VARCHAR(250) NULL AFTER SALE_DATE";

    if ($mysqli->query($query)) {
        echo "<p>The Primary_Image column was successfully added.</p>";
    } else {
        echo "<p>Error modifying inventory: " .
             htmlspecialchars($mysqli->error) . "</p>";
    }
}

$mysqli->close();
?>

<p><a href="index.php">Home</a></p>
