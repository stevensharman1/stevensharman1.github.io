
<?php
// Set the response header to JSON.
header('Content-Type: application/json; charset=utf-8');

// Include the database connection.
include 'db.php';

// Check whether a VIN was provided.
$vin = trim($_GET['VIN'] ?? '');

if ($vin === '') {
    http_response_code(400);
    echo json_encode(["error" => "VIN parameter is required."]);
    $mysqli->close();
    exit;
}

// Prepare the database query.
$query = "SELECT * FROM inventory WHERE VIN = ?";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Could not prepare the database query."]);
    $mysqli->close();
    exit;
}

// Bind the VIN and execute the query.
$stmt->bind_param("s", $vin);
$stmt->execute();

$result = $stmt->get_result();

// Check whether the vehicle exists.
if ($car = $result->fetch_assoc()) {

    // Return the vehicle information as JSON.
    $response = [
        "year" => $car['YEAR'],
        "make" => $car['Make'],
        "model" => $car['Model'],
        "trim" => $car['TRIM'],
        "exterior_color" => $car['EXT_COLOR'],
        "interior_color" => $car['INT_COLOR'],
        "mileage" => $car['MILEAGE'],
        "transmission" => $car['TRANSMISSION'],
        "price" => $car['ASKING_PRICE']
    ];

    echo json_encode($response);

} else {
    http_response_code(404);
    echo json_encode(["error" => "Vehicle not found."]);
}

// Close database resources.
$result->free();
$stmt->close();
$mysqli->close();
?>

