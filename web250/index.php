<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'introduction':
        $title = 'Introduction';
        $content = 'contents/introduction.php';
        break;

    case 'contract':
        $title = 'Contract';
        $content = 'contents/contract.php';
        break;

    case 'about':
        $title = 'About';
        $content = 'contents/about.php';
        break;

    default:
        $title = 'Home';
        $content = 'contents/home.php';
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steven Sharman’s Sneaky Snake ✦ web250 ✦ <?php echo $title; ?></title>

    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

<header>
    <h1>Steven Sharman’s Sneaky Snake ✦ WEB250</h1>

    <nav>
        <a href="index.php">Home</a> |
        <a href="index.php?page=introduction">Introduction</a> |
        <a href="index.php?page=contract">Contract</a> |
        <a href="index.php?page=about">About</a> |
        <a href="joyofphp/index.php">Sam's Used Cars</a> |
        <a href="joyofphp/">Joy of PHPs</a>
    </nav>
</header>

<main>
    <?php include($content); ?>
</main>

<footer>
    <p>Page built by The Sneakiest Snake Around</p>
</footer>

</body>
</html>
