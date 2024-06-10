<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['exhibit_file'])) {
    $uploadDir = '../uploads/exhibits/';
    $uploadFile = $uploadDir . basename($_FILES['exhibit_file']['name']);
    $fileExtension = pathinfo($uploadFile, PATHINFO_EXTENSION);

}

$rooms = $mysqli->query("SELECT * FROM rooms");
$exhibits = $mysqli->query("SELECT * FROM exhibits");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администрирования</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Панель администрирования</h1>
        </div>
    </header>
    <div class="container">
    <form action="add_exhibit_step1.php" method="get"> 
        <input type="submit" value="Добавить новый экспонат">
    </form>

        <h2>Список экспонатов</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Файл модели</th>
                <th>Помещение</th>
                <th>Позиция</th>
                <th>Размер</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../config.php';
            $result = $mysqli->query("SELECT exhibits.*, rooms.name as room_name FROM exhibits JOIN rooms ON exhibits.room_id = rooms.id");
            while ($exhibit = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$exhibit['id']}</td>";
                echo "<td>{$exhibit['name']}</td>";
                echo "<td>{$exhibit['description']}</td>";
                echo "<td>{$exhibit['model_path']}</td>";
                echo "<td>{$exhibit['room_name']}</td>";
                echo "<td>X: {$exhibit['position_x']}, Y: {$exhibit['position_y']}, Z: {$exhibit['position_z']}</td>";
                echo "<td>X: {$exhibit['scale_x']}, Y: {$exhibit['scale_y']}, Z: {$exhibit['scale_z']}</td>";
                echo "<td><form action='add_exhibit_step2.php' method='post' style='display:inline;'><input type='hidden' name='exhibit_id' value='{$exhibit['id']}'><input type='submit' value='Редактировать'></form></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

       </div>

</body>
</html>
