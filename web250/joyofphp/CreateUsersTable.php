
<?php
/**
 * Creates the Users table for Sam's Used Cars.
 */

require_once 'db.php';

echo "<h1>Sam's Used Cars - Users Setup</h1>";

// Create the Users table if it does not exist.
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL
)";

if ($mysqli->query($query)) {
    echo "<p>Users table is ready.</p>";
} else {
    die("Error creating Users table: " . htmlspecialchars($mysqli->error));
}

// Check whether Sam's account already exists.
$username = "Sam";

$stmt = $mysqli->prepare(
    "SELECT id FROM users WHERE username = ?"
);

$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {

    // Create a hashed password.
    $password = password_hash("pw123", PASSWORD_DEFAULT);
    $role = "Owner";

    $insert = $mysqli->prepare(
        "INSERT INTO users (username, password, role)
         VALUES (?, ?, ?)"
    );

    $insert->bind_param("sss", $username, $password, $role);

    if ($insert->execute()) {
        echo "<p>Sam's account was created successfully.</p>";
    } else {
        echo "<p>Account creation failed.</p>";
    }

    $insert->close();

} else {
    echo "<p>Sam's account already exists.</p>";
}

$stmt->close();
$mysqli->close();

echo "<p><a href='login2.php'>Go to Login</a></p>";
?>
