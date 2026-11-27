

<?php
/**
 * Car AppB - Secure Login
 */

session_start();
require_once '../config_db.php';

// Only accept login submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
    header('Location: ../index.php');
    exit;
}

// Verify the CSRF token.
if (
    !isset($_POST['csrf_token'], $_SESSION['carapp2_csrf_token']) ||
    !hash_equals($_SESSION['carapp2_csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Invalid security token. Please return to the login page and try again.');
}

// Read submitted credentials.
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['carapp2_message'] = 'Enter your username and password.';
    header('Location: ../index.php');
    exit;
}

// Look up the account.
$stmt = $mysqli->prepare(
    "SELECT id, username, password
     FROM carapp2_users
     WHERE username = ?
     LIMIT 1"
);

$stmt->bind_param('s', $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Verify the password hash.
if ($user && password_verify($password, $user['password'])) {

    // Prevent session fixation.
    session_regenerate_id(true);

    $_SESSION['carapp2_user_id'] = $user['id'];
    $_SESSION['carapp2_username'] = $user['username'];

    // Generate a fresh CSRF token.
    $_SESSION['carapp2_csrf_token'] = bin2hex(random_bytes(32));

    $_SESSION['carapp2_message'] = 'Login successful. Welcome!';

    header('Location: ../index.php');
    exit;
}

// Use a generic error message.
$_SESSION['carapp2_message'] = 'Invalid username or password.';

header('Location: ../index.php');
exit;
