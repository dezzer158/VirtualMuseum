<?php
require '../config.php';

$mode = 'add';  // 'add' или 'edit'
$exhibit_id = null;
$exhibit = null;
$name = '';
$description = '';
$model_path = '';
$room_id = '';
$position_x = 0;
$position_y = 0; 
$position_z = 0;
$scale_x = 1.0;
$scale_y = 1.0; 
$scale_z = 1.0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['exhibit_id'])) {
        // Режим редактирования
        $mode = 'edit';
        $exhibit_id = $_POST['exhibit_id'];
        $result = $mysqli->query("SELECT * FROM exhibits WHERE id=$exhibit_id");
        $exhibit = $result->fetch_assoc();

        $name = $exhibit['name'];
        $description = $exhibit['description'];
        $model_path = $exhibit['model_path'];
        $room_id = $exhibit['room_id'];
        $position_x = $exhibit['position_x'];
        $position_y = $exhibit['position_y'];
        $position_z = $exhibit['position_z'];
        $scale_x = $exhibit['scale_x'];
        $scale_y = $exhibit['scale_y'];
        $scale_z = $exhibit['scale_z'];
    } else {
        // Режим добавления
        $name = $_POST['name'];
        $description = $_POST['description'];
        $model = $_FILES['model'];
        
        if ($model['error'] !== UPLOAD_ERR_OK) {
            die("Ошибка загрузки файла: " . $model['error']);
        }

        $target_dir = "../uploads/exhibits/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $target_file = $target_dir . basename($model["name"]);
        if (!move_uploaded_file($model["tmp_name"], $target_file)) {
            die("не удалось переместить загруженный файл.");
        }

        $model_path = basename($target_file);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $mode === 'edit' ? 'Edit' : 'Add'; ?> Редактирование экспоната</title>
    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <style>
        #admin-scene {
            width: 100%;
            height: 600px;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1><?php echo $mode === 'edit' ? 'Редактирование' : 'Добавление'; ?>  экспоната</h1>
    <form id="finalize-exhibit-form" action="add_exhibit.php" method="post">
        <input type="hidden" name="mode" value="<?php echo $mode; ?>">
        <?php if ($mode === 'edit'): ?>
            <input type="hidden" name="exhibit_id" value="<?php echo $exhibit_id; ?>">
        <?php endif; ?>
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input type="hidden" name="description" value="<?php echo htmlspecialchars($description); ?>">
        <input type="hidden" name="model_path" value="<?php echo htmlspecialchars($model_path); ?>">
        
        <label for="room_name">Помещение:</label>
        <select id="room_name" name="room_name" required>
            <?php
            $rooms = $mysqli->query("SELECT id, name FROM rooms");
            while ($room = $rooms->fetch_assoc()) {
                $selected = $room['id'] == $room_id ? 'selected' : '';
                echo "<option value='{$room['id']}' $selected>{$room['name']}</option>";
            }
            ?>
        </select><br><br>
        
        <label for="position_x">Положение X:</label>
        <input type="number" id="position_x" name="position_x" step="0.1" value="<?php echo $position_x; ?>" required><br><br>
        
        <label for="position_y">Положение Y:</label>
        <input type="number" id="position_y" name="position_y" step="0.1" value="<?php echo $position_y; ?>" required><br><br>
        
        <label for="position_z">Положение Z:</label>
        <input type="number" id="position_z" name="position_z" step="0.1" value="<?php echo $position_z; ?>" required><br><br>
        
        <label for="scale_x">Размер X:</label>
        <input type="number" id="scale_x" name="scale_x" step="0.1" value="<?php echo $scale_x; ?>" required><br><br>
        
        <label for="scale_y">Размер Y:</label>
        <input type="number" id="scale_y" name="scale_y" step="0.1" value="<?php echo $scale_y; ?>" required><br><br>
        
        <label for="scale_z">Размер Z:</label>
        <input type="number" id="scale_z" name="scale_z" step="0.1" value="<?php echo $scale_z; ?>" required><br><br>
        
        <input type="submit" value="<?php echo $mode === 'edit' ? 'Сохранить' : 'Добавить экспонат'; ?>">
    </form>

    <h2>Предпросмотр</h2>
    <a-scene id="admin-scene" embedded>
        <a-entity camera look-controls position="0 20 0" rotation="-90 0 0"></a-entity>
        <a-light type="ambient" color="#888"></a-light>
        <a-light type="directional" color="#fff" position="1 1 1"></a-light>

        <?php
        $rooms = $mysqli->query("SELECT * FROM rooms");
        while ($room = $rooms->fetch_assoc()) {
            echo "<a-entity position='0 0 0' scale='{$room['scale_x']} {$room['scale_y']} {$room['scale_z']}'>";
            echo "<a-asset-item id='room-{$room['id']}' src='../uploads/rooms/{$room['model_path']}'></a-asset-item>";
            echo "<a-entity gltf-model='#room-{$room['id']}' position='0 0 0'></a-entity>";
            echo "</a-entity>";

            $exhibits = $mysqli->query("SELECT * FROM exhibits WHERE room_id={$room['id']}");
            while ($exhibit = $exhibits->fetch_assoc()) {
                echo "<a-entity id='exhibit-{$exhibit['id']}' position='{$exhibit['position_x']} {$exhibit['position_y']} {$exhibit['position_z']}' scale='{$exhibit['scale_x']} {$exhibit['scale_y']} {$exhibit['scale_z']}'>";
                echo "<a-asset-item id='exhibit-model-{$exhibit['id']}' src='../uploads/exhibits/{$exhibit['model_path']}'></a-asset-item>";
                echo "<a-entity gltf-model='#exhibit-model-{$exhibit['id']}' position='0 0 0'></a-entity>";
                echo "</a-entity>";
            }
        }
        ?>

        <!-- Preview of the new or edited exhibit -->
        <a-entity id="preview-exhibit" position="<?php echo $position_x; ?> <?php echo $position_y; ?> <?php echo $position_z; ?>" scale="<?php echo $scale_x; ?> <?php echo $scale_y; ?> <?php echo $scale_z; ?>">
            <a-asset-item id="preview-exhibit-model" src="../uploads/exhibits/<?php echo htmlspecialchars($model_path); ?>"></a-asset-item>
            <a-entity gltf-model="#preview-exhibit-model" position="0 0 0"></a-entity>
        </a-entity>
    </a-scene>

    <script>
        document.getElementById('position_x').addEventListener('input', updatePreviewExhibit);
        document.getElementById('position_y').addEventListener('input', updatePreviewExhibit);
        document.getElementById('position_z').addEventListener('input', updatePreviewExhibit);
        document.getElementById('scale_x').addEventListener('input', updatePreviewExhibit);
        document.getElementById('scale_y').addEventListener('input', updatePreviewExhibit);
        document.getElementById('scale_z').addEventListener('input', updatePreviewExhibit);

        function updatePreviewExhibit() {
            const positionX = document.getElementById('position_x').value;
            const positionY = document.getElementById('position_y').value;
            const positionZ = document.getElementById('position_z').value;
            const scaleX = document.getElementById('scale_x').value;
            const scaleY = document.getElementById('scale_y').value;
            const scaleZ = document.getElementById('scale_z').value;

            const previewExhibit = document.getElementById('preview-exhibit');
            previewExhibit.setAttribute('position', `${positionX} ${positionY} ${positionZ}`);
            previewExhibit.setAttribute('scale', `${scaleX} ${scaleY} ${scaleZ}`);
        }
    </script>
</body>
</html>

