
<?php
/**
 * Sam's Used Cars
 * API that returns all inventory records as JSON.
 */

// Set the response format to JSON.
header('Content-Type: application/json; charset=utf-8');

// Prevent database warnings from corrupting JSON output.
mysqli_report(MYSQLI_REPORT_OFF);

try {

    // Connect to the database.
    require_once 'db.php';

    // Get all vehicles, ordered by Make.
    $query = "SELECT * FROM inventory ORDER BY Make";

    $result = $mysqli->query($query);

    if (!$result) {
        http_response_code(500);

        echo json_encode([
            "error" => "Error retrieving cars: " . $mysqli->error
        ]);

        $mysqli->close();
        exit();
    }

    // Store vehicle records.
    $cars = [];

    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }

    // Close database resources.
    $result->free();
    $mysqli->close();

    // Return valid JSON.
    echo json_encode($cars);

} catch (Throwable $error) {

    http_response_code(500);

    echo json_encode([
        "error" => "API error: " . $error->getMessage()
    ]);
}
?>
