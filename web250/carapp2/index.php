
<?php
/**
 * Steven Sharman's Sneaky Snake - Car AppB
 * Used Cars Inventory
 */

session_start();
require_once 'config_db.php';

// Check whether a user is logged in.
$isLoggedIn = isset($_SESSION['carapp2_user_id']);

// Escape output before displaying it.
function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Create a CSRF token for protected actions.
if (empty($_SESSION['carapp2_csrf'])) {
    $_SESSION['carapp2_csrf'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['carapp2_csrf'];
$message = '';
$editCar = null;

// Check the CSRF token on POST requests.
function checkCsrf() {
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['carapp2_csrf'], $_POST['csrf_token'])
    ) {
        die('Invalid request. Please reload the page and try again.');
    }
}

// Process actions only for logged-in users.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    checkCsrf();

    // Delete a vehicle.
    if (isset($_POST['delete'])) {
        $vin = trim($_POST['VIN'] ?? '');

        $stmt = $mysqli->prepare(
            "DELETE FROM carapp2_inventory WHERE VIN = ?"
        );
        $stmt->bind_param("s", $vin);

        if ($stmt->execute()) {
            $message = "Vehicle deleted successfully.";
        } else {
            $message = "Unable to delete vehicle.";
        }

        $stmt->close();
    }

    // Add or update a vehicle.
    if (isset($_POST['save'])) {
        $vin = trim($_POST['VIN'] ?? '');
        $originalVin = trim($_POST['ORIGINAL_VIN'] ?? '');

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

        // Validate vehicle information.
        $validVin = strlen($vin) === 17;
        $validYear = $year >= 1900 && $year <= 2100;
        $validPrices = $askingPrice >= 0 &&
                       $salePrice >= 0 &&
                       $purchasePrice >= 0;
        $validMileage = $mileage >= 0;

        $validPurchaseDate = $purchaseDate === '' ||
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $purchaseDate);

        $validSaleDate = $saleDate === '' ||
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $saleDate);

        if (
            !$validVin ||
            !$validYear ||
            $make === '' ||
            $model === '' ||
            !$validPrices ||
            !$validMileage ||
            !$validPurchaseDate ||
            !$validSaleDate
        ) {
            $message = "Please check your vehicle information.";
        } else {
            $purchaseDate = $purchaseDate === '' ? null : $purchaseDate;
            $saleDate = $saleDate === '' ? null : $saleDate;

            if ($originalVin !== '') {
                // Update an existing vehicle.
                $stmt = $mysqli->prepare(
                    "UPDATE carapp2_inventory SET
                        VIN = ?,
                        YEAR = ?,
                        Make = ?,
                        Model = ?,
                        TRIM = ?,
                        EXT_COLOR = ?,
                        INT_COLOR = ?,
                        ASKING_PRICE = ?,
                        SALE_PRICE = ?,
                        PURCHASE_PRICE = ?,
                        MILEAGE = ?,
                        TRANSMISSION = ?,
                        PURCHASE_DATE = ?,
                        SALE_DATE = ?
                     WHERE VIN = ?"
                );

                $stmt->bind_param(
                    "sisssssdddis sss",
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
                    $originalVin
                );

                // Correct the parameter type string.
                $stmt->close();

                $stmt = $mysqli->prepare(
                    "UPDATE carapp2_inventory SET
                        VIN = ?, YEAR = ?, Make = ?, Model = ?,
                        TRIM = ?, EXT_COLOR = ?, INT_COLOR = ?,
                        ASKING_PRICE = ?, SALE_PRICE = ?,
                        PURCHASE_PRICE = ?, MILEAGE = ?,
                        TRANSMISSION = ?, PURCHASE_DATE = ?,
                        SALE_DATE = ?
                     WHERE VIN = ?"
                );

                $stmt->bind_param(
                    "sisssssdddis sss",
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
                    $originalVin
                );

                if ($stmt->execute()) {
                    $message = "Vehicle updated successfully.";
                } else {
                    $message = "Update failed: " . $stmt->error;
                }

                $stmt->close();
            } else {
                // Insert a new vehicle.
                $stmt = $mysqli->prepare(
                    "INSERT INTO carapp2_inventory (
                        VIN, YEAR, Make, Model, TRIM,
                        EXT_COLOR, INT_COLOR, ASKING_PRICE,
                        SALE_PRICE, PURCHASE_PRICE, MILEAGE,
                        TRANSMISSION, PURCHASE_DATE, SALE_DATE
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "sisssssdddis ss",
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

                if ($stmt->execute()) {
                    $message = "Vehicle added successfully.";
                } else {
                    $message = "Insert failed: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }
}

// Load a vehicle into the edit form.
if ($isLoggedIn && isset($_GET['edit'])) {
    $vin = trim($_GET['edit']);

    $stmt = $mysqli->prepare(
        "SELECT * FROM carapp2_inventory WHERE VIN = ?"
    );
    $stmt->bind_param("s", $vin);
    $stmt->execute();

    $result = $stmt->get_result();
    $editCar = $result->fetch_assoc();

    $stmt->close();
}

// Pagination.
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));

$countResult = $mysqli->query(
    "SELECT COUNT(*) AS total FROM carapp2_inventory"
);

$totalCars = (int)$countResult->fetch_assoc()['total'];
$totalPages = max(1, (int)ceil($totalCars / $perPage));

$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// Retrieve vehicles for the current page.
$stmt = $mysqli->prepare(
    "SELECT * FROM carapp2_inventory
     ORDER BY Make, Model
     LIMIT ? OFFSET ?"
);
$stmt->bind_param("ii", $perPage, $offset);
$stmt->execute();

$cars = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman's Sneaky Snake ✦ WEB250 ✦ Car AppB</title>

    <link rel="icon" href="../images/favicon.svg">
    <link rel="stylesheet" href="styles/default.css">

    <script src="https://lint.page/kit/4d0fe3.js" crossorigin="anonymous"></script>
</head>

<body>

<header>
    <h1>Steven Sharman's Sneaky Snake</h1>
    <h2>WEB250 - Car AppB</h2>

    <nav>
        <a href="../index.php">WEB250 Home</a> |
        <a href="../carapp/index.php">Car AppA</a> |
        <a href="index.php">Car AppB</a>
    </nav>
</header>

<main>

    <h2>Sam's Used Cars Inventory</h2>

    <?php if ($message !== ''): ?>
        <p class="message"><?= h($message) ?></p>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>

        <p>
            Logged in as
            <strong><?= h($_SESSION['carapp2_username'] ?? 'User') ?></strong>.
            <a href="scripts/logout.php">Log Out</a>
        </p>

        <p>
            <a href="scripts/setup.php"
               onclick="return confirm('WARNING: This will reset Car AppB data. Continue?');">
                Reset Car AppB Database
            </a>
        </p>

        <section>
            <h2><?= $editCar ? 'Edit Vehicle' : 'Add Vehicle' ?></h2>

            <form method="post" action="index.php">
                <input type="hidden" name="csrf_token"
                       value="<?= h($csrfToken) ?>">

                <input type="hidden" name="ORIGINAL_VIN"
                       value="<?= h($editCar['VIN'] ?? '') ?>">

                <label for="VIN">VIN:</label>
                <input type="text" id="VIN" name="VIN" maxlength="17"
                       minlength="17" required
                       value="<?= h($editCar['VIN'] ?? '') ?>">

                <label for="YEAR">Year:</label>
                <input type="number" id="YEAR" name="YEAR"
                       min="1900" max="2100" required
                       value="<?= h($editCar['YEAR'] ?? '') ?>">

                <label for="Make">Make:</label>
                <input type="text" id="Make" name="Make" required
                       value="<?= h($editCar['Make'] ?? '') ?>">

                <label for="Model">Model:</label>
                <input type="text" id="Model" name="Model" required
                       value="<?= h($editCar['Model'] ?? '') ?>">

                <label for="TRIM">Trim:</label>
                <input type="text" id="TRIM" name="TRIM"
                       value="<?= h($editCar['TRIM'] ?? '') ?>">

                <label for="EXT_COLOR">Exterior Color:</label>
                <input type="text" id="EXT_COLOR" name="EXT_COLOR"
                       value="<?= h($editCar['EXT_COLOR'] ?? '') ?>">

                <label for="INT_COLOR">Interior Color:</label>
                <input type="text" id="INT_COLOR" name="INT_COLOR"
                       value="<?= h($editCar['INT_COLOR'] ?? '') ?>">

                <label for="ASKING_PRICE">Asking Price:</label>
                <input type="number" step="0.01" min="0"
                       id="ASKING_PRICE" name="ASKING_PRICE"
                       value="<?= h($editCar['ASKING_PRICE'] ?? '0') ?>">

                <label for="SALE_PRICE">Sale Price:</label>
                <input type="number" step="0.01" min="0"
                       id="SALE_PRICE" name="SALE_PRICE"
                       value="<?= h($editCar['SALE_PRICE'] ?? '0') ?>">

                <label for="PURCHASE_PRICE">Purchase Price:</label>
                <input type="number" step="0.01" min="0"
                       id="PURCHASE_PRICE" name="PURCHASE_PRICE"
                       value="<?= h($editCar['PURCHASE_PRICE'] ?? '0') ?>">

                <label for="MILEAGE">Mileage:</label>
                <input type="number" min="0" id="MILEAGE" name="MILEAGE"
                       value="<?= h($editCar['MILEAGE'] ?? '0') ?>">

                <label for="TRANSMISSION">Transmission:</label>
                <input type="text" id="TRANSMISSION" name="TRANSMISSION"
                       value="<?= h($editCar['TRANSMISSION'] ?? '') ?>">

                <label for="PURCHASE_DATE">Purchase Date:</label>
                <input type="text" id="PURCHASE_DATE" name="PURCHASE_DATE"
                       placeholder="YYYY-MM-DD"
                       value="<?= h($editCar['PURCHASE_DATE'] ?? '') ?>">

                <label for="SALE_DATE">Sale Date:</label>
                <input type="text" id="SALE_DATE" name="SALE_DATE"
                       placeholder="YYYY-MM-DD"
                       value="<?= h($editCar['SALE_DATE'] ?? '') ?>">

                <button type="submit" name="save">Save Vehicle</button>

                <?php if ($editCar): ?>
                    <a href="index.php">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </section>

    <?php else: ?>

        <section>
            <h2>Employee Login</h2>
            <p>Log in to add, edit, or delete vehicles.</p>

            <form method="post" action="scripts/login.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Log In</button>
            </form>
        </section>

    <?php endif; ?>

    <section>
        <h2>Current Inventory</h2>

        <p>Total Vehicles: <?= $totalCars ?></p>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>VIN</th>
                        <th>Year</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Trim</th>
                        <th>Exterior</th>
                        <th>Interior</th>
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
                    <?php if ($cars->num_rows > 0): ?>

                        <?php while ($car = $cars->fetch_assoc()): ?>
                            <tr>
                                <td><?= h($car['VIN']) ?></td>
                                <td><?= h($car['YEAR']) ?></td>
                                <td><?= h($car['Make']) ?></td>
                                <td><?= h($car['Model']) ?></td>
                                <td><?= h($car['TRIM']) ?></td>
                                <td><?= h($car['EXT_COLOR']) ?></td>
                                <td><?= h($car['INT_COLOR']) ?></td>
                                <td>$<?= number_format((float)$car['ASKING_PRICE'], 2) ?></td>
                                <td>$<?= number_format((float)$car['SALE_PRICE'], 2) ?></td>
                                <td>$<?= number_format((float)$car['PURCHASE_PRICE'], 2) ?></td>
                                <td><?= number_format((int)$car['MILEAGE']) ?></td>
                                <td><?= h($car['TRANSMISSION']) ?></td>
                                <td><?= h($car['PURCHASE_DATE']) ?></td>
                                <td><?= h($car['SALE_DATE']) ?></td>

                                <?php if ($isLoggedIn): ?>
                                    <td>
                                        <a href="index.php?edit=<?= urlencode($car['VIN']) ?>">
                                            Edit
                                        </a>

                                        <form method="post" action="index.php"
                                              onsubmit="return confirm('Delete this vehicle?');">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= h($csrfToken) ?>">
                                            <input type="hidden" name="VIN"
                                                   value="<?= h($car['VIN']) ?>">
                                            <button type="submit" name="delete">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isLoggedIn ? 15 : 14 ?>">
                                No vehicles found in Car AppB.
                            </td>
                        </tr>
                    <?php endif; ?>
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

    </section>

</main>

<footer>
    <p>Designed by Sneaky Snake Productions</p>
    <p>Steven Sharman &copy; 2026</p>
</footer>

</body>
</html>
<?php
$stmt->close();
$mysqli->close();
?>
