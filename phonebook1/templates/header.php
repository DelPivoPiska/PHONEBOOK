<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Телефонный справочник</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <h1>Телефонный справочник</h1>
    <nav>
    <a href="index.php">Главная</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="search.php">Поиск</a>

        <?php if ($_SESSION['role_id'] == 1): ?>
            <a href="admin.php">Админка</a>
        <?php endif; ?>

        <a href="logout.php">Выход</a>
    <?php else: ?>
        <a href="login.php">Вход</a>
    <?php endif; ?>
</nav>
</header>
<main>