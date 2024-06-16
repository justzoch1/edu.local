<?php
session_start();
define('ROOT_DIR', dirname(__DIR__));
include ROOT_DIR . '/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header('Location: ../admin/admin_panel.php');
        } else {
            header('Location: ../index.php');
        }
        exit();
    } else {
        $error = "Неверное имя пользователя или пароль";
    }
}
?>

<?php include ROOT_DIR . '/includes/header.php'; ?>

<h2>Авторизация</h2>
<p>Пожалуйста, введите ваше имя пользователя и пароль, чтобы войти в систему.</p>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<form action="" method="post">
    <div class="form-group">
        <label for="username">Пользователь:</label>
        <input type="text" class="form-control" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" class="form-control" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Войти</button>
</form>

<div class="text-center mt-4">
    <p>Данные пользователя для администратора: admin</p>
    <p>Данные пользователя для преподавателя: teacher</p>
    <p>Данные пароля для администратора: admin</p>
    <p>Данные пароля для администратора: teacher</p>
</div>

<?php include ROOT_DIR . '/includes/footer.php'; ?>
