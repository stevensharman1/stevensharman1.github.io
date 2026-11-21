
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joy of PHP - Creating the Users Table</title>

    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        p {
            color: #555;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

<img src="joy_logo.jpg" alt="The Joy of PHP" width="809" height="87">

<h2>Creating the 'users' table in the Cars database</h2>

<?php
/**
 * Joy of PHP sample code
 * Creates the users table and inserts Sam's account if it does not exist.
 */

include 'db.php';

try {

    // Create the users table if it does not already exist.
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT,
            username VARCHAR(65) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(65) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_username (username)
        )
    ";

    if ($mysqli->query($createTableQuery)) {
        echo "<p class='success'>The users table is ready.</p>";
    } else {
        throw new Exception("Error creating users table: " . $mysqli->error);
    }

    // Check whether Sam's account already exists.
    $username = "Sam";

    $checkQuery = $mysqli->prepare(
        "SELECT id FROM users WHERE username = ?"
    );

    if (!$checkQuery) {
        throw new Exception("Error preparing username check.");
    }

    $checkQuery->bind_param("s", $username);
    $checkQuery->execute();
    $checkQuery->store_result();

    if ($checkQuery->num_rows === 0) {

        // Create Sam's account if it does not exist.
        $password = password_hash("pw123", PASSWORD_DEFAULT);
        $role = "Owner";

        $insertQuery = $mysqli->prepare(
            "INSERT INTO users (username, password, role)
             VALUES (?, ?, ?)"
        );

        if (!$insertQuery) {
            throw new Exception("Error preparing user insert.");
        }

        $insertQuery->bind_param("sss", $username, $password, $role);

        if ($insertQuery->execute()) {
            echo "<p class='success'>Sam's account was created successfully.</p>";
        } else {
            throw new Exception("Error inserting Sam's account.");
        }

        $insertQuery->close();

    } else {
        echo "<p class='success'>Sam's account already exists.</p>";
    }

    $checkQuery->close();

} catch (Exception $e) {
    echo "<p class='error'>An error occurred: " .
         htmlspecialchars($e->getMessage()) . "</p>";
}

$mysqli->close();
?>

<?php include 'footer.php'; ?>

</body>
</html>
