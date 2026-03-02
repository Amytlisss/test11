<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=Неверный ID');
    exit;
}

$id = (int)$_GET['id'];

try {
    $sql = "UPDATE employees SET fired = FALSE, fired_date = NULL WHERE id = :id AND fired = TRUE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        header('Location: index.php?message=Сотрудник восстановлен');
    } else {
        header('Location: index.php?error=Сотрудник не найден или уже работает');
    }
    
} catch (PDOException $e) {
    header('Location: index.php?error=Ошибка при восстановлении');
}
exit;
?>