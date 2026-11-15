
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

    <title>Steven Sharman’s Sneaky Snake ✦ WEB250 ✦ <?= htmlspecialchars($title) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Main stylesheet -->
    <link rel="stylesheet" href="styles/default.css">

    <!-- Favicon -->
    <link rel="icon" href="images/favicon.svg" type="image/svg+xml">

    <!-- Accumulus -->
    <script src="https://lint.page/kit/4d0fe3.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- Shared header -->
    <?php include 'components/header.php'; ?>

    <main>
        <?php include $content; ?>
    </main>

    <!-- Shared footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Vicunadator -->
    <script src="https://divonbriesen.github.io/teaching/tools/standards-check.js" defer></script>

</body>
</html>
