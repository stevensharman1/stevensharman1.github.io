
<?php
/**
 * Steven Sharman's Sneaky Snake's Used Cars
 * Database Configuration
 */

// Get database settings from Wasmer.
$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USERNAME');
$dbpass = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$dbport = getenv('DB_PORT');

// Check database settings.
if (!$dbhost || !$dbuser || !$dbname) {
    die("Database configuration is missing.");
}

// Connect to the existing database.
$mysqli = new mysqli(
    $dbhost,
    $dbuser,
    $dbpass,
    $dbname,
    (int)$dbport
);

// Check the connection.
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Set character encoding.
$mysqli->set_charset("utf8mb4");
?>
