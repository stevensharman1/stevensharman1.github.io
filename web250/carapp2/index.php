
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

// Safely display text.
function escapeValue($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Validate dates.
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

    if (!$isLoggedIn) {
        http_response_code(403);
        exit('You must log in to manage vehicles.');
    }

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
                    'sisssssdddisssss',
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

// Load a vehicle for editing.
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

// Retrieve inventory.
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

    <title>Steven Sharman’s Sneaky Snake ✦ WEB250 ✦ Used Cars Inventory - Car AppB</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="styles/default.css">
    <link rel="icon" href="../images/favicon.svg" type="image/svg+xml">

    <!-- Vicunadator Validator -->
    <script src="https://divonbriesen.github.io/teaching/tools/standards-check.js"
            defer></script>

    <!-- Accumulus Validator -->
    <script src="https://lint.page/kit/4d0fe3.js"
            crossorigin="anonymous"></script>
</head>

<body>

<?php include 'components/header.php'; ?>

<main>

    <h2>Used Cars Inventory - Car AppB</h2>

    <?php if ($message !== ''): ?>
        <p class="message">
            <?= escapeValue($message) ?>
        </p>
    <?php endif; ?>

    <?php include 'components/account.php'; ?>

    <?php include 'components/vehicle_form.php'; ?>

    <section>
        <h3>Current Inventory</h3>

        <p>
            Showing <?= $totalCars === 0 ? 0 : $offset + 1 ?>
            through <?= min($offset + $perPage, $totalCars) ?>
            of <?= $totalCars ?> vehicles.
        </p>

        <?php if ($totalCars > 0): ?>

        <!-- The table-wrapper div allows the wide inventory table to scroll on smaller screens. -->
            <div class="table-wrapper">

                  <!-- The Grid id identifies the inventory table for its existing CSS styling. -->
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

                        <!-- The odd and even classes alternate row styling to make inventory easier to read. -->
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
<!-- End of the responsive table-wrapper div. -->
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

<?php
// Close the inventory query and database connection.
$stmt->close();
$mysqli->close();
?>

<?php include 'components/footer.php'; ?>

<?php
$stmt->close();
$mysqli->close();
?>
