<?php
session_start();
include '../config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $fileId = $_POST['file_id'];
    
    $stmt = $conn->prepare("SELECT file_path FROM uploads WHERE id = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        if (unlink($file['file_path'])) {
            $stmt = $conn->prepare("DELETE FROM uploads WHERE id = ?");
            $stmt->execute([$fileId]);
            header('Location: documents.php');
            exit();
        } else {
            echo "Ошибка при удалении файла.";
        }
    } else {
        echo "Файл не найден.";
    }
}
?>
