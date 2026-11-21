
<?php
session_start();

// Check whether the user has an active login session.
if (!isset($_SESSION['MyUserName'])) {
    header("Location: login2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful</title>
</head>
<body>

    <h1>Login Successful</h1>

    <p>Welcome, <?php echo htmlspecialchars($_SESSION['MyUserName']); ?>!</p>

    <p><a href="samsusedcars.php">Continue to Sam's Used Cars</a></p>

</body>
</html>
