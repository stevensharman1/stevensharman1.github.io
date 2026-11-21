
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Variables</title>
</head>

<body>
    <h1>Variables</h1>

    <?php
        $brand = "Ford";
        $model = "Explorer";
        $year = 2024;

        echo "This $brand $model is a $year.<br>";

        $gas_price = 3.89;

        echo "The price of gas is $$gas_price.";

        echo "<br><br><a href='index.php'>Home</a>";
    ?>
</body>
</html>
