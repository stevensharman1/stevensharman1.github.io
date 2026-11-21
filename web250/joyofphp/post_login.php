
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Please submit the login form.");
}

$myusername = trim($_POST['myusername'] ?? '');
$mypassword = $_POST['mypassword'] ?? '';

if ($myusername === '' || $mypassword === '') {
    exit("Username and password are required.");
}

$query = "SELECT username, password, role FROM users WHERE username = ?";

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    exit("Query error: " . htmlspecialchars($mysqli->error));
}

$stmt->bind_param("s", $myusername);

if (!$stmt->execute()) {
    exit("Execution error: " . htmlspecialchars($stmt->error));
}

$stmt->bind_result($username, $hashedPassword, $role);

if ($stmt->fetch() && password_verify($mypassword, $hashedPassword)) {

    session_regenerate_id(true);

    $_SESSION['MyUserName'] = $username;
    $_SESSION['Role'] = $role;

    $stmt->close();
    $mysqli->close();

    header("Location: login_success.php");
    exit();
}

$stmt->close();
$mysqli->close();

echo "<h2>Login Failed</h2>";
echo "<p>Invalid username or password.</p>";
echo "<a href='login2.php'>Try Again</a>";
?>
