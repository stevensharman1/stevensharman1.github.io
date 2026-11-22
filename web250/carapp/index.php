
<?php
/**
 * Steven Sharman's Sneaky Snake's Used Cars
 * Single-page car inventory application.
 */

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
    $purchaseDate = $_POST["PURCHASE_DATE"] ?: null;
    $saleDate = $_POST["SALE_DATE"] ?: null;

    $originalVIN = trim($_POST["ORIGINAL_VIN"] ?? "");

    if (
        strlen($vin) !== 17 ||
        $year < 1900 ||
        $make === "" ||
        $model === "" ||
        $askingPrice < 0 ||
        $salePrice < 0 ||
        $purchasePrice < 0 ||
        $mileage < 0
    ) {
        $message = "Please check the vehicle information. VIN must be 17 characters.";
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

// Load a vehicle into the form for editing.
if (isset($_GET["edit"])) {
    $vin = trim($_GET["edit"]);

    $stmt = $mysqli->prepare("SELECT * FROM inventory WHERE VIN = ?");
    $stmt->bind_param("s", $vin);
    $stmt->execute();

    $result = $stmt->get_result();
    $editCar = $result->fetch_assoc();

    $stmt->close();
}

// Get all vehicles.
$result = $mysqli->query("SELECT * FROM inventory ORDER BY Make, Model");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman's Sneaky Snake's Used Cars</title>

    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="stylesheet" href="styles/default.css">

    <!-- Accumulus Validator -->
    <script src="https://lint.page/kit/4d0fe3.js" crossorigin="anonymous"></script>
</head>

<body>

<header>
    <h1>Steven Sharman's Sneaky Snake's Used Cars</h1>
    <nav>
        <a href="index.php">Home</a> |
        <a href="../index.php">WEB250 Home</a>
    </nav>
</header>

<main>
    <h2>Vehicle Inventory</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <h2><?= $editCar ? "Edit Vehicle" : "Add a Vehicle" ?></h2>

    <form method="post" action="index.php">
        <input type="hidden" name="ORIGINAL_VIN"
            value="<?= htmlspecialchars($editCar["VIN"] ?? "") ?>">

        <label for="VIN">VIN:</label>
        <input id="VIN" name="VIN" type="text" maxlength="17"
            required value="<?= htmlspecialchars($editCar["VIN"] ?? "") ?>">

        <label for="YEAR">Year:</label>
        <input id="YEAR" name="YEAR" type="number" required
            value="<?= htmlspecialchars($editCar["YEAR"] ?? "") ?>">

        <label for="Make">Make:</label>
        <input id="Make" name="Make" type="text" required
            value="<?= htmlspecialchars($editCar["Make"] ?? "") ?>">

        <label for="Model">Model:</label>
        <input id="Model" name="Model" type="text" required
            value="<?= htmlspecialchars($editCar["Model"] ?? "") ?>">

        <label for="TRIM">Trim:</label>
        <input id="TRIM" name="TRIM" type="text"
            value="<?= htmlspecialchars($editCar["TRIM"] ?? "") ?>">

        <label for="EXT_COLOR">Exterior Color:</label>
        <input id="EXT_COLOR" name="EXT_COLOR" type="text"
            value="<?= htmlspecialchars($editCar["EXT_COLOR"] ?? "") ?>">

        <label for="INT_COLOR">Interior Color:</label>
        <input id="INT_COLOR" name="INT_COLOR" type="text"
            value="<?= htmlspecialchars($editCar["INT_COLOR"] ?? "") ?>">

        <label for="ASKING_PRICE">Asking Price:</label>
        <input id="ASKING_PRICE" name="ASKING_PRICE" type="number"
            min="0" step="0.01" required
            value="<?= htmlspecialchars($editCar["ASKING_PRICE"] ?? "") ?>">

        <label for="SALE_PRICE">Sale Price:</label>
        <input id="SALE_PRICE" name="SALE_PRICE" type="number"
            min="0" step="0.01"
            value="<?= htmlspecialchars($editCar["SALE_PRICE"] ?? "0") ?>">

        <label for="PURCHASE_PRICE">Purchase Price:</label>
        <input id="PURCHASE_PRICE" name="PURCHASE_PRICE" type="number"
            min="0" step="0.01"
            value="<?= htmlspecialchars($editCar["PURCHASE_PRICE"] ?? "0") ?>">

        <label for="MILEAGE">Mileage:</label>
        <input id="MILEAGE" name="MILEAGE" type="number" min="0"
            value="<?= htmlspecialchars($editCar["MILEAGE"] ?? "0") ?>">

        <label for="TRANSMISSION">Transmission:</label>
        <input id="TRANSMISSION" name="TRANSMISSION" type="text"
            value="<?= htmlspecialchars($editCar["TRANSMISSION"] ?? "") ?>">

        <label for="PURCHASE_DATE">Purchase Date:</label>
        <input id="PURCHASE_DATE" name="PURCHASE_DATE" type="date"
            value="<?= htmlspecialchars($editCar["PURCHASE_DATE"] ?? "") ?>">

        <label for="SALE_DATE">Sale Date:</label>
        <input id="SALE_DATE" name="SALE_DATE" type="date"
            value="<?= htmlspecialchars($editCar["SALE_DATE"] ?? "") ?>">

        <button type="submit" name="save">
            <?= $editCar ? "Update Vehicle" : "Add Vehicle" ?>
        </button>

        <?php if ($editCar): ?>
            <a href="index.php">Cancel Edit</a>
        <?php endif; ?>
    </form>

    <h2>Current Inventory</h2>

    <?php if ($result && $result->num_rows > 0): ?>
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
                        <tr class="<?= $rowNumber++ % 2 === 0 ? 'odd' : 'even' ?>">
                            <?php foreach ($car as $value): ?>
                                <td><?= htmlspecialchars((string)($value ?? "")) ?></td>
                            <?php endforeach; ?>

                            <td>
                                <a href="index.php?edit=<?= urlencode($car["VIN"]) ?>">
                                    Edit
                                </a>

                                <form method="post" action="index.php"
                                    onsubmit="return confirm('Delete this vehicle?');">
                                    <input type="hidden" name="VIN"
                                        value="<?= htmlspecialchars($car["VIN"]) ?>">
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
</main>

<footer>
    <p>Page built by Steven Sharman's Sneaky Snake.</p>
    <p>WEB250 Web Development</p>
</footer>

<!-- Vicunadator validator: use the same validator script from your WEB250 site here. -->

</body>
</html>

<?php
if ($result) {
    $result->free();
}
$mysqli->close();
?>

