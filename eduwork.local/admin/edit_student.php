<?php
session_start();
define('ROOT_DIR', dirname(__DIR__));
include ROOT_DIR . '/config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'])) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_POST['student_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $stmt = $conn->prepare("UPDATE students SET last_name = ?, first_name = ?, middle_name = ?, birth_date = ?, gender = ?, address = ?, phone = ?, email = ?, group_number = ?, enrollment_date = ?, graduation_date = ? WHERE id = ?");
    $stmt->execute([
        $_POST['last_name'],
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['address'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['group_number'],
        $_POST['enrollment_date'],
        $_POST['graduation_date'],
        $_POST['student_id']
    ]);
    header('Location: admin_panel.php');
}
?>

<?php include ROOT_DIR . '/includes/header.php'; ?>

<h2>Редактировать студента</h2>
<form method="post">
    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
    <div class="form-group">
        <label for="last_name">Фамилия:</label>
        <input type="text" class="form-control" name="last_name" value="<?php echo $student['last_name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="first_name">Имя:</label>
        <input type="text" class="form-control" name="first_name" value="<?php echo $student['first_name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="middle_name">Отчество:</label>
        <input type="text" class="form-control" name="middle_name" value="<?php echo $student['middle_name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="birth_date">Дата рождения:</label>
        <input type="date" class="form-control" name="birth_date" value="<?php echo $student['birth_date']; ?>" required>
    </div>
    <div class="form-group">
        <label for="gender">Пол:</label>
        <input type="text" class="form-control" name="gender" value="<?php echo $student['gender']; ?>" required>
    </div>
    <div class="form-group">
        <label for="address">Адрес:</label>
        <input type="text" class="form-control" name="address" value="<?php echo $student['address']; ?>" required>
    </div>
    <div class="form-group">
        <label for="phone">Телефон:</label>
        <input type="text" class="form-control" name="phone" value="<?php echo $student['phone']; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Почта:</label>
        <input type="email" class="form-control" name="email" value="<?php echo $student['email']; ?>" required>
    </div>
    <div class="form-group">
        <label for="group_number">Номер группы:</label>
        <input type="text" class="form-control" name="group_number" value="<?php echo $student['group_number']; ?>" required>
    </div>
    <div class="form-group">
        <label for="enrollment_date">Дата поступления:</label>
        <input type="date" class="form-control" name="enrollment_date" value="<?php echo $student['enrollment_date']; ?>" required>
    </div>
    <div class="form-group">
        <label for="graduation_date">Дата окончания:</label>
        <input type="date" class="form-control" name="graduation_date" value="<?php echo $student['graduation_date']; ?>" required>
    </div>
    <!-- <div class="form-group">
        <label for="role">Роль:</label>
        <select class="form-control" name="role">
            <option value="student" <?php if (isset($student['role']) && $student['role'] == 'student') echo 'selected'; ?>>Студент</option>
            <option value="teacher" <?php if (isset($student['role']) && $student['role'] == 'teacher') echo 'selected'; ?>>Преподаватель</option>
        </select>
    </div> -->
    <button type="submit" class="btn btn-primary" name="update_student">Сохранить изменения</button>
</form>
