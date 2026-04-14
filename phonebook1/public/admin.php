<?php
include '../src/auth.php';
include '../config/db.php';

requireLogin();

if (!isAdmin()) {
    header("Location: no_access.php");
    exit;
}

if (isset($_POST['create_user'])) {
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];
    $can_search = isset($_POST['can_search']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO users (login, password, role_id, can_search) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $login, $password, $role_id, $can_search);
    $stmt->execute();

    echo "<p style='color:green;'>Пользователь создан</p>";
}

if (isset($_GET['toggle_search'])) {
    $id = (int)$_GET['toggle_search'];

    $user = $conn->query("SELECT can_search FROM users WHERE id = $id")->fetch_assoc();
    $action = $user['can_search'] ? 'Запрет поиска' : 'Разрешение поиска';

    $conn->query("UPDATE users SET can_search = NOT can_search WHERE id = $id");
    $conn->query("INSERT INTO block_history (user_id, action) VALUES ($id, '$action')");
}

if (isset($_GET['make_admin'])) {
    $id = (int)$_GET['make_admin'];
    $conn->query("UPDATE users SET role_id = 1 WHERE id = $id");
}

if (isset($_GET['make_user'])) {
    $id = (int)$_GET['make_user'];
    $conn->query("UPDATE users SET role_id = 2 WHERE id = $id");
}

$users = $conn->query("SELECT * FROM users");

$history = $conn->query("SELECT bh.*, u.login FROM block_history bh LEFT JOIN users u ON bh.user_id = u.id ORDER BY bh.created_at DESC");

?>

<?php include '../templates/header.php'; ?>

<h2>Админ-панель</h2>

<h3>Создать нового пользователя</h3>
<form method="POST" class="admin-form">
    <input type="text" name="login" placeholder="Логин" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <select name="role_id">
        <option value="2">Сотрудник</option>
        <option value="1">Админ</option>
    </select>
    <label class="checkbox-label">
        <input type="checkbox" name="can_search" checked>
        Разрешить поиск
    </label>
    <button type="submit" name="create_user">Создать</button>
</form>

<h3>Список пользователей</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Логин</th>
        <th>Роль</th>
        <th>Поиск</th>
        <th>Действия</th>
    </tr>

    <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['login'] ?></td>
            <td><?= $user['role_id'] == 1 ? 'Админ' : 'Сотрудник' ?></td>
            <td><?= $user['can_search'] ? 'Разрешён' : 'Запрещён' ?></td>
            <td>
                <div class="actions">
    <a class="btn-action btn-toggle" href="?toggle_search=<?= $user['id'] ?>">
        <?= $user['can_search'] ? 'Запретить поиск' : 'Разрешить поиск' ?>
    </a>

    <?php if ($user['role_id'] == 2): ?>
        <a class="btn-action btn-up" href="?make_admin=<?= $user['id'] ?>">
            Сделать админом
        </a>
    <?php else: ?>
        <a class="btn-action btn-down" href="?make_user=<?= $user['id'] ?>">
            Сделать сотрудником
        </a>
    <?php endif; ?>
</div>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>История блокировок</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Пользователь</th>
        <th>Действие</th>
        <th>Дата</th>
    </tr>

    <?php while ($row = $history->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['login'] ?></td>
            <td><?= $row['action'] ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../templates/footer.php'; ?>