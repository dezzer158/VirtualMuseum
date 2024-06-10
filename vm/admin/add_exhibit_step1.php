<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавление экспоната. Шаг 1</title>
    <script>
        function validateForm(event) {
            const fileInput = document.getElementById('model');
            const filePath = fileInput.value;
            const allowedExtensions = /(\.glb)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Можно загрузить только файл в формате GLB.');
                fileInput.value = '';
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h1>Добавление экспоната. Шаг 1</h1>
    <form action="add_exhibit_step2.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm(event);">
        <label for="name">Название экспоната:</label>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="description">Описание:</label>
        <textarea id="description" name="description" required></textarea><br><br>
        
        <label for="model">Файл модели (только GLB):</label>
        <input type="file" id="model" name="model" accept=".glb" required><br><br>
        
        <input type="submit" value="Далее">
    </form>
</body>
</html>
