<?php
session_start();
include '../config.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    header('Location: ../content/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $uploadDir = '../uploads/';
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if ($_FILES['file']['size'] > 12 * 1024 * 1024) { //12MB
        echo "File size exceeds 12MB limit.";
    } else {
        $allowedTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
                $theme = implode(', ', $_POST['theme']);
                $stmt = $conn->prepare("INSERT INTO uploads (file_name, file_description, file_path, theme) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['file_name'], $_POST['file_description'], $targetFilePath, $theme]);
                echo "Файл успешно загружен.";
            } else {
                echo "Извините, произошла ошибка при загрузке вашего файла.";
            }
        } else {
            echo "К сожалению, разрешены только файлы в форматах JPG, JPEG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, PPT и PPTX.";
        }
    }
}
$theme = $_GET['docs'] ?? "";
$stmt = $conn->prepare("SELECT * FROM uploads WHERE theme = ?");
$stmt->execute(([$theme]));
$stmt->execute();
$uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h3 class="text-center">Документы</h3>

    <?php if ($_SESSION['role'] == 'admin'): ?>
    <div class="upload-form">
        <h4>Загрузить файл</h4>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file_name">Название файла:</label>
                <input type="text" class="form-control" name="file_name" required>
            </div>
            <div class="form-group">
                <label for="file_description">Описание файла:</label>
                <textarea class="form-control" name="file_description" required></textarea>
            </div>
            <div class="form-group">
            <select name="theme[]" class="form-control" id="theme">
                <option value="" selected disabled>Выберите тему документа</option>
                <option value="Журнал_по_воспитательной_работе">Журнал по воспитательной работе</option>
                <option value="Тематическое_планирование">Тематическое планирование "Разговоры о важном"</option>
                <option value="Педагогическая_характеристика">Педагогическая характеристика</option>
                <option value="Социальный_паспорт">Социальный паспорт</option>
                <option value="Активность_групп">Активность групп</option>
                <option value="Памятка_для_родителей">Памятка для родителей</option>
                <option value="Протокол_родительского_собрания">Протокол родительского собрания</option>
            </select>
            </div>
            <div class="form-group">
                <label for="file">Выберите файл:</label>
                <input type="file" class="form-control" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary" name="upload">Загрузить</button>


        </form>
    </div>
    <?php endif; ?>

    <h4 class="mt-5">Загруженные файлы</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Описание</th>
                <th>Файл</th>
                <th>Предпросмотр</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($uploads as $upload): ?>
                <tr>
                    <td><?php echo htmlspecialchars($upload['file_name']); ?></td>
                    <td><?php echo htmlspecialchars($upload['file_description']); ?></td>
                    <td>
                        <?php
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                        $fileExtension = pathinfo($upload['file_path'], PATHINFO_EXTENSION);
                        if (in_array($fileExtension, $imageExtensions)): ?>
                            <img src="<?php echo htmlspecialchars($upload['file_path']); ?>" alt="Preview" style="max-width: 100px;">
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($upload['file_path']); ?>" download>Скачать</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if (in_array($fileExtension, $imageExtensions)): ?>
                            <a href="<?php echo htmlspecialchars($upload['file_path']); ?>" target="_blank">Предпросмотр</a>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($upload['file_path']); ?>" download>Скачать</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="delete_file.php" method="post" style="display:inline-block;">
                            <input type="hidden" name="file_id" value="<?php echo $upload['id']; ?>">
                            <button type="submit" class="btn btn-danger" name="delete">Удалить</button>
                        </form>
                        <form action="download_file.php" method="post" style="display:inline-block;">
                            <input type="hidden" name="file_path" value="<?php echo $upload['file_path']; ?>">
                            <button type="submit" class="btn btn-primary" name="download">Скачать</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>