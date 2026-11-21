<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'db.php';
<?php
/**
 * Joy of PHP
 * Processes the Sam's Used Cars login form.
 */

session_start();

// Include database connection.
require_once 'db.php';

// Retrieve form values.
$myusername = trim($_POST['myusername'] ?? '');
$mypassword = $_POST['mypassword'] ?? '';

// Check that both fields were submitted.
if ($myusername === '' || $mypassword === '') {
    die("Username and password are required. <a href='login2.php'>Try again</a>");
}

// Look up the username.
// Use lowercase users to match the table name.
$query = "SELECT username, password, role FROM users WHERE username = ?";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("Database query failed: " . htmlspecialchars($mysqli->error));
}

$stmt->bind_param("s", $myusername);
$stmt->execute();

// Retrieve account information without get_result().
$stmt->bind_result($username, $hashedPassword, $role);

if ($stmt->fetch()) {

    // Verify the submitted password.
    if (password_verify($mypassword, $hashedPassword)) {

        // Generate a new session ID.
        session_regenerate_id(true);

        // Store login information.
        $_SESSION['MyUserName'] = $username;
        $_SESSION['Role'] = $role;

        $stmt->close();
        $mysqli->close();

        // Redirect after successful login.
        header("Location: login_success.php");
        exit();
    }
}

// Display a general error message.
echo "<h2>Login Failed</h2>";
echo "<p>Invalid username or password.</p>";
echo "<p><a href='login2.php'>Try Again</a></p>";

// Close database resources.
$stmt->close();
$mysqli->close();
?>
