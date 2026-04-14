<?php
include '../src/auth.php';
include '../config/db.php';

requireLogin();

if (!canSearch()) {
    header("Location: no_access.php");
exit;
}

$result = null;

if (isset($_GET['query'])) {
    $query = "%" . $_GET['query'] . "%";

    $sql = "SELECT e.*, d.name as department_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE e.last_name LIKE ?
               OR e.first_name LIKE ?
               OR e.middle_name LIKE ?
               OR d.name LIKE ?
               OR e.position LIKE ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $query, $query, $query, $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<?php include '../templates/header.php'; ?>

<h2>Поиск сотрудников</h2>

<form method="GET">
    <input type="text" name="query" placeholder="ФИО, отдел, должность">
    <button type="submit">Найти</button>
</form>

<?php if ($result): ?>
    <table>
        <tr>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Отдел</th>
            <th>Должность</th>
            <th>Кабинет</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?= $row['last_name'] . ' ' . $row['first_name'] . ' ' . $row['middle_name'] ?>
                </td>
                <td><?= $row['phone'] ?></td>
                <td><?= $row['department_name'] ?></td>
                <td><?= $row['position'] ?></td>
                <td><?= $row['office'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<?php include '../templates/footer.php'; ?>