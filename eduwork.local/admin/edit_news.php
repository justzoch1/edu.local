<?php
session_start();
define('ROOT_DIR', dirname(__DIR__));
include ROOT_DIR . '/config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['news_id'])) {
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$_POST['news_id']]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_news'])) {
    $stmt = $conn->prepare("UPDATE news SET name = ?, description = ?, full_description = ? WHERE id = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['full_description'],
        $_POST['news_id']
    ]);
    header('Location: admin_panel.php');
}
?>

<?php include ROOT_DIR . '/includes/header.php'; ?>

<h2>Редактировать студента</h2>
<form method="post">
    <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
    <div class="form-group">
        <label for="last_name">Название:</label>
        <input type="text" class="form-control" name="name" value="<?php echo $news['name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="first_name">Описание:</label>
        <input type="text" class="form-control" name="description" value="<?php echo $news['description']; ?>" required>
    </div>
    <div class="form-group">
        <label for="gender">Подробное описание:</label>
        <input type="text" class="form-control" name="full_description" value="<?php echo $news['full_description']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary" name="update_news">Сохранить изменения</button>
</form>
