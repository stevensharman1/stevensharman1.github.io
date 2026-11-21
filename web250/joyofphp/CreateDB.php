
<?php
/**
 * Joy of PHP - Sam's Used Cars
 * Creates the inventory table and inserts sample vehicles.
 */

require_once 'db.php';

echo "<h1>Sam's Used Cars - Database Setup</h1>";

// Create the inventory table if it does not already exist.
$query = "CREATE TABLE IF NOT EXISTS inventory (
    VIN VARCHAR(17) PRIMARY KEY,
    `YEAR` INT,
    Make VARCHAR(50),
    Model VARCHAR(100),
    TRIM VARCHAR(50),
    EXT_COLOR VARCHAR(50),
    INT_COLOR VARCHAR(50),
    ASKING_PRICE DECIMAL(10,2),
    SALE_PRICE DECIMAL(10,2),
    PURCHASE_PRICE DECIMAL(10,2),
    MILEAGE INT,
    TRANSMISSION VARCHAR(50),
    PURCHASE_DATE DATE,
    SALE_DATE DATE
)";

if ($mysqli->query($query)) {
    echo "<p>Inventory table is ready.</p>";
} else {
    die("<p>Error creating inventory table: "
        . htmlspecialchars($mysqli->error) . "</p>");
}

// Original sample vehicle records.
$cars = [
    ['5FNYF4H91CB054036', 2012, 'Honda', 'Pilot', 'Touring', 'White Diamond Pearl', 'Leather', 37807, null, 34250, 7076, 'Automatic', '2012-11-08', null],
    ['LAKSDFJ234LASKRF2', 2009, 'Dodge', 'Durango', 'SLT', 'Silver', 'Black', 2700, null, 2000, 144000, '4WD Automatic', '2012-12-05', null],
    ['1FAFP44423F44657', 2003, 'Ford', 'Mustang', 'Base', 'Silver / Black', 'Gray', 8995, null, 6746, 75820, 'Automatic', '2013-01-14', null],
    ['2G1WD58C47917903', 2007, 'Chevrolet', 'Impala', 'SS', 'Gray', 'Gray', 9995, null, 7496, 129108, '4-Speed Automatic', '2013-01-14', null],
    ['19UUA56682A036203', 2002, 'Acura', 'TL', 'Base', 'Blue', 'Tan', 7995, null, 5996, 77442, '5-Speed Automatic', '2013-01-14', null],
    ['1B3EL46J25N513802', 2005, 'Dodge', 'Stratus', 'SXT', 'Blue', 'Gray', 7995, null, 5996, 41941, '4-Speed Automatic', '2013-01-14', null],
    ['1FAHP36N09W191342', 2009, 'Ford', 'Focus', 'SES', 'Silver', 'Gray', 9670, null, 7252, 47000, 'Automatic', '2013-01-14', null],
    ['1G1JH12FX57138124', 2005, 'Chevrolet', 'Cavalier', 'LS Sport', 'Yellow', 'Gray', 9995, null, 7596, 15828, '5-Speed Manual', '2013-01-14', null],
    ['1G1ZU54814F248763', 2004, 'Chevrolet', 'Malibu', 'LT', 'Black', 'Gray', 8995, null, 6746, 41135, '4-Speed Automatic', '2013-01-14', null],
    ['1G6EL5780FE603440', 1985, 'Cadillac', 'Eldorado', 'Base', 'Pewter', 'Tan', 7995, null, 5996, 32880, 'UNSPECIFIED', '2013-01-14', null],
    ['1G8JD54R05Y503397', 2005, 'Saturn', 'L300', 'Base', 'Silver', 'Grey', 8888, null, 6666, 35751, '4-Speed Automatic', '2013-01-14', null],
    ['2HKYF18567H525598', 2007, 'Honda', 'Pilot', 'EX-L', 'Aberdeen Green Metallic', 'Gray', 14500, null, 10875, 86200, '5-Speed Automatic', '2013-01-14', null],
    ['3A4FY48B27T554499', 2007, 'Chrysler', 'PT Cruiser', 'Base', 'Purple', 'Gray', 7995, null, 5996, 41981, 'Manual', '2013-01-14', null],
    ['3FAHP01159R137525', 2009, 'Ford', 'Fusion', 'SE', 'Vapor Silver', 'Camel', 9495, null, 7124, 107532, '6-Speed Automatic', '2013-01-14', null],
    ['3GNFK16T71G208523', 2001, 'Chevrolet', 'Suburban', 'LT', 'Black Onyx', 'Tan Neutral', 7995, null, 5996.25, 116305, '4-Speed Automatic', '2013-01-14', null],
    ['5FNRL38477B091190', 2007, 'Honda', 'Odyssey', 'EX', 'Slate Green Metallic', 'Gray', 10000, null, 7500, 99555, '5-Speed Automatic', '2013-01-14', null],
    ['5FNYF4H91CB052346', 2012, 'Honda', 'Pilot', 'Touring', 'White Diamond Pearl', 'Leather', 37807, null, 34250, 7076, 'Automatic', '2012-11-08', null],
    ['5J6YH28307L014151', 2007, 'Honda', 'Element', 'LX', 'Silver', 'Gray', 8995, null, 6746, 111000, 'Automatic', '2013-01-14', null],
    ['JHLRD77802C069548', 2002, 'Honda', 'CR-V', 'EX', 'Blue', 'Black', 6995, null, 5246, 105139, '5-Speed Manual', '2013-01-14', null],
    ['JTLKE50E481060621', 2008, 'Scion', 'xB', 'Base', 'Black Sand Pearl', 'Dark Gray', 9999, null, 7499, 65146, 'Automatic', '2013-01-14', null],
    ['KMHDU46DX7U184501', 2007, 'Hyundai', 'Elantra', 'SE', 'Seattle Blue', 'Gray', 999, null, 7497, 41560, '5-Speed Manual', '2013-01-14', null],
    ['KNAGE228795310609', 2009, 'Kia', 'Optima', 'Base', 'Ruby Red', 'Beige', 10000, null, 7500, 56749, '5-Speed Automatic', '2013-01-14', null],
    ['LAKSDFJ234ZASKRF2', 2009, 'Dodge', 'Durango', 'SLT', 'Silver', 'Black', 2700, null, 2000, 144000, '4WD Automatic', '2012-12-05', null],
    ['WBAEU33434PR12965', 2004, 'BMW', '325', 'xi', 'Silver', 'Black', 9650, null, 7237.50, 98208, 'Automatic', '2013-01-14', null],
    ['WDBJF65J71B215984', 2001, 'Mercedes-Benz', 'E320', 'Base', 'Silver', 'Gray', 9900, null, 7425, 40902, '5-Speed Automatic', '2013-01-14', null],
    ['WDBRF87HX6F757914', 2006, 'Mercedes-Benz', 'C350 4Matic', 'Luxury', 'Black', 'Black', 14450, null, 10837.50, 82000, '5-Speed Automatic', '2013-01-14', null],
    ['WMEEK31X79K226939', 2021, 'Toyota', 'Tacoma', 'TRD Racing Sport', 'Silver', 'Black', 34995, null, 6741, 23272, '5-Speed Automatic with Auto-Shift', '2013-01-14', null],
    ['WMWRC334X4TJ61214', 2004, 'MINI', 'Cooper', 'Base', 'Jet Black', 'Black', 9499, null, 7124.25, 59160, 'Automatic', '2013-01-14', null],
    ['YS3DF75K627015298', 2002, 'Saab', '9-3', 'SE', 'Sand', 'Tan', 8495, null, 6371, 48499, '5-speed Manual', '2013-01-14', null],
    ['YV1MJ682152063198', 2005, 'Volvo', 'V50', 'T5', 'Blue', 'Black', 8995, null, 6746, 110354, 'Automatic', '2013-01-14', null],
    ['YV4SZ592561219696', 2006, 'Volvo', 'XC70', 'AWD', 'Willow Green Metallic', 'Taupe Leather', 14996, null, 11247, 83664, '5-Speed Automatic w/ Geartronic', '2013-01-14', null]
];

// Prepare the insert statement.
$insert = $mysqli->prepare(
    "INSERT IGNORE INTO inventory
    (VIN, `YEAR`, Make, Model, TRIM, EXT_COLOR, INT_COLOR,
    ASKING_PRICE, SALE_PRICE, PURCHASE_PRICE, MILEAGE,
    TRANSMISSION, PURCHASE_DATE, SALE_DATE)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$insert) {
    die("<p>Prepare failed: "
        . htmlspecialchars($mysqli->error) . "</p>");
}

// Correct type string: 14 parameters.
$inserted = 0;

foreach ($cars as $car) {
    [
        $vin, $year, $make, $model, $trim, $extColor, $intColor,
        $askingPrice, $salePrice, $purchasePrice, $mileage,
        $transmission, $purchaseDate, $saleDate
    ] = $car;

    $insert->bind_param(
        "sisssssdddisss",
        $vin, $year, $make, $model, $trim, $extColor, $intColor,
        $askingPrice, $salePrice, $purchasePrice, $mileage,
        $transmission, $purchaseDate, $saleDate
    );

    if ($insert->execute()) {
        $inserted += $insert->affected_rows;
    } else {
        echo "<p>Error inserting vehicle: "
            . htmlspecialchars($insert->error) . "</p>";
    }
}

$insert->close();

echo "<h2>Database Setup Complete</h2>";
echo "<p>$inserted new vehicles added.</p>";
echo "<p>Existing vehicles were left unchanged.</p>";

$mysqli->close();

echo "<p><a href='ViewCars.php'>View Complete Inventory</a></p>";
?>

