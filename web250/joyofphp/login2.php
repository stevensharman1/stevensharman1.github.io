
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in to Sam's Used Cars</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 40px;
        }

        .login-container {
            background-color: white;
            width: 320px;
            margin: 60px auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            font-size: 24px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #365f91;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>Sam's Used Cars</h1>
    <h2>Log In</h2>

    <form action="post_login.php" method="POST">
        <label for="myusername">Username:</label>
        <input type="text" id="myusername" name="myusername" required>

        <label for="mypassword">Password:</label>
        <input type="password" id="mypassword" name="mypassword" required>

        <button type="submit">Log In</button>
    </form>

    <!-- Instructor demo login information -->
<div class="demo-info">
    <h3>Instructor Demo Login</h3>
    <p>Username: Sam</p>
    <p>Password: pw123</p>
</div>
    
    <p><a href="index.php">Home</a></p>
</div>

</body>
</html>

