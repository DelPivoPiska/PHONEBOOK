<?php
include '../src/auth.php';
include '../config/db.php';
?>

<?php include '../templates/header.php'; ?>

<h2>О нашей организации</h2>
<p>
    Добро пожаловать в корпоративный телефонный справочник. 
    Здесь вы можете найти контактную информацию сотрудников, 
    узнать структуру организации и подразделения.
</p>

<h2>Отделы</h2>

<?php
include '../config/db.php';

$sql = "SELECT d.id, d.name, d.description, 
               e.last_name, e.first_name 
        FROM departments d
        LEFT JOIN employees e ON d.id = e.department_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>Отдел</th>
            <th>Описание</th>
            <th>Руководитель</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['description']}</td>
                <td>{$row['last_name']} {$row['first_name']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>Нет данных</p>";
}
?>

<?php include '../templates/footer.php'; ?>