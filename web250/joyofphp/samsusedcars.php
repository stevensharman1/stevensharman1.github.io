
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Sam's Used Cars</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
        }

        .header img {
            max-width: 184px;
            height: auto;
        }

        .header-text {
            flex: 1;
            text-align: center;
        }

        .content {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            margin-top: 15px;
        }

        .book-cover {
            display: block;
            max-width: 196px;
            height: auto;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="usedcars.jpg" alt="Used Cars">

        <div class="header-text">
            <h1>Welcome to Sam's Used Cars</h1>
            <p>
                The companion website that goes with the
                <a href="index.php">Joy of PHP</a> book.
            </p>
        </div>
    </div>

    <hr>

    <div class="content">
        <h2>Welcome to Sam's Used Car Lot!</h2>

        <p><a href="formEnterCar.php">Add a Car</a></p>

        <p><a href="ViewCars.php">View Cars - Basic</a></p>

        <p><a href="ViewCarsWithStyle.php">View Cars with Style</a></p>

        <p><a href="ViewCarsWithStyle2.php">View Cars with Edit Links</a></p>

        <p><a href="viewCarsPlusDetail.php">View Cars with Modal Popup (uses API)</a></p>

        <p><a href="ViewCarsAddImage.php">Add Images to Cars</a></p>

        <p><a href="login2.php">Log In</a></p>

        <p>
            <a href="index.php">Return to Joy of PHP Home</a>
        </p>

        <img class="book-cover" src="smallcover.jpg" alt="Joy of PHP book cover">
    </div>

</body>
</html>
