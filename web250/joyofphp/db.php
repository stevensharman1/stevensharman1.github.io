
<?php
// Use Wasmer environment variables for the database connection.
$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USERNAME');
$dbpass = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');
$dbport = getenv('DB_PORT');

// Check that the database settings exist.
if (!$dbhost || !$dbuser || !$dbname) {
    die("Database configuration is missing.");
}

// Connect to MySQL.
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, (int)$dbport);

// Check the connection.
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Set character encoding.
$mysqli->set_charset("utf8mb4");
?>
