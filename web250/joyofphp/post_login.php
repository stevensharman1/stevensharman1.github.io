
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
$query = "SELECT username, password, role FROM Users WHERE username = ?";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("Database query failed.");
}

$stmt->bind_param("s", $myusername);
$stmt->execute();

// Retrieve the account.
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify the submitted password.
    if (password_verify($mypassword, $user['password'])) {

        // Generate a new session ID.
        session_regenerate_id(true);

        // Store login information.
        $_SESSION['MyUserName'] = $user['username'];
        $_SESSION['Role'] = $user['role'];

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
