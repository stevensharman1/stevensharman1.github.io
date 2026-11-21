
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="favicon.ico">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"> 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

     <!-- Joy of PHP stylesheet -->
    <link rel="stylesheet" href="style.css">
    
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>

    <title>Welcome to the Joy of PHP</title>

    <style>
        body {
            padding-left: 30px;
            padding-right: 30px;
        }

        .auto-style1 {
            border: 1px solid;
            background-color: #C0C0C0;
        }

        .book-logo {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>

<p>
    <a href="https://www.amazon.com/Joy-PHP-Programming-Interactive-Applications-ebook/dp/B00BALXN70/">
        <img class="book-logo"
             src="joy_logo.jpg"
             alt="The Joy of PHP"
             width="809"
             height="87">
    </a>
</p>

<h2>Introduction</h2>

<p>
    The Joy of PHP book walks you through creating a PHP-powered web
    application for <strong><a href="samsusedcars.php">Sam's Used Cars</a></strong>.
    All the sample code in the book is conveniently created here.
    To get started, you'll need to make sure that you have PHP and
    MySQL running on your machine.
</p>

<div class="alert alert-info shadow rounded">

    <h4 class="alert-heading">Getting Started</h4>

    <p>
        Before you can do much of anything, you need to install PHP,
        Apache, and MySQL. The following packages do it automatically.
    </p>

    <p>
        Windows users:
        <a href="https://www.wampserver.com/en/">Download WAMP</a>
    </p>

    <p>
        Mac users:
        <a href="https://www.mamp.info/en/">Download MAMP</a>
        (Pro version not required)
    </p>

    <strong>Free Code Editors</strong>

    <p>
        <a href="https://code.visualstudio.com/">Visual Studio Code</a>
        is my recommended editor.
    </p>

    <p>
        <a href="http://www.pnotepad.org/">Programmer's Notepad</a>
        is a good all-purpose editor.
    </p>

    <p>
        <a href="http://devphp.sourceforge.net/">Dev-PHP</a>
        is a PHP-specific editor.
    </p>

    <p>
        <a href="https://zed.dev/">Zed</a>
        is an up-and-coming editor with a lot of promise.
    </p>

</div>

<h2>Following Along with the Lessons</h2>

<p>
    Is PHP running on my machine? What version? What extensions?
    Check it here:
    <a href="info.php" target="_blank">PHP Info Page</a>
</p>

<p>
    You can use this information from the PHP Info page to determine
    which version of XDEBUG to download by pasting that information
    into the <a href="https://xdebug.org/wizard">XDEBUG Wizard</a>.
</p>

<p>
    A really simple PHP page:
    <a href="simple.php">simple.php</a>
</p>

<p>
    The finished product:
    <a href="samsusedcars.php">Sam's Used Cars Home</a>
</p>

<h2>Database Related Scripts</h2>

<p>
    <a href="CreateDB.php">Create the Cars Database</a>
    (Database setup script)
</p>

<p>
    <a href="ModifyDB.php">Modify the Inventory Table</a>
    (Adds the Primary_Image column)
</p>

<p>
    <a href="CreateImagesTable.php">Create the Images Table</a>
</p>

<p>
    <a href="CreateUsersTable.php">Create the Users Table</a>
</p>

<p>
    <a href="https://www.amazon.com/Joy-PHP-Programming-Interactive-Applications-ebook/dp/B00BALXN70/">
        <img src="smallcover.jpg"
             alt="Joy of PHP book cover"
             width="196"
             height="313">
    </a>
</p>

<?php include 'footer.php'; ?>

</body>
</html>
