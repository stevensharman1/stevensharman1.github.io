
<?php
/**
 * Joy of PHP
 * Processes the Sam's Used Cars login form.
 */

// Display PHP errors while troubleshooting.
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start the session.
session_start();

// Include database connection.
require_once 'db.php';

// Make sure the form was submitted.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Please submit the login form.");
}

// Retrieve form values.
$myusername = trim($_POST['myusername'] ?? '');
$mypassword = $_POST['mypassword'] ?? '';

// Check required fields.
if ($myusername === '' || $mypassword === '') {
    exit("Username and password are required. <a href='login2.php'>Try again</a>");
}

// Look up the username.
$query = "SELECT username, password, role FROM users WHERE username = ?";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    exit("Database query failed: " . htmlspecialchars($mysqli->error));
}

// Bind username parameter.
$stmt->bind_param("s", $myusername);

// Execute query.
if (!$stmt->execute()) {
    exit("Query execution failed: " . htmlspecialchars($stmt->error));
}

// Retrieve account information.
$stmt->bind_result($username, $hashedPassword, $role);

// Check whether the account exists.
if ($stmt->fetch()) {

    // Verify the submitted password.
    if (password_verify($mypassword, $hashedPassword)) {

        // Generate a new session ID.
        session_regenerate_id(true);

        // Store login information.
        $_SESSION['MyUserName'] = $username;
        $_SESSION['Role'] = $role;

        // Close database resources.
        $stmt->close();
        $mysqli->close();

        // Redirect to successful login page.
        header("Location: login_success.php");
        exit();
    }
}

// Login failed.
$stmt->close();
$mysqli->close();

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head><meta charset='UTF-8'><title>Login Failed</title></head>";
echo "<body>";
echo "<h1>Login Failed</h1>";
echo "<p>Invalid username or password.</p>";
echo "<p><a href='login2.php'>Try Again</a></p>";
echo "</body>";
echo "</html>";
?>
