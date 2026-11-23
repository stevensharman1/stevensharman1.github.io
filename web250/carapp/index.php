
<?php
require_once 'config_db.php';

$message = "";
$editCar = null;

// Delete a vehicle.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $vin = trim($_POST["VIN"] ?? "");

    $stmt = $mysqli->prepare("DELETE FROM inventory WHERE VIN = ?");
    $stmt->bind_param("s", $vin);

    if ($stmt->execute()) {
        $message = "Vehicle deleted successfully.";
    } else {
        $message = "Unable to delete vehicle.";
    }

    $stmt->close();
}

// Add or update a vehicle.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save"])) {
    $vin = strtoupper(trim($_POST["VIN"] ?? ""));
    $year = (int)($_POST["YEAR"] ?? 0);
    $make = trim($_POST["Make"] ?? "");
    $model = trim($_POST["Model"] ?? "");
    $trim = trim($_POST["TRIM"] ?? "");
    $extColor = trim($_POST["EXT_COLOR"] ?? "");
    $intColor = trim($_POST["INT_COLOR"] ?? "");
    $askingPrice = (float)($_POST["ASKING_PRICE"] ?? 0);
    $salePrice = (float)($_POST["SALE_PRICE"] ?? 0);
    $purchasePrice = (float)($_POST["PURCHASE_PRICE"] ?? 0);
    $mileage = (int)($_POST["MILEAGE"] ?? 0);
    $transmission = trim($_POST["TRANSMISSION"] ?? "");
    $purchaseDate = !empty($_POST["PURCHASE_DATE"]) ? trim($_POST["PURCHASE_DATE"]) : null;
    $saleDate = !empty($_POST["SALE_DATE"]) ? trim($_POST["SALE_DATE"]) : null;
    $originalVIN = trim($_POST["ORIGINAL_VIN"] ?? "");

    // Validate the required vehicle information and date format.
    $validPurchaseDate = $purchaseDate === null ||
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $purchaseDate);

    $validSaleDate = $saleDate === null ||
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $saleDate);

    if (
        strlen($vin) !== 17 ||
        $year < 1900 ||
        $make === "" ||
        $model === "" ||
        $askingPrice < 0 ||
        $salePrice < 0 ||
        $purchasePrice < 0 ||
        $mileage < 0 ||
        !$validPurchaseDate ||
        !$validSaleDate
    ) {
        $message = "Please check the vehicle information. VIN must be 17 characters and dates must use YYYY-MM-DD.";
    } else {
        if ($originalVIN !== "") {
            $stmt = $mysqli->prepare(
                "UPDATE inventory SET
                VIN=?, YEAR=?, Make=?, Model=?, TRIM=?,
                EXT_COLOR=?, INT_COLOR=?, ASKING_PRICE=?,
                SALE_PRICE=?, PURCHASE_PRICE=?, MILEAGE=?,
                TRANSMISSION=?, PURCHASE_DATE=?, SALE_DATE=?
                WHERE VIN=?"
            );

            $stmt->bind_param(
                "sisssssdddissss",
                $vin, $year, $make, $model, $trim,
                $extColor, $intColor, $askingPrice,
                $salePrice, $purchasePrice, $mileage,
                $transmission, $purchaseDate, $saleDate,
                $originalVIN
            );

            $successMessage = "Vehicle updated successfully.";
        } else {
            $stmt = $mysqli->prepare(
                "INSERT INTO inventory (
                VIN, YEAR, Make, Model, TRIM,
                EXT_COLOR, INT_COLOR, ASKING_PRICE,
                SALE_PRICE, PURCHASE_PRICE, MILEAGE,
                TRANSMISSION, PURCHASE_DATE, SALE_DATE
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sisssssdddisss",
                $vin, $year, $make, $model, $trim,
                $extColor, $intColor, $askingPrice,
                $salePrice, $purchasePrice, $mileage,
                $transmission, $purchaseDate, $saleDate
            );

            $successMessage = "Vehicle added successfully.";
        }

        if ($stmt->execute()) {
            $message = $successMessage;
        } else {
            $message = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Load a vehicle for editing.
if (isset($_GET["edit"])) {
    $vin = trim($_GET["edit"]);

    $stmt = $mysqli->prepare("SELECT * FROM inventory WHERE VIN = ?");
    $stmt->bind_param("s", $vin);
    $stmt->execute();

    $resultEdit = $stmt->get_result();
    $editCar = $resultEdit->fetch_assoc();

    $stmt->close();
}

// Retrieve inventory.
$result = $mysqli->query("SELECT * FROM inventory ORDER BY Make, Model");

// Safely display database values in form fields and table cells.
function fieldValue($car, $field) {
    return htmlspecialchars((string)($car[$field] ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman’s Sneaky Snake ✦ WEB250 ✦ Sam's Used Cars Inventory</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles/default.css">
    <link rel="icon" href="../images/favicon.svg" type="image/svg+xml">

    <!-- Accumulus Validator -->
    <script src="https://lint.page/kit/4d0fe3.js" crossorigin="anonymous"></script>
</head>

<body>

<header>
    <h1>Steven Sharman’s Sneaky Snake ✦ WEB250</h1>

    <!-- Reuses the existing Sneaky Snake favicon as the site logo. -->
    <img src="../images/favicon.svg" alt="Sneaky Snake logo" class="site-logo">

    <nav>
        <a href="index.php">Used Cars Home</a> |
        <a href="../index.php">WEB250 Home</a>
    </nav>
</header>

<main>

    <h2>Sam's Used Cars Inventory</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <section>
        <h3><?= $editCar ? "Edit Vehicle" : "Add a Vehicle" ?></h3>

        <!-- The form groups all vehicle information and its image together. -->
        <form method="post" action="index.php">
            <input type="hidden" name="ORIGINAL_VIN"
                value="<?= fieldValue($editCar ?? [], "VIN") ?>">

            <!-- Displays the Chrysler 300C above the vehicle fields. -->
           <img src="images/Black%20Chrysler%20300C%20Vector%20Illustration.png"
     alt="Black Chrysler 300C" class="car-image">

            <label for="VIN">VIN:</label>
            <input id="VIN" name="VIN" type="text" maxlength="17"
                minlength="17" required value="<?= fieldValue($editCar ?? [], "VIN") ?>">

            <label for="YEAR">Year:</label>
            <input id="YEAR" name="YEAR" type="number" min="1900"
                required value="<?= fieldValue($editCar ?? [], "YEAR") ?>">

            <label for="Make">Make:</label>
            <input id="Make" name="Make" type="text" required
                value="<?= fieldValue($editCar ?? [], "Make") ?>">

            <label for="Model">Model:</label>
            <input id="Model" name="Model" type="text" required
                value="<?= fieldValue($editCar ?? [], "Model") ?>">

            <label for="TRIM">Trim:</label>
            <input id="TRIM" name="TRIM" type="text"
                value="<?= fieldValue($editCar ?? [], "TRIM") ?>">

            <label for="EXT_COLOR">Exterior Color:</label>
            <input id="EXT_COLOR" name="EXT_COLOR" type="text"
                value="<?= fieldValue($editCar ?? [], "EXT_COLOR") ?>">

            <label for="INT_COLOR">Interior Color:</label>
            <input id="INT_COLOR" name="INT_COLOR" type="text"
                value="<?= fieldValue($editCar ?? [], "INT_COLOR") ?>">

            <label for="ASKING_PRICE">Asking Price:</label>
            <input id="ASKING_PRICE" name="ASKING_PRICE" type="number"
                min="0" step="0.01" required
                value="<?= fieldValue($editCar ?? [], "ASKING_PRICE") ?>">

            <label for="SALE_PRICE">Sale Price:</label>
            <input id="SALE_PRICE" name="SALE_PRICE" type="number"
                min="0" step="0.01"
                value="<?= fieldValue($editCar ?? [], "SALE_PRICE") ?: "0" ?>">

            <label for="PURCHASE_PRICE">Purchase Price:</label>
            <input id="PURCHASE_PRICE" name="PURCHASE_PRICE" type="number"
                min="0" step="0.01"
                value="<?= fieldValue($editCar ?? [], "PURCHASE_PRICE") ?: "0" ?>">

            <label for="MILEAGE">Mileage:</label>
            <input id="MILEAGE" name="MILEAGE" type="number" min="0"
                value="<?= fieldValue($editCar ?? [], "MILEAGE") ?: "0" ?>">

            <label for="TRANSMISSION">Transmission:</label>
            <input id="TRANSMISSION" name="TRANSMISSION" type="text"
                value="<?= fieldValue($editCar ?? [], "TRANSMISSION") ?>">

            <!-- Text inputs accept YYYY-MM-DD dates without browser date controls. -->
            <label for="PURCHASE_DATE">Purchase Date:</label>
            <input id="PURCHASE_DATE" name="PURCHASE_DATE" type="text"
                placeholder="YYYY-MM-DD"
                value="<?= fieldValue($editCar ?? [], "PURCHASE_DATE") ?>">

            <label for="SALE_DATE">Sale Date:</label>
            <input id="SALE_DATE" name="SALE_DATE" type="text"
                placeholder="YYYY-MM-DD"
                value="<?= fieldValue($editCar ?? [], "SALE_DATE") ?>">

            <button type="submit" name="save">
                <?= $editCar ? "Update Vehicle" : "Add Vehicle" ?>
            </button>

            <?php if ($editCar): ?>
                <a href="index.php">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </section>

    <section>
        <h3>Current Inventory</h3>

        <?php if ($result && $result->num_rows > 0): ?>

            <!-- Allows the wide inventory table to scroll on smaller screens. -->
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
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $rowNumber = 0; ?>

                        <?php while ($car = $result->fetch_assoc()): ?>
                            <tr class="<?= $rowNumber++ % 2 === 0 ? "odd" : "even" ?>">
                                <td><?= fieldValue($car, "VIN") ?></td>
                                <td><?= fieldValue($car, "YEAR") ?></td>
                                <td><?= fieldValue($car, "Make") ?></td>
                                <td><?= fieldValue($car, "Model") ?></td>
                                <td><?= fieldValue($car, "TRIM") ?></td>
                                <td><?= fieldValue($car, "EXT_COLOR") ?></td>
                                <td><?= fieldValue($car, "INT_COLOR") ?></td>
                                <td>$<?= number_format((float)$car["ASKING_PRICE"], 2) ?></td>
                                <td>$<?= number_format((float)$car["SALE_PRICE"], 2) ?></td>
                                <td>$<?= number_format((float)$car["PURCHASE_PRICE"], 2) ?></td>
                                <td><?= number_format((int)$car["MILEAGE"]) ?></td>
                                <td><?= fieldValue($car, "TRANSMISSION") ?></td>
                                <td><?= fieldValue($car, "PURCHASE_DATE") ?></td>
                                <td><?= fieldValue($car, "SALE_DATE") ?></td>
                                <td>
                                    <a href="index.php?edit=<?= urlencode($car["VIN"]) ?>">Edit</a>

                                    <form method="post" action="index.php"
                                        onsubmit="return confirm('Delete this vehicle?');">
                                        <input type="hidden" name="VIN"
                                            value="<?= fieldValue($car, "VIN") ?>">
                                        <button type="submit" name="delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <p>No vehicles are currently in the inventory.</p>
        <?php endif; ?>
    </section>

</main>

<footer>
    <p><em>"Slow and steady in quality, fast in learning."</em></p>
    <p>Page built by Steven Sharman's Sneaky Snake.</p>
    <p>WEB250 Web Development</p>

    <p>
        <a href="https://validator.w3.org/check?uri=referer" target="_blank" rel="noopener">
            HTML Validator
        </a>
        |
        <a href="https://jigsaw.w3.org/css-validator/check/referer" target="_blank" rel="noopener">
            CSS Validator
        </a>
    </p>
</footer>

<!-- Vicunadator Validator -->
<script src="https://divonbriesen.github.io/teaching/tools/standards-check.js" defer></script>

</body>
</html>

<?php
if ($result) {
    $result->free();
}
$mysqli->close();
?>

