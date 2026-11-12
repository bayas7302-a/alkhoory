<!DOCTYPE html>
<html lang="en-US" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($site['title']) ?></title>
    <meta name="description" content="<?= e($site['description']) ?>">
    <link rel="icon" href="<?= e($site['favicon']) ?>">
    <link rel="apple-touch-icon" href="<?= e($site['favicon']) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800;900&family=Raleway:wght@400&display=swap">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="./" title="<?= e($site['name']) ?>">
        <img src="<?= e($site['logo']) ?>" width="365" height="150" alt="<?= e($site['name']) ?>">
    </a>
</header>
