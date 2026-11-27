
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

