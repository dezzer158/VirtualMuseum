<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $model_path = $_POST['model_path'];
    $room_id = $_POST['room_name'];
    $position_x = $_POST['position_x'];
    $position_y = $_POST['position_y'];
    $position_z = $_POST['position_z'];
    $scale_x = $_POST['scale_x'];
    $scale_y = $_POST['scale_y'];
    $scale_z = $_POST['scale_z'];

    if ($mode === 'add') {
        $stmt = $mysqli->prepare("INSERT INTO exhibits (name, description, model_path, room_id, position_x, position_y, position_z, scale_x, scale_y, scale_z) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssiddiddd', $name, $description, $model_path, $room_id, $position_x, $position_y, $position_z, $scale_x, $scale_y, $scale_z);
    } elseif ($mode === 'edit') {
        $exhibit_id = $_POST['exhibit_id'];
        $stmt = $mysqli->prepare("UPDATE exhibits SET name=?, description=?, model_path=?, room_id=?, position_x=?, position_y=?, position_z=?, scale_x=?, scale_y=?, scale_z=? WHERE id=?");
        $stmt->bind_param('sssiddidddi', $name, $description, $model_path, $room_id, $position_x, $position_y, $position_z, $scale_x, $scale_y, $scale_z, $exhibit_id);
    }

    if ($stmt->execute()) {
        header('Location: index.php');
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$mysqli->close();
?>
