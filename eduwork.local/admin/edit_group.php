<?php
session_start();
define('ROOT_DIR', dirname(__DIR__));
include ROOT_DIR . '/config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_id'])) {
    $stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
    $stmt->execute([$_POST['group_id']]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_group'])) {
    $stmt = $conn->prepare("UPDATE groups SET number_group = ?, number_students = ?, enrollment_year = ?, graduation_year = ?, classroom_teacher = ? WHERE id = ?");
    $stmt->execute([
        $_POST['number_group'],
        $_POST['number_students'],
        $_POST['enrollment_year'],
        $_POST['graduation_year'],
        $_POST['classroom_teacher'],
        $_POST['group_id']
    ]);
    header('Location: admin_panel.php');
}
?>

<?php include ROOT_DIR . '/includes/header.php'; ?>

<h2>Редактировать студента</h2>
<form method="post">
    <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
    <div class="form-group">
        <label for="last_name">Номер группы:</label>
        <input type="text" class="form-control" name="number_group" value="<?php echo $group['number_group']; ?>" required>
    </div>
    <div class="form-group">
        <label for="first_name">Колличевство студентов:</label>
        <input type="text" class="form-control" name="number_students" value="<?php echo $group['number_students']; ?>" required>
    </div>
    <div class="form-group">
        <label for="gender">Классный руководитель:</label>
        <input type="text" class="form-control" name="classroom_teacher" value="<?php echo $group['classroom_teacher']; ?>" required>
    </div>
    <div>
        <label for="enrollment_date">Год поступления:</label>
        <input type="text" class="form-control" name="enrollment_year" value="<?php echo $group['enrollment_year']; ?>" required>
    </div>
    <div class="form-group">
        <label for="graduation_date">Год окончания:</label>
        <input type="text" class="form-control" name="graduation_year" value="<?php echo $group['graduation_year']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary" name="update_group">Сохранить изменения</button>
</form>
