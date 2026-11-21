
<?php
/**
 * Joy of PHP - Sam's Used Cars
 * Creates the Images table for vehicle pictures.
 */

include 'db.php';

echo "<h1>Sam's Used Cars - Image Setup</h1>";

// Create the Images table if it does not already exist.
$query = "CREATE TABLE IF NOT EXISTS IMAGES (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    VIN VARCHAR(17),
    ImageFile VARCHAR(250)
)";

if ($mysqli->query($query)) {
    echo "<p>Images table is ready.</p>";
} else {
    die("<p>Error creating Images table: " .
        htmlspecialchars($mysqli->error) . "</p>");
}

echo "<p><a href='index.php'>Home</a></p>";

$mysqli->close();
?>
