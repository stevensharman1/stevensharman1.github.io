
<?php
/**
 * Joy of PHP - Sam's Used Cars
 * Creates the inventory table and inserts initial vehicles.
 */

// Use the shared Wasmer database connection.
include 'db.php';

echo "<h1>Sam's Used Cars - Database Setup</h1>";

// Create the inventory table if it does not exist.
$query = "CREATE TABLE IF NOT EXISTS INVENTORY (
    VIN VARCHAR(17) PRIMARY KEY,
    YEAR INT,
    Make VARCHAR(50),
    Model VARCHAR(100),
    TRIM VARCHAR(50),
    EXT_COLOR VARCHAR(50),
    INT_COLOR VARCHAR(50),
    ASKING_PRICE DECIMAL(10,2),
    SALE_PRICE DECIMAL(10,2),
    PURCHASE_PRICE DECIMAL(10,2),
    MILEAGE INT,
    TRANSMISSION VARCHAR(50),
    PURCHASE_DATE DATE,
    SALE_DATE DATE
)";

if ($mysqli->query($query)) {
    echo "<p>Inventory table is ready.</p>";
} else {
    die("<p>Error creating inventory table: " .
        htmlspecialchars($mysqli->error) . "</p>");
}

// Insert the original Honda Pilot if its VIN is not already present.
$query = "INSERT IGNORE INTO INVENTORY
(VIN, YEAR, Make, Model, TRIM, EXT_COLOR, INT_COLOR,
 ASKING_PRICE, SALE_PRICE, PURCHASE_PRICE, MILEAGE,
 TRANSMISSION, PURCHASE_DATE, SALE_DATE)
VALUES
('5FNYF4H91CB054036', 2012, 'Honda', 'Pilot', 'Touring',
 'White Diamond Pearl', 'Leather', 37807, NULL, 34250,
 7076, 'Automatic', '2012-11-08', NULL)";

if ($mysqli->query($query)) {
    if ($mysqli->affected_rows > 0) {
        echo "<p>Honda Pilot added to inventory.</p>";
    } else {
        echo "<p>Honda Pilot already exists.</p>";
    }
} else {
    echo "<p>Error inserting Honda Pilot: " .
        htmlspecialchars($mysqli->error) . "</p>";
}

// Insert the original Dodge Durango if its VIN is not already present.
$query = "INSERT IGNORE INTO INVENTORY
(VIN, YEAR, Make, Model, TRIM, EXT_COLOR, INT_COLOR,
 ASKING_PRICE, SALE_PRICE, PURCHASE_PRICE, MILEAGE,
 TRANSMISSION, PURCHASE_DATE, SALE_DATE)
VALUES
('LAKSDFJ234LASKRF2', 2009, 'Dodge', 'Durango', 'SLT',
 'Silver', 'Black', 2700, NULL, 2000,
 144000, '4WD Automatic', '2012-12-05', NULL)";

if ($mysqli->query($query)) {
    if ($mysqli->affected_rows > 0) {
        echo "<p>Dodge Durango added to inventory.</p>";
    } else {
        echo "<p>Dodge Durango already exists.</p>";
    }
} else {
    echo "<p>Error inserting Dodge Durango: " .
        htmlspecialchars($mysqli->error) . "</p>";
}

$mysqli->close();

include 'footer.php';
?>
