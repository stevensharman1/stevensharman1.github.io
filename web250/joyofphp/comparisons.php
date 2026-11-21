
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Practice</title>
</head>

<body>

<h1>Open Hours</h1>

<?php
// Set the default time zone.
date_default_timezone_set("America/New_York");

// Check whether the business is open today.
if (date("l") == "Sunday") {
    echo "Sorry, we are closed today.";
} else {
    echo "We are open today from 10 AM to 9 PM<br>";
}

// Demonstrate comparison operators.
echo "<h1>Comparison Operators</h1>";

$FirstName = "Alan";

if ($FirstName == "Alan") {
    echo "Hello Master";
} else {
    echo "Hello $FirstName";
}

echo "<br>";

$a = 1;
$b = "1";

// Compare values using ==.
if ($a == $b) {
    echo '$a is equal to $b';
} else {
    echo '$a is not equal to $b';
}

echo "<br><br>";

// Compare values and data types using ===.
if ($a === $b) {
    echo '$a is equal to $b';
} else {
    echo '$a is not equal to $b';
}

echo "<br>";

// Demonstrate a while loop.
$i = 1;

while ($i <= 10) {
    echo $i . " ";
    $i = $i + 1;
}
?>

</body>
</html>

?>
</body>	
</html>

