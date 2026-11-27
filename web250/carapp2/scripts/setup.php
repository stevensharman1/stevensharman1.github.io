
<?php
/**
 * Steven Sharman's Sneaky Snake - Car AppB
 * Database Setup and Reset
 *
 * Creates inventory and images tables.
 * Preserves the users table and existing accounts.
 */

session_start();
require_once '../config_db.php';

// Create the users table if it does not exist.
// This table is never dropped by this script.
$mysqli->query("
    CREATE TABLE IF NOT EXISTS carapp2_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        first_name VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Check whether any users already exist.
$userCountResult = $mysqli->query(
    "SELECT COUNT(*) AS total FROM carapp2_users"
);
$userCount = (int)$userCountResult->fetch_assoc()['total'];

// First installation is allowed when no accounts exist.
// Otherwise, only logged-in users can reset the database.
$firstInstallation = ($userCount === 0);
$isLoggedIn = isset($_SESSION['carapp2_user_id']);

if (!$firstInstallation && !$isLoggedIn) {
    http_response_code(403);
    exit('You must log in before accessing database setup.');
}

// Create a CSRF token.
if (empty($_SESSION['carapp2_csrf_token'])) {
    $_SESSION['carapp2_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['carapp2_csrf_token'];
$message = '';

// Handle confirmed reset.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($csrfToken, $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Invalid security token. Reload the page and try again.');
    }

    if (!$firstInstallation && !$isLoggedIn) {
        http_response_code(403);
        exit('You must log in before resetting the database.');
    }

    if (!isset($_POST['confirm_reset'])) {
        exit('Reset was not confirmed.');
    }

    try {
        $mysqli->begin_transaction();

        // Drop only the AppB inventory and images tables.
        // The users table is intentionally preserved.
        $mysqli->query("DROP TABLE IF EXISTS carapp2_images");

        $message .= 'Dropping all fields in images... ';

        $mysqli->query("DROP TABLE IF EXISTS carapp2_inventory");

        $message .= 'Dropping all fields in inventory... ';

        // Recreate inventory.
        $mysqli->query("
            CREATE TABLE carapp2_inventory (
                VIN VARCHAR(17) PRIMARY KEY,
                YEAR INT NOT NULL,
                Make VARCHAR(50) NOT NULL,
                Model VARCHAR(50) NOT NULL,
                TRIM VARCHAR(50),
                EXT_COLOR VARCHAR(50),
                INT_COLOR VARCHAR(50),
                ASKING_PRICE DECIMAL(10,2) DEFAULT 0,
                SALE_PRICE DECIMAL(10,2) DEFAULT 0,
                PURCHASE_PRICE DECIMAL(10,2) DEFAULT 0,
                MILEAGE INT DEFAULT 0,
                TRANSMISSION VARCHAR(50),
                PURCHASE_DATE DATE NULL,
                SALE_DATE DATE NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $message .= 'Recreating inventory table... ';

        // Recreate images.
        $mysqli->query("
            CREATE TABLE carapp2_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                VIN VARCHAR(17) NOT NULL,
                image_path VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $message .= 'Recreating images table... ';

        // Create the initial account only if it does not already exist.
        $username = 'web250';
        $passwordHash = password_hash('123!', PASSWORD_DEFAULT);
        $lastName = 'Sharman';
        $firstName = 'Steven';

        $userCheck = $mysqli->prepare(
            "SELECT id FROM carapp2_users WHERE username = ?"
        );
        $userCheck->bind_param('s', $username);
        $userCheck->execute();
        $existingUser = $userCheck->get_result()->fetch_assoc();
        $userCheck->close();

        if (!$existingUser) {
            $userInsert = $mysqli->prepare("
                INSERT INTO carapp2_users
                    (username, password, last_name, first_name)
                VALUES (?, ?, ?, ?)
            ");

            $userInsert->bind_param(
                'ssss',
                $username,
                $passwordHash,
                $lastName,
                $firstName
            );

            $userInsert->execute();
            $userInsert->close();
        }

        // Sample vehicle information.
        $vehicles = [
            ['Toyota', 'Camry', 'SE'],
            ['Honda', 'Accord', 'Sport'],
            ['Ford', 'Mustang', 'GT'],
            ['Chevrolet', 'Malibu', 'LT'],
            ['Nissan', 'Altima', 'SV'],
            ['Hyundai', 'Sonata', 'SEL'],
            ['Kia', 'K5', 'GT-Line'],
            ['Dodge', 'Charger', 'SXT'],
            ['Chrysler', '300', 'Touring'],
            ['BMW', '330i', 'Sport'],
            ['Mercedes-Benz', 'C300', '4MATIC'],
            ['Audi', 'A4', 'Premium'],
            ['Volkswagen', 'Jetta', 'SE'],
            ['Subaru', 'Legacy', 'Premium'],
            ['Mazda', 'Mazda6', 'Touring'],
            ['Lexus', 'ES 350', 'Base'],
            ['Acura', 'TLX', 'Technology'],
            ['Chevrolet', 'Camaro', 'LT'],
            ['Ford', 'Fusion', 'SE'],
            ['Toyota', 'Corolla', 'LE']
        ];

        $colors = [
            'Black', 'Silver', 'White', 'Gray', 'Blue',
            'Red', 'Pearl White', 'Dark Gray', 'Burgundy', 'Green'
        ];

        $interiorColors = [
            'Black', 'Gray', 'Beige', 'Tan', 'Charcoal'
        ];

        $transmissions = [
            'Automatic', 'Automatic', 'Automatic', 'Manual'
        ];

        // Insert 100 sample vehicles.
        $insert = $mysqli->prepare("
            INSERT INTO carapp2_inventory (
                VIN, YEAR, Make, Model, TRIM,
                EXT_COLOR, INT_COLOR, ASKING_PRICE,
                SALE_PRICE, PURCHASE_PRICE, MILEAGE,
                TRANSMISSION, PURCHASE_DATE, SALE_DATE
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        for ($i = 0; $i < 100; $i++) {

            $vehicle = $vehicles[$i % count($vehicles)];

            $vin = 'APPB' . str_pad(
                (string)($i + 1),
                13,
                '0',
                STR_PAD_LEFT
            );

            $year = 2016 + ($i % 11);
            $make = $vehicle[0];
            $model = $vehicle[1];
            $trim = $vehicle[2];

            $extColor = $colors[$i % count($colors)];
            $intColor = $interiorColors[$i % count($interiorColors)];

            $askingPrice = 9500 + (($i * 725) % 35000);
            $salePrice = 0;
            $purchasePrice = round($askingPrice * 0.78, 2);

            $mileage = 18000 + (($i * 2371) % 125000);
            $transmission = $transmissions[$i % count($transmissions)];

            $purchaseDate = date(
                'Y-m-d',
                strtotime('-' . (30 + $i * 3) . ' days')
            );

            $saleDate = null;

            $insert->bind_param(
                'sisssssdddisss',
                $vin,
                $year,
                $make,
                $model,
                $trim,
                $extColor,
                $intColor,
                $askingPrice,
                $salePrice,
                $purchasePrice,
                $mileage,
                $transmission,
                $purchaseDate,
                $saleDate
            );

            $insert->execute();
        }

        $insert->close();

        $mysqli->commit();

        // Refresh the session security token.
        $_SESSION['carapp2_csrf_token'] = bin2hex(random_bytes(32));

        $message .= 'Added 100 sample vehicles. Database setup completed successfully.';

        // Sign in the initial account after first installation.
        if ($firstInstallation) {
            $accountResult = $mysqli->query(
                "SELECT id, username FROM carapp2_users
                 WHERE username = 'web250' LIMIT 1"
            );

            $account = $accountResult->fetch_assoc();

            if ($account) {
                session_regenerate_id(true);
                $_SESSION['carapp2_user_id'] = $account['id'];
                $_SESSION['carapp2_username'] = $account['username'];
                $_SESSION['carapp2_csrf_token'] = bin2hex(random_bytes(32));
            }
        }

    } catch (Throwable $error) {
        $mysqli->rollback();

        $message = 'Setup failed: ' . $error->getMessage();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman’s Sneaky Snake ✦ WEB250 ✦ Car AppB Setup</title>

    <link rel="stylesheet" href="../styles/default.css">
    <link rel="icon" href="../../images/favicon.svg" type="image/svg+xml">

    <script src="https://lint.page/kit/4d0fe3.js"
            crossorigin="anonymous"></script>
</head>

<body>

<header>
    <h1>Car AppB Database Setup</h1>

    <nav>
        <a href="../index.php">Return to Car AppB</a>
    </nav>
</header>

<main>

    <section>
        <h2>Database Reset Confirmation</h2>

        <?php if ($message !== ''): ?>

            <p class="message">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <a href="../index.php">Return to inventory</a>
            </p>

        <?php else: ?>

            <p>
                <strong>Warning:</strong>
                This operation will delete and recreate the Car AppB
                inventory and images tables.
            </p>

            <p>
                All existing Car AppB vehicle and image records will be lost.
                User accounts will NOT be deleted.
            </p>

            <p>Well, you asked for it...</p>

            <form method="post" action="setup.php">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <label>
                    <input type="checkbox"
                           name="confirm_reset"
                           value="yes"
                           required>
                    I understand that the inventory and images tables will be reset.
                </label>

                <button type="submit">
                    Confirm Database Setup / Reset
                </button>

                <a href="../index.php">Cancel</a>

            </form>

        <?php endif; ?>

    </section>

</main>

<footer>
    <p>Designed by Sneaky Snake Productions</p>
    <p>Steven Sharman ©2026</p>
</footer>

<script src="https://divonbriesen.github.io/teaching/tools/standards-check.js"
        defer></script>

</body>
</html>

