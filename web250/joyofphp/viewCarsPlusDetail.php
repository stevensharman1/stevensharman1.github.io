
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sam's Used Cars - Complete Inventory</title>
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>

<body>

<h1>Sam's Used Cars</h1>
<h3>Complete Inventory</h3>

<?php
// Get vehicle data from the API.
$apiUrl = 'cars_list_api.php';
$response = file_get_contents($apiUrl);

if ($response === false) {
    die("<p>Error fetching car data from the API.</p>");
}

// Convert JSON into a PHP array.
$cars = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("<p>Error reading vehicle data: " .
        htmlspecialchars(json_last_error_msg()) . "</p>");
}

if (isset($cars['error'])) {
    die("<p>Error: " .
        htmlspecialchars($cars['error']) . "</p>");
}

if (!is_array($cars)) {
    die("<p>Invalid vehicle data received.</p>");
}

// Display the inventory table.
echo "<table id='Grid' style='width: 80%; cursor: pointer;'>";
echo "<tr>";
echo "<th>Make</th>";
echo "<th>Model</th>";
echo "<th>Asking Price</th>";
echo "</tr>";

$class = "odd";

if (!empty($cars)) {

    foreach ($cars as $car) {

        $vin = htmlspecialchars(
            $car['VIN'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        );

        echo "<tr class='$class' onclick=\"showCarDetails('" .
             $vin . "')\">";

        echo "<td>" .
             htmlspecialchars($car['Make'] ?? '') .
             "</td>";

        echo "<td>" .
             htmlspecialchars($car['Model'] ?? '') .
             "</td>";

        echo "<td>$" .
             htmlspecialchars((string)($car['ASKING_PRICE'] ?? '')) .
             "</td>";

        echo "</tr>";

        $class = ($class == "odd") ? "even" : "odd";
    }

} else {
    echo "<tr><td colspan='3'>No vehicles found.</td></tr>";
}

echo "</table>";
?>

<!-- Modal -->
<div id="carModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Car Details</h2>
        <div id="carDetails"></div>
    </div>
</div>

<script>
// Display the selected vehicle's details.
function showCarDetails(vin) {
    const modal = document.getElementById('carModal');
    const carDetailsDiv = document.getElementById('carDetails');

    carDetailsDiv.textContent = 'Loading vehicle details...';
    modal.style.display = 'block';

    fetch('car_details_api.php?VIN=' + encodeURIComponent(vin))
        .then(response => {
            if (!response.ok) {
                throw new Error('Unable to retrieve vehicle details.');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                carDetailsDiv.textContent = data.error;
                return;
            }

            // Build the details safely.
            carDetailsDiv.textContent = '';

            const details = [
                ['Vehicle', (data.year || '') + ' ' +
                    (data.make || '') + ' ' + (data.model || '')],
                ['Trim', data.trim],
                ['Price', data.price ? '$' + data.price : ''],
                ['Exterior Color', data.exterior_color],
                ['Interior Color', data.interior_color],
                ['Mileage', data.mileage ? data.mileage + ' miles' : ''],
                ['Transmission', data.transmission]
            ];

            details.forEach(([label, value]) => {
                const paragraph = document.createElement('p');
                const strong = document.createElement('strong');

                strong.textContent = label + ': ';
                paragraph.appendChild(strong);
                paragraph.appendChild(
                    document.createTextNode(value || 'Not available')
                );

                carDetailsDiv.appendChild(paragraph);
            });
        })
        .catch(error => {
            carDetailsDiv.textContent =
                'Error fetching car details. Please try again.';
        });
}

// Close the modal.
function closeModal() {
    document.getElementById('carModal').style.display = 'none';
}

// Close the modal when clicking outside it.
window.onclick = function(event) {
    const modal = document.getElementById('carModal');

    if (event.target === modal) {
        closeModal();
    }
};
</script>

<p><a href="index.php">Home</a></p>

</body>
</html>
