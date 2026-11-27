
<?php
/**
 * Steven Sharman's Sneaky Snake - Car AppB
 * Used Cars Inventory
 *
 * Public visitors can view inventory.
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

$message = '';
$editCar = null;

// Escape output before displaying it in HTML.
function escapeValue($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Validate optional dates.
function validDate($date) {
    if ($date === '') {
        return true;
    }

    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    return $parsed && $parsed->format('Y-m-d') === $date;
}

// Handle submitted forms.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify the CSRF token.
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($csrfToken, $submittedToken)) {
        http_response_code(403);
        die('Invalid form submission. Please return to the inventory page.');
    }

    // Only logged-in users may change inventory.
    if (!$isLoggedIn) {
        http_response_code(403);
        die('You must log in to manage vehicles.');
    }

    // Delete a vehicle.
    if (isset($_POST['delete'])) {
        $vin = trim($_POST['vin'] ?? '');

        if (strlen($vin) !== 17) {
            $message = 'Invalid VIN.';
        } else {
            $stmt = $mysqli->prepare(
                "DELETE FROM carapp2_inventory WHERE VIN = ?"
            );

            $stmt->bind_param('s', $vin);

            if ($stmt->execute()) {
                $message = 'Vehicle deleted successfully.';
            } else {
                $message = 'Unable to delete vehicle.';
            }

            $stmt->close();
        }
    }

    // Add or update a vehicle.
    if (isset($_POST['save'])) {

        $vin = strtoupper(trim($_POST['VIN'] ?? ''));
        $originalVin = strtoupper(trim($_POST['ORIGINAL_VIN'] ?? ''));

        $year = filter_var($_POST['YEAR'] ?? null, FILTER_VALIDATE_INT);
        $make = trim($_POST['Make'] ?? '');
        $model = trim($_POST['Model'] ?? '');
        $trim = trim($_POST['TRIM'] ?? '');
        $extColor = trim($_POST['EXT_COLOR'] ?? '');
        $intColor = trim($_POST['INT_COLOR'] ?? '');

        $askingPrice = filter_var($_POST['ASKING_PRICE'] ?? null, FILTER_VALIDATE_FLOAT);
        $salePrice = filter_var($_POST['SALE_PRICE'] ?? null, FILTER_VALIDATE_FLOAT);
        $purchasePrice = filter_var($_POST['PURCHASE_PRICE'] ?? null, FILTER_VALIDATE_FLOAT);

        $mileage = filter_var($_POST['MILEAGE'] ?? null, FILTER_VALIDATE_INT);
        $transmission = trim($_POST['TRANSMISSION'] ?? '');

        $purchaseDate = trim($_POST['PURCHASE_DATE'] ?? '');
        $saleDate = trim($_POST['SALE_DATE'] ?? '');

        // Validate required fields and values.
        $valid =
            strlen($vin) === 17 &&
            $year !== false &&
            $year >= 1900 &&
            $year <= ((int)date('Y') + 2) &&
            $make !== '' &&
            $model !== '' &&
            $askingPrice !== false &&
            $askingPrice >= 0 &&
            $salePrice !== false &&
            $salePrice >= 0 &&
            $purchasePrice !== false &&
            $purchasePrice >= 0 &&
            $mileage !== false &&
            $mileage >= 0 &&
            validDate($purchaseDate) &&
            validDate($saleDate);

        if (!$valid) {
            $message = 'Please check all fields. VIN must contain 17 characters and numeric values must be valid.';
        } else {

            // Convert blank dates to SQL NULL.
            $purchaseDateValue = $purchaseDate === '' ? null : $purchaseDate;
            $saleDateValue = $saleDate === '' ? null : $saleDate;

            if ($originalVin !== '') {

                // Update an existing vehicle.
                $stmt = $mysqli->prepare(
                    "UPDATE carapp2_inventory
                     SET VIN = ?, YEAR = ?, Make = ?, Model = ?, TRIM = ?,
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
                    $purchaseDateValue,
                    $saleDateValue,
                    $originalVin
                );

                if ($stmt->execute()) {
                    $message = 'Vehicle updated successfully.';
                } else {
                    $message = 'Unable to update vehicle. Check that the VIN is not already in use.';
                }

                $stmt->close();

            } else {

                // Insert a new vehicle.
                $stmt = $mysqli->prepare(
                    "INSERT INTO carapp2_inventory
                     (VIN, YEAR, Make, Model, TRIM, EXT_COLOR, INT_COLOR,
                      ASKING_PRICE, SALE_PRICE, PURCHASE_PRICE, MILEAGE,
                      TRANSMISSION, PURCHASE_DATE, SALE_DATE)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
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
                    $purchaseDateValue,
                    $saleDateValue
                );

                if ($stmt->execute()) {
                    $message = 'Vehicle added successfully.';
                } else {
                    $message = 'Unable to add vehicle. Check that the VIN is unique.';
                }

                $stmt->close();
            }
        }
    }
}

// Load a vehicle for editing.
if ($isLoggedIn && isset($_GET['edit'])) {
    $editVin = trim($_GET['edit']);

    $stmt = $mysqli->prepare(
        "SELECT * FROM carapp2_inventory WHERE VIN = ?"
    );

    $stmt->bind_param('s', $editVin);
    $stmt->execute();

    $result = $stmt->get_result();
    $editCar = $result->fetch_assoc();

    $stmt->close();
}

// Pagination settings.
$perPage = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

if ($page === false || $page === null || $page < 1) {
    $page = 1;
}

// Count inventory records.
$countResult = $mysqli->query(
    "SELECT COUNT(*) AS total FROM carapp2_inventory"
);

$totalCars = (int)$countResult->fetch_assoc()['total'];
$totalPages = max(1, (int)ceil($totalCars / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

// Retrieve the current inventory page.
$stmt = $mysqli->prepare(
    "SELECT * FROM carapp2_inventory
     ORDER BY Make, Model
     LIMIT ? OFFSET ?"
);

$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();

$cars = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman’s Sneaky Snake ✦ WEB250 ✦ Used Cars Inventory - Car AppB</title>

    <link rel="stylesheet" href="styles/default.css">
    <link rel="icon" href="../images/favicon.svg" type="image/svg+xml">

    <!-- Accumulus validator script. -->
    <script src="https://lint.page/kit/4d0fe3.js"></script>
</head>

<body>

<header>
    <h1>Steven Sharman’s Sneaky Snake ✦ WEB250</h1>

    <!-- The site-logo class controls the mascot logo size. -->
    <img src="../images/favicon.svg"
         alt="Sneaky Snake logo"
         class="site-logo">

    <!-- This image provides a page image separate from the favicon. -->
    <img src="../carapp/images/chrysler-300c.png"
         alt="Chrysler 300C used car"
         class="car-image">

    <?php include 'components/navigation.php'; ?>
</header>

<main>

    <h2>Used Cars Inventory - Car AppB</h2>

    <?php if ($message !== ''): ?>
        <!-- The message class styles inventory feedback messages. -->
        <p class="message"><?= escapeValue($message) ?></p>
    <?php endif; ?>

    <?php
    // Display the login form to visitors.
    include 'components/login_form.php';
    ?>

    <?php if ($isLoggedIn): ?>
        <section>
            <h3>Account</h3>

            <p>
                You are logged in as
                <strong><?= escapeValue($_SESSION['carapp2_username'] ?? 'User') ?></strong>.
            </p>

            <p>
                <a href="scripts/logout.php">Log Out</a>
            </p>

            <p>
                <a href="scripts/setup.php">Reset and Rebuild Car AppB Database</a>
            </p>
        </section>
    <?php endif; ?>

    <?php
    // Display the add/edit vehicle form.
    include 'components/vehicle_form.php';
    ?>

    <section>
        <h3>Current Inventory</h3>

        <p>Total vehicles: <?= $totalCars ?></p>

            <!-- This div is needed to wrap the wide inventory table so it can scroll horizontally on smaller screens. -->
            <div class="table-wrapper">        
        <!-- The Grid id identifies the inventory table for its CSS styling. -->
            <table id="Grid">

                <thead>
                    <tr>
                        <th>VIN</th>
                        <th>YEAR</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>TRIM</th>
                        <th>EXT_COLOR</th>
                        <th>INT_COLOR</th>
                        <th>ASKING_PRICE</th>
                        <th>SALE_PRICE</th>
                        <th>PURCHASE_PRICE</th>
                        <th>MILEAGE</th>
                        <th>TRANSMISSION</th>
                        <th>PURCHASE_DATE</th>
                        <th>SALE_DATE</th>

                        <?php if ($isLoggedIn): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($cars->num_rows > 0): ?>

                        <?php $rowNumber = 0; ?>

                        <?php while ($car = $cars->fetch_assoc()): ?>

                            <?php
                            // Odd and even classes alternate the row appearance.
                            $rowClass = ($rowNumber % 2 === 0) ? 'odd' : 'even';
                            $rowNumber++;
                            ?>

                            <tr class="<?= $rowClass ?>">
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
                                        <a href="?edit=<?= urlencode($car['VIN']) ?>">
                                            Edit
                                        </a>

                                        <form method="post"
                                              action="index.php"
                                              onsubmit="return confirm('Delete this vehicle?');">

                                            <input type="hidden"
                                                   name="csrf_token"
                                                   value="<?= escapeValue($csrfToken) ?>">

                                            <input type="hidden"
                                                   name="vin"
                                                   value="<?= escapeValue($car['VIN']) ?>">

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
                                No vehicles are currently in the inventory.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>
         </table>

        <!-- This div closes the responsive wrapper around the inventory table. -->
    </div>

        <!-- The pagination class styles the links between inventory pages. -->
        <nav class="pagination" aria-label="Inventory pages">

            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Previous</a>
            <?php endif; ?>
        <!-- This span keeps the current page number together on one line between pagination links. -->

            <span>
                Page <?= $page ?> of <?= $totalPages ?>
            </span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next</a>
            <?php endif; ?>

        </nav>
    </section>

</main>

<?php
// Close the inventory query and database connection once.
$stmt->close();
$mysqli->close();
?>

<?php include 'components/footer.php'; ?>

