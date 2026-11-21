
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Age Calculator</title>
</head>
<body>

<?php
$myBirthday = "1965-01-21";

// Calculate age from the birthday.
function age($birthday) {
    $birthDate = new DateTime($birthday);
    $today = new DateTime();

    return $birthDate->diff($today)->y;
}

echo age($myBirthday);
?>

</body>
</html>
