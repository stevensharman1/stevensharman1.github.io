
<?php
/**
 * Steven Sharman's Sneaky Snake - Car AppB
 * Creates the database tables for Car AppB.
 *
 * IMPORTANT:
 * These table names are separate from Car AppA.
 * This script does not delete or change Car AppA's inventory.
 */

require_once '../config_db.php';

// Create Car AppB inventory table.
$inventorySQL = "
CREATE TABLE IF NOT EXISTS carapp2_inventory (
    VIN VARCHAR(17) PRIMARY KEY,
    YEAR INT NOT NULL,
    Make VARCHAR(50) NOT NULL,
    Model VARCHAR(50) NOT NULL,
    TRIM VARCHAR(50),
    EXT_COLOR VARCHAR(50),
    INT_COLOR VARCHAR(50),
    ASKING_PRICE DECIMAL(10,2) DEFAULT 0,
    SALE_PRICE DECIMAL(10,2) DEFAULT 0,
    PURCHASE_PRICE DECIMAL(10,2) DEFAULT 0,
    MILEAGE INT DEFAULT 0,
    TRANSMISSION VARCHAR(30),
    PURCHASE_DATE DATE,
    SALE_DATE DATE
)";

if (!$mysqli->query($inventorySQL)) {
    die("Inventory table error: " . $mysqli->error);
}

// Create Car AppB images table.
$imagesSQL = "
CREATE TABLE IF NOT EXISTS carapp2_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    VIN VARCHAR(17) NOT NULL,
    image_path VARCHAR(255) NOT NULL
)";

if (!$mysqli->query($imagesSQL)) {
    die("Images table error: " . $mysqli->error);
}

// Create Car AppB users table.
$usersSQL = "
CREATE TABLE IF NOT EXISTS carapp2_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL
)";

if (!$mysqli->query($usersSQL)) {
    die("Users table error: " . $mysqli->error);
}

echo "<h1>Car AppB Database Setup</h1>";
echo "<p>Car AppB inventory table is ready.</p>";
echo "<p>Car AppB images table is ready.</p>";
echo "<p>Car AppB users table is ready.</p>";
echo "<p>Car AppA's inventory table was not changed.</p>";
?>
