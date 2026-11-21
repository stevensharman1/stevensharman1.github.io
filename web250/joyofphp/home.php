
<?php
// Start the user session.
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Used Cars - Home</title>

    <style>
        .center {
            text-align: center;
        }

        .header-table {
            width: 100%;
        }

        .header-table img {
            max-width: 184px;
            height: auto;
        }
    </style>
</head>

<body>

<table class="header-table">
    <tr>
        <td style="width: 133px">
            <img src="usedcars.jpg" alt="Sam's Used Cars">
        </td>

        <td>
            <h1 class="center">Welcome to Sam's Used Car Lot</h1>

            <p class="center">
                The companion website that goes with the
            </p>

            <p class="center">
                <a href="index.php">Joy of PHP</a>:
                <em>Deep Dive Into Sessions</em> Book
            </p>
        </td>
    </tr>
</table>

<hr>

<?php
// Display the appropriate welcome message.
if (isset($_SESSION['MyUserName'])) {

    echo "<p>Welcome " .
         htmlspecialchars($_SESSION['MyUserName']) . "</p>";

    echo "<p>Your role is " .
         htmlspecialchars($_SESSION['Role']) . "</p>";

} else {

    echo "<p>Welcome to Sam's Used Car Lot</p>";
}
?>

<h2>Vehicle Inventory</h2>

<p><a href="ViewCars.php">View Cars - Basic</a></p>

<p><a href="ViewCarsWithStyle.php">View Cars with Style</a></p>

<?php
// Display navigation based on the user's role.
if (isset($_SESSION['MyUserName'])) {

    if ($_SESSION['Role'] == "Owner") {

        echo "<h3>Owner Actions</h3>";

        echo "<p><a href='ViewSalesReports.php'>View Sales Report</a></p>";

        echo "<p><a href='ViewPendingSales.php'>Approve Pending Sales</a></p>";

        echo "<p><a href='ViewCarsWithStyle2.php'>View Cars with Edit Links</a></p>";
    }

    if ($_SESSION['Role'] == "Data Entry") {

        echo "<h3>Data Entry Actions</h3>";

        echo "<p><a href='formEnterCar.php'>Add a Car</a></p>";

        echo "<p><a href='ViewCarsAddImage.php'>Add Images to Cars</a></p>";
    }

    echo "<p><a href='logout.php'>Logout</a></p>";

} else {

    echo "<p><a href='login2.php'>Login</a></p>";
}
?>

<?php include 'footer.php'; ?>

</body>
</html>
