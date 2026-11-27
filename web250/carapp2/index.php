
<?php
/**
 * Steven Sharman's Sneaky Snake - Car AppB
 * Used Cars Inventory
 *
 * Public visitors can view inventory and log in.
 * Logged-in users can add, edit, and delete vehicles.
 */

// Start the session and connect to the database.
session_start();
require_once 'config_db.php';

// Create a CSRF token for secure forms.
if (empty($_SESSION['carapp2_csrf_token'])) {
    $_SESSION['carapp2_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['carapp2_csrf_token'];
$isLoggedIn = isset($_SESSION['carapp2_user_id']);
$message = $_SESSION['carapp2_message'] ?? '';
unset($_SESSION['carapp2_message']);

$editCar = null;

// Safely display text from the database or user input.
function escapeValue($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Validate a date in YYYY-MM-DD format.
function validDate($date) {
    if ($date === null || $date === '') {
        return true;
    }

    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
        return false;
    }

    return checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
}

// Handle vehicle changes.
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    (isset($_POST['save']) || isset($_POST['delete']))) {

    // Only logged-in users can change inventory.
    if (!$isLoggedIn) {
        http_response_code(403);
        exit('You must log in to manage vehicles.');
    }

    // Verify the CSRF token.
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($csrfToken, $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Invalid security token. Please reload the page.');
    }

    // Delete a vehicle.
    if (isset($_POST['delete'])) {
        $vin = trim($_POST['VIN'] ?? '');

        $stmt = $mysqli->prepare(
            "DELETE FROM carapp2_inventory WHERE VIN = ?"
        );

        $stmt->bind_param('s', $vin);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['carapp2_message'] = 'Vehicle deleted successfully.';
        } else {
            $_SESSION['carapp2_message'] = 'Vehicle could not be deleted.';
        }

        $stmt->close();

        header('Location: index.php');
        exit;
    }

    // Add or update a vehicle.
    if (isset($_POST['save'])) {
        $vin = strtoupper(trim($_POST['VIN'] ?? ''));
        $year = (int)($_POST['YEAR'] ?? 0);
        $make = trim($_POST['Make'] ?? '');
        $model = trim($_POST['Model'] ?? '');
        $trim = trim($_POST['TRIM'] ?? '');
        $extColor = trim($_POST['EXT_COLOR'] ?? '');
        $intColor = trim($_POST['INT_COLOR'] ?? '');
        $askingPrice = (float)($_POST['ASKING_PRICE'] ?? 0);
        $salePrice = (float)($_POST['SALE_PRICE'] ?? 0);
        $purchasePrice = (float)($_POST['PURCHASE_PRICE'] ?? 0);
        $mileage = (int)($_POST['MILEAGE'] ?? 0);
        $transmission = trim($_POST['TRANSMISSION'] ?? '');
        $purchaseDate = trim($_POST['PURCHASE_DATE'] ?? '');
        $saleDate = trim($_POST['SALE_DATE'] ?? '');
        $originalVIN = trim($_POST['ORIGINAL_VIN'] ?? '');

        $purchaseDate = $purchaseDate === '' ? null : $purchaseDate;
        $saleDate = $saleDate === '' ? null : $saleDate;

        // Validate the submitted vehicle information.
        $valid =
            strlen($vin) === 17 &&
            $year >= 1900 &&
            $make !== '' &&
            $model !== '' &&
            $askingPrice >= 0 &&
            $salePrice >= 0 &&
            $purchasePrice >= 0 &&
            $mileage >= 0 &&
            validDate($purchaseDate) &&
            validDate($saleDate);

        if (!$valid) {
            $_SESSION['carapp2_message'] =
                'Please check the vehicle information. VIN must be 17 characters, and dates must use YYYY-MM-DD.';
        } else {
            if ($originalVIN !== '') {
                // Update an existing vehicle.
                $stmt = $mysqli->prepare(
                    "UPDATE carapp2_inventory SET
                        VIN = ?, YEAR = ?, Make = ?, Model = ?, TRIM = ?,
                        EXT_COLOR = ?, INT_COLOR = ?, ASKING_PRICE = ?,
                        SALE_PRICE = ?, PURCHASE_PRICE = ?, MILEAGE = ?,
                        TRANSMISSION = ?, PURCHASE_DATE = ?, SALE_DATE = ?
                     WHERE VIN = ?"
                );

                $stmt->bind_param(
                    'sisssssdddissss',
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
                    $saleDate,
                    $originalVIN
                );

                $successMessage = 'Vehicle updated successfully.';
            } else {
                // Add a new vehicle.
                $stmt = $mysqli->prepare(
                    "INSERT INTO carapp2_inventory (
                        VIN, YEAR, Make, Model, TRIM,
                        EXT_COLOR, INT_COLOR, ASKING_PRICE,
                        SALE_PRICE, PURCHASE_PRICE, MILEAGE,
                        TRANSMISSION, PURCHASE_DATE, SALE_DATE
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
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

                $successMessage = 'Vehicle added successfully.';
            }

            if ($stmt->execute()) {
                $_SESSION['carapp2_message'] = $successMessage;
            } else {
                $_SESSION['carapp2_message'] =
                    'Database error: ' . $stmt->error;
            }

            $stmt->close();
        }

        header('Location: index.php');
        exit;
    }
}

// Load a vehicle for editing, but only for logged-in users.
if ($isLoggedIn && isset($_GET['edit'])) {
    $vinToEdit = trim($_GET['edit']);

    $stmt = $mysqli->prepare(
        "SELECT * FROM carapp2_inventory WHERE VIN = ?"
    );

    $stmt->bind_param('s', $vinToEdit);
    $stmt->execute();

    $editResult = $stmt->get_result();
    $editCar = $editResult->fetch_assoc();

    $stmt->close();
}

// Set up pagination.
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));

$countResult = $mysqli->query(
    "SELECT COUNT(*) AS total FROM carapp2_inventory"
);

$totalCars = (int)$countResult->fetch_assoc()['total'];
$totalPages = max(1, (int)ceil($totalCars / $perPage));

$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// Retrieve the current page of inventory.
$stmt = $mysqli->prepare(
    "SELECT * FROM carapp2_inventory
     ORDER BY Make, Model
     LIMIT ? OFFSET ?"
);

$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman’s Sneaky Snake ✦ WEB250 ✦ Car AppB</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="styles/default.css">
    <link rel="icon" href="../images/favicon.svg" type="image/svg+xml">

    <!-- Accumulus Validator -->
    <script src="https://lint.page/kit/4d0fe3.js"
            crossorigin="anonymous"></script>
</head>

<body>

<header>
    <h1>Steven Sharman’s Sneaky Snake ✦ WEB250</h1>

    <img src="../images/favicon.svg"
         alt="Sneaky Snake logo"
         class="site-logo">

    <nav>
        <a href="index.php">Car AppB Home</a> |
        <a href="../carapp/index.php">Car AppA</a> |
        <a href="../index.php">WEB250 Home</a>
    </nav>
</header>

<main>

    <h2>Used Cars Inventory - Car AppB</h2>

    <?php if ($message !== ''): ?>
        <p class="message">
            <?= escapeValue($message) ?>
        </p>
    <?php endif; ?>

    <section>
        <h3>Account</h3>

        <?php if ($isLoggedIn): ?>

            <p>
                You are logged in as
                <strong><?= escapeValue($_SESSION['carapp2_username'] ?? 'User') ?></strong>.
            </p>

            <p>
                <a href="scripts/logout.php">Log Out</a>
            </p>

            <p>
                <a href="scripts/setup.php">
                    Reset and Rebuild Car AppB Database
                </a>
            </p>

        <?php else: ?>

            <p>Visitors can view the inventory. Log in to manage vehicles.</p>

            <form method="post" action="scripts/login.php">
                <input type="hidden"
                       name="csrf_token"
                       value="<?= escapeValue($csrfToken) ?>">

                <label for="username">Username:</label>
                <input id="username"
                       name="username"
                       type="text"
                       autocomplete="username"
                       required>

                <label for="password">Password:</label>
                <input id="password"
                       name="password"
                       type="password"
                       autocomplete="current-password"
                       required>

                <button type="submit" name="login">Log In</button>
            </form>

        <?php endif; ?>
    </section>

    <?php if ($isLoggedIn): ?>

        <section>
            <h3><?= $editCar ? 'Edit Vehicle' : 'Add a Vehicle' ?></h3>

            <form method="post" action="index.php">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= escapeValue($csrfToken) ?>">

                <input type="hidden"
                       name="ORIGINAL_VIN"
                       value="<?= escapeValue($editCar['VIN'] ?? '') ?>">

                <label for="VIN">VIN:</label>
                <input id="VIN"
                       name="VIN"
                       type="text"
                       minlength="17"
                       maxlength="17"
                       required
                       value="<?= escapeValue($editCar['VIN'] ?? '') ?>">

                <label for="YEAR">Year:</label>
                <input id="YEAR"
                       name="YEAR"
                       type="number"
                       min="1900"
                       required
                       value="<?= escapeValue($editCar['YEAR'] ?? '') ?>">

                <label for="Make">Make:</label>
                <input id="Make"
                       name="Make"
                       type="text"
                       required
                       value="<?= escapeValue($editCar['Make'] ?? '') ?>">

                <label for="Model">Model:</label>
                <input id="Model"
                       name="Model"
                       type="text"
                       required
                       value="<?= escapeValue($editCar['Model'] ?? '') ?>">

                <label for="TRIM">Trim:</label>
                <input id="TRIM"
                       name="TRIM"
                       type="text"
                       value="<?= escapeValue($editCar['TRIM'] ?? '') ?>">

                <label for="EXT_COLOR">Exterior Color:</label>
                <input id="EXT_COLOR"
                       name="EXT_COLOR"
                       type="text"
                       value="<?= escapeValue($editCar['EXT_COLOR'] ?? '') ?>">

                <label for="INT_COLOR">Interior Color:</label>
                <input id="INT_COLOR"
                       name="INT_COLOR"
                       type="text"
                       value="<?= escapeValue($editCar['INT_COLOR'] ?? '') ?>">

                <label for="ASKING_PRICE">Asking Price:</label>
                <input id="ASKING_PRICE"
                       name="ASKING_PRICE"
                       type="number"
                       min="0"
                       step="0.01"
                       required
                       value="<?= escapeValue($editCar['ASKING_PRICE'] ?? '0') ?>">

                <label for="SALE_PRICE">Sale Price:</label>
                <input id="SALE_PRICE"
                       name="SALE_PRICE"
                       type="number"
                       min="0"
                       step="0.01"
                       value="<?= escapeValue($editCar['SALE_PRICE'] ?? '0') ?>">

                <label for="PURCHASE_PRICE">Purchase Price:</label>
                <input id="PURCHASE_PRICE"
                       name="PURCHASE_PRICE"
                       type="number"
                       min="0"
                       step="0.01"
                       value="<?= escapeValue($editCar['PURCHASE_PRICE'] ?? '0') ?>">

                <label for="MILEAGE">Mileage:</label>
                <input id="MILEAGE"
                       name="MILEAGE"
                       type="number"
                       min="0"
                       value="<?= escapeValue($editCar['MILEAGE'] ?? '0') ?>">

                <label for="TRANSMISSION">Transmission:</label>
                <input id="TRANSMISSION"
                       name="TRANSMISSION"
                       type="text"
                       value="<?= escapeValue($editCar['TRANSMISSION'] ?? '') ?>">

                <label for="PURCHASE_DATE">Purchase Date:</label>
                <input id="PURCHASE_DATE"
                       name="PURCHASE_DATE"
                       type="text"
                       placeholder="YYYY-MM-DD"
                       value="<?= escapeValue($editCar['PURCHASE_DATE'] ?? '') ?>">

                <label for="SALE_DATE">Sale Date:</label>
                <input id="SALE_DATE"
                       name="SALE_DATE"
                       type="text"
                       placeholder="YYYY-MM-DD"
                       value="<?= escapeValue($editCar['SALE_DATE'] ?? '') ?>">

                <button type="submit" name="save">
                    <?= $editCar ? 'Update Vehicle' : 'Add Vehicle' ?>
                </button>

                <?php if ($editCar): ?>
                    <a href="index.php">Cancel Edit</a>
                <?php endif; ?>

            </form>
        </section>

    <?php endif; ?>

    <section>
        <h3>Current Inventory</h3>

        <p>
            Showing <?= $totalCars === 0 ? 0 : $offset + 1 ?>
            through <?= min($offset + $perPage, $totalCars) ?>
            of <?= $totalCars ?> vehicles.
        </p>

        <?php if ($totalCars > 0): ?>

            <div class="table-wrapper">

                <table id="Grid">

                    <thead>
                        <tr>
                            <th>VIN</th>
                            <th>Year</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Trim</th>
                            <th>Exterior Color</th>
                            <th>Interior Color</th>
                            <th>Asking Price</th>
                            <th>Sale Price</th>
                            <th>Purchase Price</th>
                            <th>Mileage</th>
                            <th>Transmission</th>
                            <th>Purchase Date</th>
                            <th>Sale Date</th>

                            <?php if ($isLoggedIn): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>

                        <?php $rowNumber = 0; ?>

                        <?php while ($car = $result->fetch_assoc()): ?>

                            <tr class="<?= $rowNumber++ % 2 === 0 ? 'odd' : 'even' ?>">

                                <td><?= escapeValue($car['VIN']) ?></td>
                                <td><?= escapeValue($car['YEAR']) ?></td>
                                <td><?= escapeValue($car['Make']) ?></td>
                                <td><?= escapeValue($car['Model']) ?></td>
                                <td><?= escapeValue($car['TRIM']) ?></td>
                                <td><?= escapeValue($car['EXT_COLOR']) ?></td>
                                <td><?= escapeValue($car['INT_COLOR']) ?></td>

                                <td>$<?= number_format((float)$car['ASKING_PRICE'], 2) ?></td>
                                <td>$<?= number_format((float)$car['SALE_PRICE'], 2) ?></td>
                                <td>$<?= number_format((float)$car['PURCHASE_PRICE'], 2) ?></td>

                                <td><?= number_format((int)$car['MILEAGE']) ?></td>
                                <td><?= escapeValue($car['TRANSMISSION']) ?></td>
                                <td><?= escapeValue($car['PURCHASE_DATE']) ?></td>
                                <td><?= escapeValue($car['SALE_DATE']) ?></td>

                                <?php if ($isLoggedIn): ?>

                                    <td>
                                        <a href="index.php?edit=<?= urlencode($car['VIN']) ?>">
                                            Edit
                                        </a>

                                        <form method="post"
                                              action="index.php"
                                              onsubmit="return confirm('Are you sure you want to delete this vehicle?');">

                                            <input type="hidden"
                                                   name="csrf_token"
                                                   value="<?= escapeValue($csrfToken) ?>">

                                            <input type="hidden"
                                                   name="VIN"
                                                   value="<?= escapeValue($car['VIN']) ?>">

                                            <button type="submit" name="delete">
                                                Delete
                                            </button>

                                        </form>
                                    </td>

                                <?php endif; ?>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

            <nav aria-label="Inventory pages">

                <?php if ($page > 1): ?>
                    <a href="index.php?page=<?= $page - 1 ?>">Previous</a>
                <?php endif; ?>

                Page <?= $page ?> of <?= $totalPages ?>

                <?php if ($page < $totalPages): ?>
                    <a href="index.php?page=<?= $page + 1 ?>">Next</a>
                <?php endif; ?>

            </nav>

        <?php else: ?>

            <p>No vehicles are currently in the inventory.</p>

        <?php endif; ?>
    </section>

</main>

<footer>
    <p><em>"Slow and steady in quality, fast in learning."</em></p>
    <p>Page built by Steven Sharman's Sneaky Snake.</p>
    <p>WEB250 Web Development - Car AppB</p>

    <p>
        <a href="https://validator.w3.org/check?uri=referer"
           target="_blank"
           rel="noopener">HTML Validator</a>
        |
        <a href="https://jigsaw.w3.org/css-validator/check/referer"
           target="_blank"
           rel="noopener">CSS Validator</a>
    </p>
</footer>

<!-- Vicunadator Validator -->
<script src="https://divonbriesen.github.io/teaching/tools/standards-check.js"
        defer></script>

<?php
$stmt->close();
$mysqli->close();
?>

</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
