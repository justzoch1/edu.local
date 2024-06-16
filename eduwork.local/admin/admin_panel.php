<?php
session_start();
define('ROOT_DIR', dirname(__DIR__));
include ROOT_DIR . '/config.php';

try {
    if (!$conn) {
        throw new Exception("Failed to connect to database.");
    }

    if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher')) {
        header('Location: ../content/login.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
        try {
        $stmt = $conn->prepare("INSERT INTO students (last_name, first_name, middle_name, birth_date, gender, address, phone, email, group_number, enrollment_date, graduation_date, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            'student'
        ]);
        header('Location: admin_panel.php');
        exit();
    }catch(Throwable $ex)
    {
        echo "Ошибка при выполнении программы<br>";
    }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
        $stmt = $conn->prepare("INSERT INTO teachers (last_name, first_name, middle_name, birth_date, gender, address, phone, email, position, employment_date role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['last_name'],
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['birth_date'],
            $_POST['gender'],
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['position'],
            $_POST['employment_date'],
            'teacher'
        ]);
        header('Location: admin_panel.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_student'])) {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$_POST['student_id']]);
        header('Location: admin_panel.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_teacher'])) {
        $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$_POST['teacher_id']]);
        header('Location: admin_panel.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_news'])) {
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$_POST['news_id']]);
        header('Location: admin_panel.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_group'])) {
        $stmt = $conn->prepare("DELETE FROM groups WHERE id = ?");
        $stmt->execute([$_POST['group_id']]);
        header('Location: admin_panel.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_group'])) {
        $stmt = $conn->prepare("INSERT INTO groups (number_group, number_students, enrollment_year, graduation_year, classroom_teacher, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['number_group'],
            $_POST['number_students'],
            $_POST['enrollemnt_year'],
            $_POST['graduation_year'],
            $_POST['classroom_teacher'],
            'groupe'
        ]);
        header('Location: admin_panel.php');
        exit();
    }

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_news'])) {
        $stmt = $conn->prepare("INSERT INTO news (name, description, full_description) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['full_description'],
        ]);
        header('Location: admin_panel.php');
        exit();
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<?php include ROOT_DIR . '/includes/header.php'; ?>

<h2>Панель администратора</h2>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showContent('home')">Главная</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showContent('news')">Новости</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showContent('groups')">Группы</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" onclick="showContent('admin-content')">Списки</a>
    </li>
</ul>

<div id="home" class="container mt-4">
    <h3>Добро пожаловать в панель администратора</h3>
    <p>Здесь будет содержимое главной страницы.</p>
</div>

<div id="news" class="container mt-4" style="display: none;">
    <h3>Новости</h3>
    <div id="form">
    
    <button type="button" class="btn btn-primary" id="add-news-btn">Добавить новость</button>
    <form id="news_form" method="POST" style=" display: none;" >
        <div >
            <div class="form-group">
                <label for="first_name">Название:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Описание:</label>
                <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="form-group">
                <label for="birth_date">Подробное описание:</label>
                <input type="text" class="form-control" id="full_description" name="full_description" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_news">Добавить новость</button>
        </div>
    </form>

    <h3>Список новостей</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Подробное описание</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM news");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td>{$row['full_description']}</td>";
                echo "<td>";
                echo "<form method='post' style='display:inline-block;'>";
                echo "<input type='hidden' name='news_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-danger' name='delete_news'>Удалить</button>";
                echo "</form>";
                echo "<form method='post' action='edit_news.php' style='display:inline-block;'>";
                echo "<input type='hidden' name='news_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-warning' name='edit_news'>Редактировать</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<div id="groups" class="container mt-4" style="display: none;">
    <h3>Группы</h3>
    <div id="form">
    <button type="button" class="btn btn-primary" id="add-group-btn">Добавить группу</button>
    <form id="group_form" method="POST" style=" display: none;" >
        <div >
            <div class="form-group">
                <label for="last_name">Номер группы:</label>
                <input type="text" class="form-control" id="number_group" name="number_group" required>
            </div>
            <div class="form-group">
                <label for="first_name">Колличество студентов:</label>
                <input type="text" class="form-control" id="number_students" name="number_students" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Год поступления:</label>
                <input type="text" class="form-control" id="enrollemnt_year" name="enrollemnt_year" required>
            </div>
            <div class="form-group">
                <label for="birth_date">Год окончания:</label>
                <input type="text" class="form-control" id="graduation_year" name="graduation_year" required>
            </div>
            <div class="form-group">
                <label for="gender">Классный руководитель:</label>
                <input type="text" class="form-control" id="classroom_teacher" name="classroom_teacher" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_group">Добавить группу</button>
        </div>
    </form>

    <h3>Список групп</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Номер группы</th>
                <th>Количество студентов</th>
                <th>Год поступления</th>
                <th>Год окончания</th>
                <th>Классный руководитель</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM groups");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['number_group']}</td>";
                echo "<td>{$row['number_students']}</td>";
                echo "<td>{$row['enrollment_year']}</td>";
                echo "<td>{$row['graduation_year']}</td>";
                echo "<td>{$row['classroom_teacher']}</td>";
                echo "<td>";
                echo "<form method='post' style='display:inline-block;'>";
                echo "<input type='hidden' name='group_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-danger' name='delete_group'>Удалить</button>";
                echo "</form>";
                echo "<form method='post' action='edit_group.php' style='display:inline-block;'>";
                echo "<input type='hidden' name='group_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-warning' name='edit_group'>Редактировать</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<div id="admin-content" class="container mt-4" style="display: none;">
    <h3>Добавить участника</h3>
        <div class="form-group">
            <label for="user_type">Тип участника:</label>
            <select class="form-control" id="user_type" onchange="showUserForm()">
                <option value="" selected disabled>Выберите тип участника</option>
                <option value="student">Студент</option>
                <option value="teacher">Преподаватель</option>
            </select>
        </div>

        <form method="POST" >
        <div id="student_form" style="display: none;">
            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" class="form-control" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Отчество:</label>
                <input type="text" class="form-control" name="middle_name" required>
            </div>
            <div class="form-group">
                <label for="birth_date">Дата рождения:</label>
                <input type="date" class="form-control" name="birth_date" required>
            </div>
            <div class="form-group">
                <label for="gender">Пол:</label>
                <input type="text" class="form-control" name="gender" required>
            </div>
            <div class="form-group">
                <label for="address">Адрес:</label>
                <input type="text" class="form-control" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" class="form-control" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Почта:</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label for="group_number">Номер группы:</label>
                <input type="text" class="form-control" name="group_number" required>
            </div>
            <div class="form-group">
                <label for="enrollment_date">Дата поступления:</label>
                <input type="date" class="form-control" name="enrollment_date" required>
            </div>
            <div class="form-group">
                <label for="graduation_date">Дата окончания:</label>
                <input type="date" class="form-control" name="graduation_date" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_student">Добавить студента</button>
        </div>
    </form>

    <form method="POST" >
        <div id="teacher_form" style="display: none;">
            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Отчество:</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" required>
            </div>
            <div class="form-group">
                <label for="birth_date">Дата рождения:</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
            </div>
            <div class="form-group">
                <label for="gender">Пол:</label>
                <input type="text" class="form-control" id="gender" name="gender" required>
            </div>
            <div class="form-group">
                <label for="address">Адрес:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="email">Почта:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="employment_date">Дата трудоустройства:</label>
                <input type="date" class="form-control" id="employment_date" name="employment_date" required>
            </div>
            <div class="form-group">
                <label for="position">Должность</label>
                <input type="text" class="form-control" id="position" name="position" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_teacher">Добавить преподавателя</button>
        </div>
    </form>

<div id="form">
    <h3>Список студентов</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Дата рождения</th>
                <th>Пол</th>
                <th>Адрес</th>
                <th>Телефон</th>
                <th>Почта</th>
                <th>Номер группы</th>
                <th>Дата поступления</th>
                <th>Дата окончания</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM students");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['last_name']}</td>";
                echo "<td>{$row['first_name']}</td>";
                echo "<td>{$row['middle_name']}</td>";
                echo "<td>{$row['birth_date']}</td>";
                echo "<td>{$row['gender']}</td>";
                echo "<td>{$row['address']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['group_number']}</td>";
                echo "<td>{$row['enrollment_date']}</td>";
                echo "<td>{$row['graduation_date']}</td>";
                echo "<td>";
                echo "<form method='post' style='display:inline-block;'>";
                echo "<input type='hidden' name='student_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-danger' name='delete_student'>Удалить</button>";
                echo "</form>";
                echo "<form method='post' action='edit_student.php' style='display:inline-block;'>";
                echo "<input type='hidden' name='student_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-warning' name='edit_student'>Редактировать</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="teacher_form">
<h3>Список преподавателей</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Дата рождения</th>
                <th>Пол</th>
                <th>Адрес</th>
                <th>Телефон</th>
                <th>Почта</th>
                <th>Дата трудоустройства</th>
                <th>Должность</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM teachers");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['last_name']}</td>";
                echo "<td>{$row['first_name']}</td>";
                echo "<td>{$row['middle_name']}</td>";
                echo "<td>{$row['birth_date']}</td>";
                echo "<td>{$row['gender']}</td>";
                echo "<td>{$row['address']}</td>";
                echo "<td>{$row['phone']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['employment_date']}</td>";
                echo "<td>{$row['position']}</td>";
                echo "<td>";
                echo "<form method='post' style='display:inline-block;'>";
                echo "<input type='hidden' name='teacher_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-danger' name='delete_teacher'>Удалить</button>";
                echo "</form>";
                echo "<form method='post' action='edit_teacher.php' style='display:inline-block;'>";
                echo "<input type='hidden' name='teacher_id' value='{$row['id']}'>";
                echo "<button type='submit' class='btn btn-warning' name='edit_teacher'>Редактировать</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</div>
<script>
function showContent(section) {
    var sections = ['home', 'news', 'admin-content', 'groups'];
    sections.forEach(function(sec) {
        document.getElementById(sec).style.display = (sec === section) ? 'block' : 'none';
    });
}

document.getElementById("add-group-btn").addEventListener("click", function() {
  document.getElementById("group_form").style.display = "block";
  this.style.display = "none";
});

document.getElementById("add-news-btn").addEventListener("click", function() {
  document.getElementById("news_form").style.display = "block";
  this.style.display = "none";
});

function showUserForm() {
    var userType = document.getElementById('user_type').value;
    if (userType === 'student') {
        document.getElementById('student_form').style.display = 'block';
        document.getElementById('teacher_form').style.display = 'none';
        document.getElementById('add_user').style.display = 'block';
        hideUsersExceptRole('student');
    } else if (userType === 'teacher') {
        document.getElementById('student_form').style.display = 'none';
        document.getElementById('teacher_form').style.display = 'block';
        document.getElementById('add_user').style.display = 'block';
        hideUsersExceptRole('teacher');
    } else {
        document.getElementById('student_form').style.display = 'none';
        document.getElementById('teacher_form').style.display = 'none';
        document.getElementById('add_user').style.display = 'none';
        showAllUsers();
    }
}

function hideUsersExceptRole(role) {
    var tableRows = document.querySelectorAll('.user-row');
    tableRows.forEach(function(row) {
        var userRole = row.getAttribute('data-role');
        if (userRole !== role) {
            row.style.display = 'none';
        } else {
            row.style.display = '';
        }
    });
}

function showAllUsers() {
    var tableRows = document.querySelectorAll('.user-row');
    tableRows.forEach(function(row) {
        row.style.display = ''; 
    });
}

document.addEventListener('DOMContentLoaded', function() {
    showContent('home');
    resetUserForm();
});

function resetUserForm() {
    document.getElementById('user_type').selectedIndex = 0;
    document.getElementById('student_form').style.display = 'none';
    document.getElementById('teacher_form').style.display = 'none';
    document.getElementById('add_user').style.display = 'none';
}

</script>