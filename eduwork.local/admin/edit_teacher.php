<?php
session_start();
define('ROOT_DIR', dirname(__DIR__));
include ROOT_DIR . '/config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['teacher_id'])) {
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->execute([$_POST['teacher_id']]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_teacher'])) {
    $stmt = $conn->prepare("UPDATE teachers SET last_name = ?, first_name = ?, middle_name = ?, birth_date = ?, gender = ?, address = ?, phone = ?, email = ?, employment_date = ?, position = ? WHERE id = ?");
    $stmt->execute([
        $_POST['last_name'],
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['address'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['employment_date'],
        $_POST['position'],
        $_POST['teacher_id']
    ]);
    header('Location: admin_panel.php');
}
?>

<?php include ROOT_DIR . '/includes/header.php'; ?>

<h2>Редактировать преподавателя</h2>
<form method="post">
    <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">
    <div class="form-group">
        <label for="last_name">Фамилия:</label>
        <input type="text" class="form-control" name="last_name" value="<?php echo $teacher['last_name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="first_name">Имя:</label>
        <input type="text" class="form-control" name="first_name" value="<?php echo $teacher['first_name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="middle_name">Отчество:</label>
        <input type="text" class="form-control" name="middle_name" value="<?php echo $teacher['middle_name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="birth_date">Дата рождения:</label>
        <input type="date" class="form-control" name="birth_date" value="<?php echo $teacher['birth_date']; ?>" required>
    </div>
    <div class="form-group">
        <label for="gender">Пол:</label>
        <input type="text" class="form-control" name="gender" value="<?php echo $teacher['gender']; ?>" required>
    </div>
    <div class="form-group">
        <label for="address">Адрес:</label>
        <input type="text" class="form-control" name="address" value="<?php echo $teacher['address']; ?>" required>
    </div>
    <div class="form-group">
        <label for="phone">Телефон:</label>
        <input type="text" class="form-control" name="phone" value="<?php echo $teacher['phone']; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Почта:</label>
        <input type="email" class="form-control" name="email" value="<?php echo $teacher['email']; ?>" required>
    </div>
    <div class="form-group">
        <label for="enrollment_date">Дата трудоустройства:</label>
        <input type="text" class="form-control" name="employment_date" value="<?php echo $teacher['employment_date']; ?>" required>
    </div>
    <div class="form-group">
        <label for="graduation_date">Должность:</label>
        <input type="text" class="form-control" name="position" value="<?php echo $teacher['position']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary" name="update_teacher">Сохранить изменения</button>
</form>
