<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Музей ВТ в виртуальной реальности</title>
    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aframe-extras@6.1.1/dist/aframe-extras.min.js"></script>
</head>
<body>
    <a-scene>

        <a-entity id="player" position="0 1.6 0" movement-controls="enabled: true">
            <a-camera wasd-controls="enabled: true"></a-camera>
        </a-entity>

        <?php
        require '../config.php';

        $rooms = $mysqli->query("SELECT * FROM rooms");

        while ($room = $rooms->fetch_assoc()) {
            echo "<a-entity gltf-model='url(../uploads/rooms/{$room['model_path']})' position='0 0 0' scale='{$room['scale_x']} {$room['scale_y']} {$room['scale_z']}'></a-entity>";
            echo "<a-text value='{$room['name']}' position='0 3 -5' align='center'></a-text>";
            echo "<a-text value='{$room['description']}' position='0 2.5 -5' align='center'></a-text>";

            $exhibits = $mysqli->query("SELECT * FROM exhibits WHERE room_id = {$room['id']}");

			while ($exhibit = $exhibits->fetch_assoc()) {
                echo "<a-entity position='{$exhibit['position_x']} {$exhibit['position_y']} {$exhibit['position_z']}' scale='{$exhibit['scale_x']} {$exhibit['scale_y']} {$exhibit['scale_z']}'>";
                echo "<a-asset-item id='exhibit-{$exhibit['id']}' src='../uploads/exhibits/{$exhibit['model_path']}'></a-asset-item>";
                echo "<a-entity gltf-model='#exhibit-{$exhibit['id']}' position='0 0 0'></a-entity>";
                echo "</a-entity>";

             
                $text_position = $exhibit['position_y'] + 2.5;  
                echo "<a-text value='{$exhibit['name']}' position='{$exhibit['position_x']} {$text_position} {$exhibit['position_z']}' align='center' color='#FFF' width='5'></a-text>";
                echo "<a-text value='{$exhibit['description']}' position='{$exhibit['position_x']} " . ($text_position - 0.5) . " {$exhibit['position_z']}' align='center' color='#FFF' width='5'></a-text>";
            }
        }


        echo "<a-entity position='-5 1.6 0' geometry='primitive: box; width: 0.1; height: 3; depth: 10' material='opacity: 0; transparent: true'></a-entity>"; 
        echo "<a-entity position='5 1.6 0' geometry='primitive: box; width: 0.1; height: 3; depth: 10' material='opacity: 0; transparent: true'></a-entity>"; 
        echo "<a-entity position='0 1.6 -5' geometry='primitive: box; width: 10; height: 3; depth: 0.1' material='opacity: 0; transparent: true'></a-entity>"; 
        echo "<a-entity position='0 1.6 5' geometry='primitive: box; width: 10; height: 3; depth: 0.1' material='opacity: 0; transparent: true'></a-entity>"; 

        $mysqli->close();
        ?>
    </a-scene>
</body>
</html>
