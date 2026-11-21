
<?php
/**
 * Sam's Used Cars
 * API that returns all inventory records as JSON.
 */

// Set the response format to JSON.
header('Content-Type: application/json; charset=utf-8');

// Connect to the database.
include 'db.php';

// Get all vehicles, ordered by Make.
$query = "SELECT * FROM inventory ORDER BY Make";

$cars = [];

$result = $mysqli->query($query);

if ($result) {

    // Add each vehicle to the cars array.
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }

    $result->free();

    // Close the database connection.
    $mysqli->close();

    // Return the vehicle information.
    echo json_encode($cars);

} else {

    // Return a JSON error if the query fails.
    http_response_code(500);

    echo json_encode([
        "error" => "Error retrieving cars from the database."
    ]);

    $mysqli->close();
}
?>
