 
<?php
// Database connection settings.
$local = true;
$docker = false;

if ($local) {
    $dbuser = 'root';
    $dbpass = 'root';
    $dbhost = 'localhost';
} elseif ($docker) {
    $dbuser = 'root';
    $dbpass = 'verysecret';
    $dbhost = 'mySQL';
}

// Connect to MySQL without selecting a database yet.
$mysqli = new mysqli($dbhost, $dbuser, $dbpass);

// Check the connection.
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Set the character encoding.
$mysqli->set_charset("utf8mb4");
?>
